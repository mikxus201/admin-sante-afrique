<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    public function index(Request $r)
    {
        // Accepte ?q= ou ?search=
        $q = trim($r->string('q')->toString() ?: $r->string('search')->toString());

        $categories = Category::query()
            ->when($q !== '', function ($qb) use ($q) {
                $like = "%{$q}%";
                $qb->where(function ($qq) use ($like) {
                    $qq->where('name', 'like', $like)
                       ->orWhere('slug', 'like', $like)
                       ->orWhere('description', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // Compat : certaines vues utilisent $items
        return view('admin.categories.index', [
            'categories' => $categories,
            'items'      => $categories,
            'q'          => $q,
            'search'     => $q,
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', ['c' => new Category()]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        // Slug: si vide → dérivé du nom, sinon nettoyage
        $data['slug'] = $data['slug']
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Catégorie créée.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', ['c' => $category]);
    }

    public function update(Request $r, Category $category)
    {
        $data = $this->validated($r, $category);

        // Slug: si renseigné on le nettoie, sinon on garde l’existant
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $category->update($data);

        return back()->with('ok', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('ok', 'Catégorie supprimée.');
    }

    public function toggle(Category $category)
    {
        $category->is_active = ! $category->is_active;
        $category->save();
        return back()->with('ok', 'Statut mis à jour.');
    }

    /** Actions groupées: activer/désactiver/supprimer */
    public function bulk(Request $r)
    {
        $ids = collect($r->input('ids', []))->filter()->all();
        $action = (string) $r->input('action');

        if (empty($ids) || !in_array($action, ['activate','deactivate','delete'], true)) {
            return back()->with('ko', 'Action invalide.');
        }

        $q = Category::whereIn('id', $ids);
        $updated = 0; $deleted = 0;

        if ($action === 'activate') {
            $updated = (clone $q)->update(['is_active' => true]);
        } elseif ($action === 'deactivate') {
            $updated = (clone $q)->update(['is_active' => false]);
        } else { // delete
            $deleted = (clone $q)->delete();
        }

        return back()->with('ok', match ($action) {
            'activate'   => "{$updated} catégorie(s) activée(s).",
            'deactivate' => "{$updated} catégorie(s) désactivée(s).",
            'delete'     => "{$deleted} catégorie(s) supprimée(s).",
        });
    }

    /**
     * Validation + normalisation des booléens/alias.
     * $current est optionnel (utilisé pour l’unicité du slug en update).
     */
    private function validated(Request $r, ?Category $current = null): array
    {
        $v = $r->validate([
            'name'        => ['required','string','max:120'],
            'slug'        => [
                'nullable','string','max:140',
                Rule::unique('categories', 'slug')->ignore($current?->id),
            ],
            'description' => ['nullable','string'],
            // On accepte les deux noms de champ pour ne pas toucher la vue
            'is_active'   => ['nullable','boolean'],
            'active'      => ['nullable','boolean'],
        ]);

        // Normalisation : la vue peut envoyer "active" ; le modèle stocke "is_active"
        $v['is_active'] = $r->boolean('is_active') || $r->boolean('active');
        unset($v['active']); // pas de colonne "active" en DB

        return $v;
    }
}
