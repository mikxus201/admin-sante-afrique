<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Arr; // <<< AJOUTER


class ArticleController extends Controller
{
    public function index(Request $r)
    {
             $q = Article::query()
              ->whereNotNull('published_at')
              ->with(['rubric:id,slug,name', 'category:id,slug,name']) // ✅
              ->orderByDesc('published_at')
              ->orderByDesc('id');

        // Recherche plein texte (q ou search)
        if ($s = ($r->input('q') ?? $r->input('search'))) {
            $q->where(function ($x) use ($s) {
                $x->where('title', 'like', "%$s%")
                  ->orWhere('excerpt', 'like', "%$s%")
                  ->orWhere('body', 'like', "%$s%");
            });
        }

        // Filtre rubrique/section/catégorie (tolérant)
        if ($rub = ($r->input('rubric')
                 ?? $r->input('rubrique')
                 ?? $r->input('section')
                 ?? $r->input('category'))) {
            $slug = Str::slug($rub);
            $q->where(function ($x) use ($slug) {
                // colonne texte éventuelle
                $x->orWhere('rubric', $slug)
                  ->orWhere('rubric_slug', $slug)
                  ->orWhere('category', $slug)
                  ->orWhere('category_slug', $slug)
                  // relation Eloquent
                  ->orWhereHas('rubric', fn ($rq) => $rq->where('slug', $slug))
                  ->orWhereHas('category', fn ($rq) => $rq->where('slug', $slug));
            });
        }

        $perPage = min(50, (int)($r->input('per_page') ?? $r->input('perPage') ?? 12));
        $items   = $q->paginate($perPage)->appends($r->query());

        // ----- API / JSON -----
        if ($r->is('api/*') || $r->wantsJson() || $r->expectsJson() || $r->boolean('json')) {
            return response()->json($items);
        }

        // ----- Vue web -----
        return view('articles.index', [
            'items' => $items,
            'q'     => $s ?? null,
        ]);
    }

    // ... (show() inchangé)

     public function apiShowSlug(Request $request, string $slug)
    {
        $article = Article::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        // "fields" peut être "id,title,slug" OU un tableau ["id","title","slug"]
        $fields = $request->input('fields');
        if (is_string($fields)) {
            $fields = array_filter(array_map('trim', preg_split('/[,;]\s*/', $fields)));
        }

        // NE PAS appeler ->only() sur le Model ni sur une string.
        // On convertit en tableau puis on utilise Arr::only(...)
        $data = $article->toArray();
        if (is_array($fields) && !empty($fields)) {
            $data = Arr::only($data, $fields);
        }

        return response()->json(['data' => $data]);
    }

    public function showBySlug(string $slug)
{
    // Récupérer la Request via le helper (sûr à 100%)
    $req = request();

    $article = \App\Models\Article::with(['category:id,name,slug', 'rubric:id,name,slug'])
        ->where('slug', $slug)
        ->orWhere('id', $slug)
        ->firstOrFail();

    // JSON pour le front
    if ($req->wantsJson() || $req->boolean('json')) {
        return response()->json([
            'id'            => $article->id,
            'slug'          => $article->slug,
            'title'         => $article->title,
            'excerpt'       => $article->excerpt,
            'body'          => $article->body,
            'published_at'  => optional($article->published_at)->toIso8601String(),
            'thumbnail_url' => $article->thumbnail_url ?? null,
            'image_url'     => $article->image_url ?? ($article->thumbnail_url ?? null),
            'category'      => $article->category ? $article->category->only(['id','name','slug']) : null,
            'rubric'        => $article->rubric   ? $article->rubric->only(['id','name','slug'])   : null,
            'views'         => $article->views,
        ]);
    }

    // sinon, vue HTML classique si tu l’utilises
    return $this->show($slug);
}
}