<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ServiceCatalog
{
    protected array $all = [];

    public function __construct()
    {
        $this->all = $this->load();
    }

    /** Muat & cache JSON dengan invalidasi berbasis mtime file */
    protected function load(): array
    {
        $paths = [
            storage_path('app/services.json'),
            public_path('services.json'),
        ];
        $file = null;
        foreach ($paths as $p) {
            if (is_file($p)) { $file = $p; break; }
        }
        if (!$file) return [];

        $key = 'services.json:' . filemtime($file);

        return Cache::remember($key, now()->addMinutes(10), function () use ($file) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            return is_array($data) ? $data : [];
        });
    }

    /** Semua data mentah */
    public function all(): array
    {
        return $this->all;
    }

    /** Facets unik */
    public function facets(): array
    {
        $cats = [];
        $accs = [];
        foreach ($this->all as $it) {
            $c = trim((string)($it['category'] ?? ''));
            if ($c !== '') $cats[$c] = true;

            $a = trim((string)($it['metadata']['nama-akreditasi'] ?? ''));
            if ($a !== '') $accs[$a] = true;
        }
        return [
            'categories'    => array_values(array_keys($cats)),
            'accreditations'=> array_values(array_keys($accs)),
        ];
    }

    /** Filter & cari */
    public function filter(array $params): array
    {
        $q            = Str::lower(trim((string)($params['q'] ?? '')));
        $category     = trim((string)($params['category'] ?? ''));
        $accreditation= trim((string)($params['accreditation'] ?? ''));

        $out = array_filter($this->all, function ($it) use ($q, $category, $accreditation) {
            // category
            if ($category !== '' && Str::lower((string)($it['category'] ?? '')) !== Str::lower($category)) {
                return false;
            }
            // accreditation (metadata.nama-akreditasi)
            $ak = Str::lower((string)($it['metadata']['nama-akreditasi'] ?? ''));
            if ($accreditation !== '' && $ak !== Str::lower($accreditation)) {
                return false;
            }
            // search
            if ($q !== '') {
                $hay = Str::lower(implode(' ', [
                    $it['title'] ?? '',
                    $it['description'] ?? '',
                    $it['short_description'] ?? '',
                    Arr::get($it, 'metadata.jenis-iso', ''),
                    Arr::get($it, 'metadata.nama-akreditasi', ''),
                ]));
                if (!Str::contains($hay, $q)) return false;
            }
            return true;
        });

        // sort sederhana (featured dulu, lalu title asc)
        usort($out, function ($a, $b) {
            $fa = !empty($a['featured']);
            $fb = !empty($b['featured']);
            if ($fa !== $fb) return $fa ? -1 : 1;
            return strcasecmp($a['title'] ?? '', $b['title'] ?? '');
        });

        return array_values($out);
    }

    /** Detail by id (string-int kompatibel) */
    public function find(string $id): ?array
    {
        foreach ($this->all as $it) {
            if ((string)($it['id'] ?? '') === (string)$id) return $it;
        }
        return null;
    }
}
