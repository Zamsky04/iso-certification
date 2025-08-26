<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportServicesFromJson extends Command
{
    protected $signature = 'services:import {path=services.json} {--fresh}';
    protected $description = 'Import services from a JSON file into database';

    public function handle(): int
    {
        $rel = $this->argument('path');
        $candidates = [
            storage_path("app/{$rel}"),
            base_path($rel),
            public_path($rel),
        ];
        $file = collect($candidates)->first(fn($p) => is_file($p));
        if (!$file) {
            $this->error("File not found: {$rel}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data)) {
            $this->error('JSON root must be an array');
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            Service::truncate();
            $this->info('Services table truncated.');
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $i => $row) {
                $title = trim((string)($row['title'] ?? ''));
                if ($title === '') continue;

                // external_id selalu ada
                $ext = (string)($row['id'] ?? '');
                if ($ext === '') {
                    $ext = 'hash:'.md5(json_encode($row));
                }

                // cari service berdasarkan external_id
                $svc = Service::firstOrNew(['external_id' => $ext]);

                // isi field
                $svc->title             = $title;
                $svc->category          = (string)($row['category'] ?? null);
                $svc->description       = $row['description'] ?? null;
                $svc->short_description = $row['short_description'] ?? null;
                $svc->image_url         = $row['image_url'] ?? null;
                $svc->cta_text          = $row['cta_text'] ?? null;
                $svc->cta_url           = $row['cta_url'] ?? null;
                $svc->featured          = (bool)($row['featured'] ?? false);
                $svc->metadata          = $row['metadata'] ?? [];
                $svc->benefits          = is_array($row['benefits'] ?? null) ? $row['benefits'] : [];
                $svc->requirements      = is_array($row['requirements'] ?? null) ? $row['requirements'] : [];

                // slug unik
                if (!$svc->slug) {
                    $base = Str::slug($title) ?: 'service-'.($i+1);
                    $slug = $base; $n = 2;
                    while (Service::where('slug', $slug)->where('id', '<>', $svc->id)->exists()) {
                        $slug = $base.'-'.$n++;
                    }
                    $svc->slug = $slug;
                }

                $svc->save();
            }
        });

        $this->info('Imported: '.count($data).' records');
        return self::SUCCESS;
    }
}
