<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    /**
     * Colonnes autorisées au remplissage.
     * On accepte aussi "featured" (du formulaire) et on le
     * mappe vers "is_featured" via un mutateur.
     */
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body',
        'author', 'author_id',
        'category', 'category_id',
        'rubric_id',                // + rubrique (pour compat éventuelle en mass-assign)
        'thumbnail',
        'is_featured', 'featured',  // "featured" sera converti vers "is_featured"
        'views', 'published_at',
        'tags', 'sources', 'previous_slugs',
    ];

    protected $casts = [
        'is_featured'    => 'boolean',
        'views'          => 'integer',
        'published_at'   => 'datetime',
        'tags'           => 'array',
        'sources'        => 'array',
        'previous_slugs' => 'array',
        'rubric_id'      => 'integer',
    ];

    // on expose aussi image_url et rubric_slug dans les réponses JSON
    protected $appends = ['thumbnail_url', 'image_url', 'url', 'rubric_slug'];

    /* ------------------------
     | Relations
     * ------------------------ */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rubric()
    {
        return $this->belongsTo(\App\Models\Rubric::class);
    }

    /* ------------------------
     | Accessors pratiques
     * ------------------------ */

    /**
     * URL publique complète de l’image (essaie plusieurs colonnes possibles).
     */
    public function getImageUrlAttribute(): ?string
    {
        $candidates = [
            $this->image ?? null,
            $this->cover ?? null,
            $this->thumbnail ?? null,
            $this->thumb ?? null,
            $this->banner ?? null,
        ];

        foreach ($candidates as $src) {
            if (!$src) continue;
            $src = trim((string) $src);

            // URL absolue
            if (preg_match('~^https?://~i', $src)) {
                return $src;
            }

            // Chemin local -> /storage/...
            $src = ltrim($src, '/');
            $src = preg_replace('~^(public/|storage/)~i', '', $src);
            return asset('storage/'.$src);
        }
        return null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) return null;
        if (Str::startsWith($this->thumbnail, ['http://','https://'])) {
            return $this->thumbnail;
        }
        return asset('storage/'.$this->thumbnail);
    }

    public function getUrlAttribute(): string
    {
        // Si tes routes d’article utilisent le slug : route('articles.show', $article)
        return route('articles.show', $this);
    }

    /**
     * Slug de la rubrique directement exposé dans le JSON.
     */
    public function getRubricSlugAttribute(): ?string
    {
        return optional($this->rubric)->slug;
    }

    /* ------------------------
     | Scopes
     * ------------------------ */
    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function scopeSearch(Builder $q, ?string $s): Builder
    {
        if (!$s) return $q;
        return $q->where(function ($x) use ($s) {
            $x->where('title', 'like', "%$s%")
              ->orWhere('excerpt', 'like', "%$s%")
              ->orWhere('body', 'like', "%$s%");
        });
    }

    /**
     * Filtrer par slug de rubrique (et rubrique active).
     */
    public function scopeInRubricSlug(Builder $q, ?string $slug): Builder
    {
        if (!$slug) return $q;
        return $q->whereHas('rubric', function (Builder $r) use ($slug) {
            $r->where('slug', $slug)->where('is_active', 1);
        });
    }

    /**
     * Filtrer par id de rubrique.
     */
    public function scopeInRubricId(Builder $q, $id): Builder
    {
        if (!$id) return $q;
        return $q->where('rubric_id', $id);
    }

    /* ------------------------
     | Mutateurs
     * ------------------------ */

    // Permet de recevoir "featured" depuis le formulaire et le stocker dans "is_featured"
    public function setFeaturedAttribute($value): void
    {
        $this->attributes['is_featured'] = filter_var($value, FILTER_VALIDATE_BOOL);
    }

    /* ------------------------
     | Slug auto, unicité + historique
     * ------------------------ */
    protected static function booted()
    {
        // Normalisations + génération du slug si vide
        static::saving(function (Article $a) {
            // Slug auto si vide
            if (blank($a->slug) && filled($a->title)) {
                $a->slug = Str::slug($a->title);
            }

            // Unicité du slug
            if (filled($a->slug)) {
                $a->slug = static::uniqueSlug($a, $a->slug);
            }

            // Normalise tags/sources lorsqu’ils arrivent en string
            if (is_string($a->tags) && $a->tags !== '') {
                $a->tags = collect(preg_split('/[,;]\s*/', $a->tags))
                    ->filter()->values()->all();
            }
            if (is_string($a->sources) && $a->sources !== '') {
                $a->sources = collect(preg_split('/[,;]\s*/', $a->sources))
                    ->filter()->values()->all();
            }
        });

        // Historique des slugs (pour redirections 301 éventuelles)
        static::updating(function (Article $a) {
            if ($a->isDirty('slug')) {
                $old = $a->getOriginal('slug');
                if ($old) {
                    $prev = collect($a->previous_slugs ?? []);
                    if (!$prev->contains($old)) {
                        $a->previous_slugs = $prev->push($old)->values()->all();
                    }
                }
            }
        });
    }

    protected static function uniqueSlug(Article $model, string $base): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $i = 2;

        // ignore l’enregistrement courant lors de l’update
        while (
            static::where('slug', $slug)
                ->when($model->exists, fn($q) => $q->where('id', '!=', $model->id))
                ->exists()
        ) {
            $slug = $original.'-'.$i;
            $i++;
        }

        return $slug;
    }

    /* ------------------------
     | Routing par slug
     * ------------------------ */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
