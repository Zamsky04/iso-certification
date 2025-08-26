<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportServicesFromJson extends Command
{
    protected $signature   = 'services:import {path=services.json} {--fresh}';
    protected $description = 'Import services from a JSON file into database (upsert by external_id)';

    /** @var array<string,bool> slug yang sudah terpakai (DB + batch saat ini) */
    private array $slugPool = [];

    public function handle(): int
    {
        // --- resolve file ---
        $rel = $this->argument('path');
        $candidates = [
            storage_path("app/{$rel}"),
            base_path($rel),
            public_path($rel),
        ];
        $file = collect($candidates)->first(fn ($p) => is_file($p));
        if (!$file) {
            $this->error("File not found: {$rel}");
            return self::FAILURE;
        }

        // --- read & validate json ---
        $raw  = file_get_contents($file);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $this->error('JSON root must be an array');
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            Service::truncate();
            $this->info('Truncated table services.');
        }

        // 🔹 Preload slug yang sudah ada di DB ke pool agar tidak bentrok
        Service::query()->pluck('slug')->filter()->each(function($s){
            $this->slugPool[$s] = true;
        });

        $total   = count($data);
        $done    = 0;
        $errors  = 0;
        $buffer  = [];
        $chunkSz = 500;

        foreach ($data as $i => $row) {
            try {
                $svc = $this->mapRowToModel($row, $i);
                $buffer[] = $svc;

                if (count($buffer) >= $chunkSz) {
                    $this->saveChunk($buffer);
                    $done += count($buffer);
                    $this->line(".. committed {$done}/{$total}");
                    $buffer = [];
                }
            } catch (\Throwable $e) {
                $errors++;
                $this->warn("Row #".($i+1)." skipped: ".$e->getMessage());
            }
        }

        if ($buffer) {
            $this->saveChunk($buffer);
            $done += count($buffer);
        }

        $this->info("Imported OK: {$done} rows; Skipped: {$errors}; Total read: {$total}");
        return self::SUCCESS;
    }

    /**
     * Map 1 row JSON -> instance Service (new or existing).
     */
    protected function mapRowToModel(array $row, int $index): Service
    {
        $title = trim((string)($row['title'] ?? ''));
        if ($title === '') throw new \RuntimeException('title empty');

        // external_id stabil
        $ext = (string)($row['external_id'] ?? $row['id'] ?? '');
        if ($ext === '') $ext = 'hash:' . md5(json_encode($row));

        // Upsert by external_id
        $svc = Service::firstOrNew(['external_id' => $ext]);

        // Field utama
        $svc->title             = $title;
        $svc->category          = filled($row['category'] ?? null) ? (string)$row['category'] : null;
        $svc->description       = $row['description']       ?? null;
        $svc->short_description = $row['short_description'] ?? null;
        $svc->image_url         = $row['image_url']         ?? null;
        $svc->cta_text          = $row['cta_text']          ?? null;
        $svc->cta_url           = $row['cta_url']           ?? null;
        $svc->featured          = (bool)($row['featured'] ?? false);
        $svc->metadata          = is_array($row['metadata']     ?? null) ? $row['metadata']     : [];
        $svc->benefits          = is_array($row['benefits']     ?? null) ? $row['benefits']     : [];
        $svc->requirements      = is_array($row['requirements'] ?? null) ? $row['requirements'] : [];

        // 🔹 Slug: kalau record sudah ada & slug-nya ada, pertahankan (tidak diubah)
        //     Jika baru / slug kosong, generate unik via pool.
        if ($svc->exists && filled($svc->slug)) {
            $svc->slug = Str::limit($svc->slug, 191, '');
            // reservasi ke pool agar batch lain tidak pakai slug ini
            $this->slugPool[$svc->slug] = true;
        } else {
            $source = (string)($row['slug'] ?? $title);
            $svc->slug = $this->makeUniqueSlug($source, $svc->id);
        }

        return $svc;
    }

    /**
     * Simpan chunk model dengan transaksi tunggal.
     */
    protected function saveChunk(array $models): void
    {
        DB::transaction(function () use ($models) {
            foreach ($models as $m) $m->save();
        });
    }

    /**
     * Buat slug ≤191 dan unik, dicek ke DB **dan** ke pool in-memory.
     */
    protected function makeUniqueSlug(string $source, ?int $excludeId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') $base = 'service';
        $base = Str::limit($base, 191, '');

        $slug = $base;
        $i = 2;

        // loop sampai tidak ada di DB *dan* tidak ada di pool
        while (
            isset($this->slugPool[$slug]) ||
            Service::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $suffix = '-' . $i;
            $slug = Str::limit($base, 191 - strlen($suffix), '') . $suffix;
            $i++;
        }

        // reservasi slug supaya batch berikutnya tahu slug ini sudah dipakai
        $this->slugPool[$slug] = true;

        return $slug;
    }
}
