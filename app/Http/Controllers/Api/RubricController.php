<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use App\Models\Article;

class RubricController extends Controller
{
    public function index()
    {
        $items = Rubric::where('is_active', true)
            ->orderBy('name')
            ->get(['id','name','slug','description','is_active']);

        return response()->json(['data' => $items]);
    }

    public function show($slug)
    {
        $rubric = Rubric::where('slug', $slug)->firstOrFail();

        return response()->json([
            'id'          => $rubric->id,
            'name'        => $rubric->name,
            'slug'        => $rubric->slug,
            'description' => $rubric->description,
            'is_active'   => (bool)$rubric->is_active,
        ]);
    }

    public function articles($slug)
    {
        $rubric = Rubric::where('slug', $slug)->firstOrFail();

        $items = Article::whereNotNull('published_at')
            ->where('rubric_id', $rubric->id)
            ->orderByDesc('published_at')
            ->limit(50)
            ->get(['id','title','slug','excerpt','published_at','rubric_id','category_id','is_featured'])
            ->map(function($a){
                return [
                    'id'           => $a->id,
                    'title'        => $a->title,
                    'slug'         => $a->slug,
                    'excerpt'      => $a->excerpt,
                    'published_at' => optional($a->published_at)->toDateTimeString(),
                    'rubric_id'    => $a->rubric_id,
                    'category_id'  => $a->category_id,
                    'is_featured'  => (bool)$a->is_featured,
                ];
            });

        return response()->json([
            'rubric' => [
                'id'   => $rubric->id,
                'name' => $rubric->name,
                'slug' => $rubric->slug,
            ],
            'data' => $items,
        ]);
    }
}
