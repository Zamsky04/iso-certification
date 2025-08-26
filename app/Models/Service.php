<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'external_id','title','slug','category','description','short_description',
        'image_url','cta_text','cta_url','featured','metadata','benefits','requirements',
    ];

    protected $casts = [
        'featured'     => 'boolean',
        'metadata'     => 'array',
        'benefits'     => 'array',
        'requirements' => 'array',
    ];

    // Buat slug otomatis saat set title
    public static function booted(): void
    {
        static::saving(function (self $m) {
            if (empty($m->slug) && !empty($m->title)) {
                $m->slug = Str::limit(Str::slug($m->title), 191, '');
            }
        });
    }

    /** Scope pencarian */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string)$term);
        if ($term === '') return $q;

        $driver = $q->getModel()->getConnection()->getDriverName();
        $len    = mb_strlen($term);

        if ($driver === 'mysql' && $len >= 4) {
            $q->selectRaw(
                "services.*, MATCH(title, short_description, description) AGAINST (? IN BOOLEAN MODE) AS _score",
                [$term]
            )->whereRaw(
                "MATCH(title, short_description, description) AGAINST (? IN BOOLEAN MODE)",
                [$term]
            )->orderByDesc('_score');
            return $q;
        }

        return $q->where(function($w) use ($term) {
            $like = '%'.$term.'%';
            $w->where('title', 'like', $like)
            ->orWhere('short_description', 'like', $like)
            ->orWhere('description', 'like', $like);
        });
    }



    /** Scope filter kategori */
    public function scopeCategory(Builder $q, ?string $category): Builder
    {
        $c = trim((string) $category);
        return $c === '' || strtolower($c) === 'all'
            ? $q
            : $q->where('category', $c);
    }


    /** Scope filter metadata dinamis: whereJsonContains("metadata->$key", $val) */
    public function scopeMetaFilters(Builder $q, array $meta): Builder
    {
        foreach ($meta as $k => $v) {
            if ($v === null) continue;
            $v = trim((string) $v);
            if ($v === '' || strtolower($v) === 'all') continue;

            // JSON path aman utk key dg tanda minus/dll: $."nama-akreditasi"
            $path = '$."' . str_replace('"', '\"', $k) . '"';

            $q->where(function (Builder $qq) use ($path, $v) {
                // 1) kalau nilai di metadata scalar → cocokkan equality
                $qq->whereRaw('JSON_EXTRACT(metadata, ?) = JSON_QUOTE(?)', [$path, $v])
                // 2) kalau nilai di metadata berupa array → cek contains
                ->orWhereRaw('JSON_CONTAINS(JSON_EXTRACT(metadata, ?), JSON_QUOTE(?))', [$path, $v]);
            });
        }
        return $q;
    }
}
