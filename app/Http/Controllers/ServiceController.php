<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceFilterRequest;
use App\Models\Service;
use App\Services\ServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ServiceController extends Controller
{
    /** Halaman utama (SEO + SSR page-1) */
    public function page(ServiceFilterRequest $req)
    {
        $perPage = (int)($req->validated()['per_page'] ?? 12);
        $q       = $req->validated()['q'] ?? '';
        $cat     = $req->validated()['category'] ?? 'all';
        $meta = collect($req->safe()->except(['q','category','page','per_page']))->toArray();


        $base = Service::query()
            ->when(true, fn($q2) => $q2->orderByDesc('featured')->orderBy('title'));

        $paginated = $base->clone()
            ->search($q)->category($cat)->metaFilters($meta)
            ->paginate($perPage)->withQueryString();

        // Facets untuk SSR awal (opsional, sisanya dari API)
        $categories = Service::query()->distinct()->pluck('category')->filter()->values();

        return view('certifications.index', [
            'initial'     => $paginated,   // SSR grid page-1 -> crawlable
            'categories'  => $categories,
            'title'       => 'ISO Certification Hub - Platform Sertifikasi ISO Terpercaya',
            'description' => 'Temukan dan dapatkan sertifikasi ISO sesuai kebutuhan bisnis Anda.',
        ]);
    }

    /** Facets untuk dropdown/filter */
     /** API: facets */
    public function facets(ServiceFilterRequest $req)
{
    $params  = $req->validated();
    $q       = $params['q'] ?? '';
    $cat     = $params['category'] ?? 'all';
    $meta    = collect($req->safe())->except(['q','category','page','per_page','_token'])->toArray();

    $base = Service::query()
        ->search($q)
        ->category($cat)
        ->metaFilters($meta);

    // Kategori unik berdasarkan hasil terfilter
    $cats = (clone $base)->distinct()->pluck('category')->filter()->values();

    // Semua facet metadata dari hasil terfilter
    $allFacets = $this->facetAllFrom($base);

    return response()->json([
        'categories'      => $cats,
        'metadata_facets' => $allFacets, // associative: key => [ [val,count], ... ]
    ]);
    }

    /**
     * Ambil SEMUA kunci metadata beserta daftar nilai + count,
     * dari query yang SUDAH terfilter (tanpa paginate).
     *
     * @return array<string, array<int, array{0:string,1:int}>>
     */
    private function facetAllFrom(\Illuminate\Database\Eloquent\Builder $q): array
    {
        // Ambil semua kolom metadata dari hasil terfilter
        $rows = (clone $q)->select('metadata')->get()->pluck('metadata')->filter();

        // Kumpulkan hitungan per key->value
        $counts = []; // [key][value] = int
        foreach ($rows as $meta) {
            if (!is_array($meta)) continue;
            foreach ($meta as $k => $v) {
                $vals = is_array($v) ? $v : [$v];
                foreach ($vals as $vv) {
                    $vv = (string)($vv ?? '');
                    if ($vv === '') continue;
                    $counts[$k][$vv] = ($counts[$k][$vv] ?? 0) + 1;
                }
            }
        }

        // Buang key yang variasinya < 2 (kurang berguna)
        foreach ($counts as $k => $map) {
            if (count($map) < 2) unset($counts[$k]);
        }

        // Urutkan kunci: prioritas dulu, lalu alfabet
        $priority = ['nama-akreditasi' => -2, 'jenis-iso' => -1];
        uksort($counts, function($a,$b) use ($priority){
            $pa = $priority[$a] ?? 0;
            $pb = $priority[$b] ?? 0;
            return $pa <=> $pb ?: strcasecmp($a, $b);
        });

        // Konversi ke bentuk [ [val,count], ... ] dan urutkan (count desc, alfabet)
        $out = [];
        foreach ($counts as $k => $map) {
            $pairs = [];
            foreach ($map as $val => $c) $pairs[] = [$val, $c];
            usort($pairs, fn($A,$B) => $B[1] <=> $A[1] ?: strcasecmp($A[0], $B[0]));
            $out[$k] = $pairs;
        }

        return $out;
    }

    /**
     * Hitung facet dari query terfilter.
     */
    private function facetCountsFrom(\Illuminate\Database\Eloquent\Builder $q, string $key)
    {
        return $q->clone()
            ->selectRaw("json_extract(metadata, '$.\"$key\"') as val")
            ->get()
            ->map(fn($r) => $r->val)
            ->flatMap(function ($v) {
                $v = json_decode($v, true);
                return is_array($v) ? $v : [$v];
            })
            ->filter(fn($v) => filled($v))
            ->map(fn($v) => (string)$v)
            ->countBy()
            ->sortDesc()
            ->keys()
            ->values();
    }

    /** API: list + filter + paginate */
    public function index(ServiceFilterRequest $req)
    {
        $params  = $req->validated();
        $q       = $params['q'] ?? '';
        $cat     = $params['category'] ?? 'all';
        $perPage = (int)($params['per_page'] ?? 12);

        $meta = collect($req->safe()->except(['q','category','page','per_page']))->toArray();


        $p = Service::query()
            ->search($q)
            ->category($cat)
            ->metaFilters($meta)
            ->orderByDesc('featured')
            ->orderBy('title')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => $p->items(),
            'meta' => [
                'total'     => $p->total(),
                'page'      => $p->currentPage(),
                'per_page'  => $p->perPage(),
                'last_page' => $p->lastPage(),
            ],
        ]);
    }

    /** Halaman detail SEO (slug) */
    public function show(Service $service) // Route Model Binding by slug (lihat routes)
    {
        return view('certifications.show', [
            'service'     => $service,
            'title'       => $service->title,
            'description' => $service->short_description ?: str($service->description)->limit(150),
        ]);
    }

    /** API: detail (opsional) */
    public function showJson(Service $service)
    {
        return response()->json($service);
    }

    private function facetCounts(string $key)
    {
        // ambil nilai unik + hit dari metadata->$key
        return Service::query()
            ->selectRaw("json_extract(metadata, '$.\"$key\"') as val")
            ->get()
            ->map(fn($r) => $r->val)
            ->flatMap(function ($v) {
                $v = json_decode($v, true);
                return is_array($v) ? $v : [$v];
            })
            ->filter(fn($v) => filled($v))
            ->map(fn($v) => (string)$v)
            ->countBy()                   // value => hit
            ->sortDesc()                  // by count
            ->keys()
            ->values();
    }
}
