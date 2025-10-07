<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category; // ✅ pour filtrer par catégorie (slug/id)
use App\Models\Rubric;   // ✅ pour filtrer par rubrique (slug/id)
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * GET /api/articles
     * Filtres:
     *  - rubric_id | rubric | rubric_slug | rubrique | section
     *  - category_id | category | category_slug
     *  - featured=1
     *  - search=... (titre/slug/extrait)
     * Tri:
     *  - sort=views | date (par défaut: date desc)
     */
    public function index(Request $request)
    {
        $query = Article::query()
            ->whereNotNull('published_at');

        /* ---------- Recherche plein-texte simple ---------- */
        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                  ->orWhere('slug', 'like', $like)
                  ->orWhere('excerpt', 'like', $like);
            });
        }

        /* ---------- Filtres Rubrique ---------- */
        $rubricId   = $request->integer('rubric_id');
        $rubricSlug = $request->query('rubric')
            ?? $request->query('rubric_slug')
            ?? $request->query('rubrique')
            ?? $request->query('section');

        if ($rubricId) {
            $query->where('rubric_id', $rubricId);
        } elseif ($rubricSlug) {
            $rubric = Rubric::where('slug', $rubricSlug)->first();
            // si slug inconnu -> renvoie vide
            $query->where('rubric_id', optional($rubric)->id ?? 0);
        }

        /* ---------- Filtres Catégorie ---------- */
        $categoryId   = $request->integer('category_id');
        $categorySlug = $request->query('category') ?? $request->query('category_slug');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } elseif ($categorySlug) {
            $cat = Category::where('slug', $categorySlug)->first();
            $query->where('category_id', optional($cat)->id ?? 0);
        }

        /* ---------- À la une ---------- */
        if ((int) $request->query('featured') === 1) {
            $query->where(function ($q) {
                // compat "is_featured" OU "featured" selon ton schéma
                $q->where('is_featured', true)
                  ->orWhere('featured', true);
            });
        }

        /* ---------- Tri ---------- */
        $sort = strtolower((string) $request->query('sort', 'date'));
        if ($sort === 'views') {
            $query->orderByDesc('views')->orderByDesc('published_at');
        } else { // 'date' (défaut)
            $query->orderByDesc('published_at');
        }

        /* ---------- Récupération ---------- */
        // On ne précise pas de liste de colonnes pour éviter les erreurs si certaines n'existent pas (featured vs is_featured, image/cover, etc.)
        $items = $query->limit(50)->get()
            ->map(function (Article $a) {
                // Accessor image_url à mettre dans le modèle Article (fourni précédemment)
                $img = method_exists($a, 'getImageUrlAttribute') ? $a->image_url : null;

                // compatibilité des colonnes "featured" vs "is_featured"
                $isFeatured = (bool) ($a->is_featured ?? $a->featured ?? false);

                return [
                    'id'            => $a->id,
                    'title'         => $a->title,
                    'slug'          => $a->slug,
                    'excerpt'       => $a->excerpt,
                    'published_at'  => optional($a->published_at)->toDateTimeString(),
                    'rubric_id'     => $a->rubric_id,
                    'category_id'   => $a->category_id,
                    'is_featured'   => $isFeatured,  // alias 1
                    'featured'      => $isFeatured,  // alias 2 (pour anciens fronts)
                    // Images : plusieurs alias pour compatibilité
                    'image_url'     => $img,
                    'image'         => $img,
                    'cover_url'     => $img,
                    'cover'         => $img,
                ];
            });

        return response()->json(['data' => $items]);
    }

    /**
     * GET /api/articles/{id}
     */
    public function show(int|string $id, Request $request)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        if ($request->boolean('count')) {
            $article->increment('views');
        }

        return response()->json($this->serializeArticle($article));
    }

    /**
     * GET /api/articles/slug/{slug}
     * Détail par slug avec 301 si ancien slug (previous_slugs).
     */
    public function showBySlug(string $slug, Request $request)
    {
        $normalized = $this->normalizeSlug($slug);

        // 1) essai direct
        $article = Article::where('slug', $normalized)->first();

        // 2) sinon, regarder dans previous_slugs
        if (!$article) {
            $needle  = '"'.$normalized.'"';
            $article = Article::where('previous_slugs', 'like', "%{$needle}%")->first();
            if ($article) {
                // redirection 301 vers le slug canonique actuel
                return response()->json([
                    'redirect' => url('/articles/'.$article->slug),
                ], 301);
            }
        }

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        if ($request->boolean('count')) {
            $article->increment('views');
        }

        return response()->json($this->serializeArticle($article));
    }

    /* -------------------- Helpers -------------------- */

    private function normalizeSlug(string $slug): string
    {
        $s = trim($slug);
        return $s === Str::slug($s) ? $s : Str::slug($s);
    }

    /**
     * URL publique d’image robuste (fallback si pas d'accessor)
     */
    private function toPublicImageUrl(?string $path): ?string
    {
        if (!$path) return null;

        $p = trim($path);
        if (preg_match('#^https?://#i', $p)) {
            return $p; // déjà absolue
        }

        // Normalise et nettoie
        $p = str_replace('\\', '/', $p);
        $p = ltrim($p, '/');
        $p = preg_replace('#^(public|storage)/#i', '', $p);

        return asset('storage/'.$p);
    }

    /**
     * Normalisation de sortie pour le front
     */
    private function serializeArticle(Article $article): array
    {
        // tags/sources → tableaux
        $tags = $article->tags;
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            $tags    = is_array($decoded) ? $decoded : null;
        }
        $sources = $article->sources;
        if (is_string($sources)) {
            $decoded = json_decode($sources, true);
            $sources = is_array($decoded) ? $decoded : null;
        }

        // Slug d’auteur (si auteur en texte libre)
        $authorSlug = $article->author ? Str::slug($article->author) : null;

        // Image (utilise accessor si dispo, sinon fallback)
        $img = method_exists($article, 'getImageUrlAttribute')
            ? $article->image_url
            : $this->toPublicImageUrl($article->thumbnail ?? $article->image ?? $article->cover ?? null);

        // compat "featured" vs "is_featured"
        $isFeatured = (bool) ($article->is_featured ?? $article->featured ?? false);

        return [
            'id'            => $article->id,
            'title'         => $article->title,
            'slug'          => $article->slug,
            'excerpt'       => $article->excerpt,
            'thumbnail'     => $article->thumbnail,
            'thumbnail_url' => $img, // ← URL normalisée
            'category'      => $article->category, // si tu as une relation, adapte
            'featured'      => $isFeatured,
            'views'         => (int) ($article->views ?? 0),
            'author'        => $article->author,
            'author_slug'   => $authorSlug,
            'tags'          => $tags,
            'sources'       => $sources,
            'body'          => $article->body,
            'published_at'  => optional($article->published_at)->toIso8601String(),
            'updated_at'    => optional($article->updated_at)->toIso8601String(),
            'canonical'     => url('/articles/'.$article->slug),
            // quelques alias d'image pour compat avec diverses pages front
            'image_url'     => $img,
            'image'         => $img,
            'cover_url'     => $img,
            'cover'         => $img,
        ];
    }
}
