<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Article;

class CategoryController extends Controller
{
    public function index()
    {
        $items = Category::orderBy('name')
            ->get(['id','name','slug','is_active']);

        return response()->json(['data' => $items]);
    }

    public function show(string $slug)
    {
        $c = Category::where('slug', $slug)->firstOrFail();

        return response()->json([
            'id'        => $c->id,
            'name'      => $c->name,
            'slug'      => $c->slug,
            'is_active' => (bool) $c->is_active,
        ]);
    }

    public function articles(string $slug)
    {
        $c = Category::where('slug', $slug)->firstOrFail();

        $items = Article::whereNotNull('published_at')
            ->where('category_id', $c->id)
            ->orderByDesc('published_at')
            ->limit(50)
            ->get()
            ->map(function (Article $a) {
                $img = $a->image_url;
                return [
                    'id'            => $a->id,
                    'title'         => $a->title,
                    'slug'          => $a->slug,
                    'excerpt'       => $a->excerpt,
                    'published_at'  => optional($a->published_at)->toDateTimeString(),
                    'rubric_id'     => $a->rubric_id,
                    'category_id'   => $a->category_id,
                    'category'      => optional($a->category)->name ?? null,
                    'category_slug' => optional($a->category)->slug ?? null,
                    'is_featured'   => (bool) $a->is_featured,
                    'image_url'     => $img,
                    'image'         => $img,
                    'cover_url'     => $img,
                    'cover'         => $img,
                ];
            });

        return response()->json([
            'category' => [
                'id'   => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
            ],
            'data' => $items,
        ]);
    }
}
