<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthorAdminController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
{
    $q = trim($request->input('q',''));

    $authors = \App\Models\Author::query()
        ->when($q !== '', function ($w) use ($q) {
            $w->where('name', 'like', "%{$q}%")
              ->orWhere('slug', 'like', "%{$q}%")
              ->orWhere('email','like', "%{$q}%");
        })
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return view('admin.authors.index', compact('authors'));
}



    public function create()
    {
        return view('admin.authors.form', ['a' => new Author()]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        // Photo (upload public)
        if ($r->hasFile('photo_file')) {
            $data['photo'] = $r->file('photo_file')->store('authors', 'public');
        }

        // Slug: si vide → dérivé du nom; sinon nettoyage
        $data['slug'] = $data['slug']
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        $a = Author::create($data);

        return redirect()->route('admin.authors.edit', $a)->with('ok', 'Auteur créé.');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.form', ['a' => $author]);
    }

    public function update(Request $r, Author $author)
    {
        $data = $this->validated($r, $author);

        // Photo (remplace l’ancienne si présente)
        if ($r->hasFile('photo_file')) {
            if ($author->photo && Storage::disk('public')->exists($author->photo)) {
                Storage::disk('public')->delete($author->photo);
            }
            $data['photo'] = $r->file('photo_file')->store('authors', 'public');
        }

        // Slug: si fourni on le nettoie, sinon on conserve l’existant
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $author->update($data);

        return back()->with('ok', 'Auteur mis à jour.');
    }

    public function destroy(Author $author)
    {
        if ($author->photo && Storage::disk('public')->exists($author->photo)) {
            Storage::disk('public')->delete($author->photo);
        }
        $author->delete();

        return back()->with('ok', 'Auteur supprimé.');
    }

    public function toggle(Author $author)
    {
        $author->active = ! $author->active;
        $author->save();

        return back()->with('ok', 'Statut mis à jour.');
    }

    /**
     * Validation + normalisation.
     * $current est optionnel (utilisé pour l’unicité du slug en update).
     */
    private function validated(Request $r, ?Author $current = null): array
    {
        $v = $r->validate([
            'name'       => ['required', 'string', 'max:140'],
            'slug'       => [
                'nullable', 'string', 'max:160',
                Rule::unique('authors', 'slug')->ignore($current?->id),
            ],
            'bio'        => ['nullable', 'string'],
            // On accepte éventuellement un alias is_active depuis d’anciennes vues
            'active'     => ['nullable', 'boolean'],
            'is_active'  => ['nullable', 'boolean'],
            'photo'      => ['nullable', 'string'],            // URL absolue possible
            'photo_file' => ['nullable', 'image', 'max:5120'], // ≤ 5 Mo
        ]);

        // Normalisation booléen
        $v['active'] = $r->boolean('active') || $r->boolean('is_active');
        unset($v['is_active']); // pas de colonne is_active en base

        return $v;
    }
}
