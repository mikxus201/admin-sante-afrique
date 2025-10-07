<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleAdminController extends Controller
{
    public function index(Request $request)
    {
        // On accepte ?q= ou ?search= pour être compatible avec tes anciennes vues
        $q = trim($request->string('q')->toString() ?: $request->string('search')->toString());

        $articles = Article::query()
            ->leftJoin('categories', 'categories.id', '=', 'articles.category_id')
            ->leftJoin('rubrics',   'rubrics.id',   '=', 'articles.rubric_id')
            ->when($q, function ($qb) use ($q) {
                $like = '%'.$q.'%';
                $qb->where(function ($qq) use ($like) {
                    $qq->where('articles.title', 'like', $like)
                       ->orWhere('articles.slug',  'like', $like)
                       ->orWhere('categories.name','like', $like)
                       ->orWhere('rubrics.name',   'like', $like);
                });
            })
            ->orderByDesc('articles.published_at')
            ->orderByDesc('articles.created_at')
            ->select([
                'articles.*',
                // alias utilisés par la vue
                'categories.name as category',
                'articles.is_featured as featured',
                'rubrics.name as rubric_name',
            ])
            ->paginate(20)
            ->withQueryString();

        // IMPORTANT : la vue attend $articles
        return view('admin.articles.index', [
            'articles' => $articles,
            'q'        => $q,
            'search'   => $q, // compat
        ]);
    }

    public function create()
    {
        return view('admin.articles.form', [
            'a'       => new Article(),
            'cats'    => Category::active()->orderBy('name')->get(),
            'authors' => Author::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        // Thumbnail
        if ($r->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $r->file('thumbnail_file')->store('articles', 'public');
        }

        // Slug propre
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        // Fallback libellés texte (optionnel)
        if (!empty($data['author_id'])) {
            $au = Author::find($data['author_id']);
            if ($au) { $data['author'] = $au->name; }
        }
        if (!empty($data['category_id'])) {
            $cat = Category::find($data['category_id']);
            if ($cat) { $data['category'] = $cat->name; }
        }

        $a = Article::create($data);

        return redirect()->route('admin.articles.edit', $a)->with('ok', 'Article créé.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.form', [
            'a'       => $article,
            'cats'    => Category::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
        ]);
    }

    public function update(Request $r, Article $article)
    {
        $data = $this->validated($r);

        if ($r->hasFile('thumbnail_file')) {
            if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = $r->file('thumbnail_file')->store('articles', 'public');
        }

        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        if (!empty($data['author_id'])) {
            $au = Author::find($data['author_id']);
            if ($au) { $data['author'] = $au->name; }
        }
        if (!empty($data['category_id'])) {
            $cat = Category::find($data['category_id']);
            if ($cat) { $data['category'] = $cat->name; }
        }

        $article->update($data);

        return back()->with('ok', 'Article mis à jour.');
    }

    public function destroy(Article $article)
    {
        if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete();

        return back()->with('ok', 'Article supprimé.');
    }

    private function validated(Request $r): array
    {
        $v = $r->validate([
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255'],
            'excerpt'        => ['nullable', 'string'],
            'body'           => ['nullable', 'string'],
            'author'         => ['nullable', 'string', 'max:255'],             // libellé libre
            'author_id'      => ['nullable', 'integer', 'exists:authors,id'],  // relation
            'category'       => ['nullable', 'string', 'max:100'],
            'category_id'    => ['nullable', 'integer', 'exists:categories,id'],
            'rubric_id'      => ['nullable', 'integer', 'exists:rubrics,id'],
            'is_featured'    => ['nullable', 'boolean'],
            'published_at'   => ['nullable', 'date'],
            'thumbnail'      => ['nullable', 'string'],
            'thumbnail_file' => ['nullable', 'image', 'max:4096'],
            'tags'           => ['nullable'],
            'sources'        => ['nullable'],
        ]);

        // Normalisation des booléens
        $v['is_featured'] = $r->boolean('is_featured');

        // Normalisation tags/sources (string -> array)
        foreach (['tags', 'sources'] as $key) {
            if (is_string($v[$key] ?? null) && $v[$key] !== '') {
                $v[$key] = collect(preg_split('/[,;]\s*/', $v[$key]))->filter()->values()->all();
            }
        }

        return $v;
    }
}
