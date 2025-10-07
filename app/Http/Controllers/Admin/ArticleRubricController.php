<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Rubric;
use Illuminate\Http\Request;

class ArticleRubricController extends Controller
{
    public function edit(Article $article)
    {
        $rubrics = Rubric::orderBy('name')->where('is_active', true)->get(['id','name']);
        return view('admin.articles.rubric', compact('article','rubrics'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'rubric_id' => ['nullable','exists:rubrics,id'],
        ]);

        $article->rubric_id = $data['rubric_id'] ?? null;
        $article->save();

        return redirect()->route('admin.articles.edit', $article)->with('ok','Rubrique mise à jour.');
    }
}
