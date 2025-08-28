<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceFilterRequest;
use App\Models\Service;
use App\Services\ServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    private function extractMetaFromRequest(Request $req): array
    {
        $meta = [];
        $input = $req->query('meta', []); // Hanya ambil dari query parameter 'meta'

        if (is_array($input)) {
            foreach ($input as $key => $value) {
                // Pastikan key dan value adalah string dan tidak kosong
                if (is_string($key) && is_string($value)) {
                    $v = trim($value);
                    // Hanya proses jika nilainya valid dan bukan 'all'
                    if ($v !== '' && strtolower($v) !== 'all') {
                        $meta[$key] = $v;
                    }
                }
            }
        }

        return $meta;
    }
    /** Halaman utama (SEO + SSR page-1) */
    public function page(ServiceFilterRequest $req)
    {
        $perPage = (int)($req->validated()['per_page'] ?? 12);
        $q       = $req->validated()['q'] ?? '';
        $cat     = $req->validated()['category'] ?? 'all';
        $meta = $this->extractMetaFromRequest($req);


        $base = Service::query()
            ->when(true, fn($q2) => $q2->orderByDesc('featured')->orderBy('title'));

        $paginated = (clone $base)
            ->search($q)->category($cat)->metaFilters($meta)
            ->paginate($perPage)->withQueryString();

        // Facets untuk SSR awal (opsional, sisanya dari API)
        $categories = Service::query()->distinct()->pluck('category')->filter()->values();

    // Kartu kategori dinamis:
    $catCards = $this->buildCategoryCards();

        return view('certifications.index', [
            'initial'     => $paginated,   // SSR grid page-1 -> crawlable
            'categories'  => $categories,
            'catCards'    => $catCards,
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
    $meta = $this->extractMetaFromRequest($req);

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

        $meta = $this->extractMetaFromRequest($req);


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

    private function buildCategoryCards(): array
{
    // gunakan path "$.\"key-dengan-strip\"" untuk JSON_EXTRACT
    $sql = "
        SUM(CASE
              WHEN JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"jenis-iso\"')) IS NOT NULL
               AND JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"nama-akreditasi\"')) LIKE ?
            THEN 1 ELSE 0 END) AS iso_kan,

        SUM(CASE
              WHEN JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"jenis-iso\"')) IS NOT NULL
               AND JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"nama-akreditasi\"')) LIKE ?
            THEN 1 ELSE 0 END) AS iso_iaf,

        SUM(CASE
              WHEN JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"jenis-iso\"')) IS NOT NULL
               AND (
                    JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"nama-akreditasi\"')) LIKE ?
                 OR JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"nama-akreditasi\"')) LIKE ?
               )
            THEN 1 ELSE 0 END) AS iso_non_iaf,

        SUM(CASE
              WHEN JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"jenis-iso\"')) IS NOT NULL
               AND JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.\"nama-akreditasi\"')) LIKE ?
            THEN 1 ELSE 0 END) AS iso_non_acc,

        SUM(CASE
              WHEN (category LIKE ? OR category LIKE ?)
            THEN 1 ELSE 0 END) AS skk_bnsp
    ";

    $bindings = [
        '%KAN%',          // iso_kan
        '%IAF%',          // iso_iaf
        '%Non IAF%',      // iso_non_iaf (1)
        '%IDCAB%',        // iso_non_iaf (2)
        '%Non Akreditasi%', // iso_non_acc
        '%skk%', '%bnsp%',  // skk_bnsp
    ];

    $row = Service::query()->selectRaw($sql, $bindings)->first();

    $cards = [
        [
            'key'=>'iso-kan','icon'=>'fa-certificate','color'=>'blue',
            'title'=>'ISO Akreditasi KAN','desc'=>'Sertifikasi ISO diakui nasional oleh KAN',
            'count'=>(int)$row->iso_kan,
            'link'=>url('/sertifikasi').'?category=iso&nama-akreditasi=KAN#certifications',
        ],
        [
            'key'=>'iso-iaf','icon'=>'fa-globe','color'=>'green',
            'title'=>'ISO Akreditasi Internasional (IAF)','desc'=>'Diakui asosiasi internasional IAF',
            'count'=>(int)$row->iso_iaf,
            'link'=>url('/sertifikasi').'?category=iso&nama-akreditasi=IAF#certifications',
        ],
        [
            'key'=>'iso-non-iaf','icon'=>'fa-shield-alt','color'=>'orange',
            'title'=>'ISO Non-IAF / IDCAB','desc'=>'Akreditasi internasional non-IAF (IDCAB)',
            'count'=>(int)$row->iso_non_iaf,
            'link'=>url('/sertifikasi').'?category=iso&nama-akreditasi=Non%20IAF#certifications',
        ],
        [
            'key'=>'iso-non-acc','icon'=>'fa-lock','color'=>'slate',
            'title'=>'ISO Non Akreditasi','desc'=>'Sertifikasi ISO tanpa akreditasi resmi',
            'count'=>(int)$row->iso_non_acc,
            'link'=>url('/sertifikasi').'?category=iso&nama-akreditasi=Non%20Akreditasi#certifications',
        ],
        [
            'key'=>'skk-bnsp','icon'=>'fa-hard-hat','color'=>'red',
            'title'=>'SKK BNSP','desc'=>'Sertifikat Kompetensi Kerja (BNSP)',
            'count'=>(int)$row->skk_bnsp,
            'link'=>url('/sertifikasi').'?category=skk%20bnsp#certifications',
        ],
    ];

    usort($cards, fn($a,$b)=>$b['count'] <=> $a['count']);
    return array_slice($cards, 0, 4);
}

}
