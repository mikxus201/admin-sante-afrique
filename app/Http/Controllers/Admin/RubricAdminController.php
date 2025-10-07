<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RubricAdminController extends Controller
{
    /** Liste + recherche (?q= ou ?search=) */
    public function index(Request $r)
    {
        $q = trim($r->string('q')->toString() ?: $r->string('search')->toString());

        $rubrics = Rubric::query()
            ->when($q !== '', function ($qb) use ($q) {
                $like = "%{$q}%";
                $qb->where(function ($qq) use ($like) {
                    $qq->where('name', 'like', $like)
                       ->orWhere('slug', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        if ($r->expectsJson()) {
            return response()->json(['rubrics' => $rubrics, 'q' => $q]);
        }

        return view('admin.rubrics.index', [
            'rubrics' => $rubrics,
            'q'       => $q,
            'search'  => $q,
        ]);
    }

    public function create()
    {
        return view('admin.rubrics.create', [
            'r' => new Rubric(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        // Slug: si vide → dérivé du nom; sinon nettoyage
        $data['slug'] = $data['slug']
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        // Normalisation booléen (par défaut actif)
        $data['is_active'] = $r->boolean('is_active', true);

        $rubric = Rubric::create($data);

        if ($r->expectsJson()) {
            return response()->json($rubric, 201);
        }

        return redirect()->route('admin.rubrics.index')->with('status', 'Rubrique créée.');
    }

    public function edit(Rubric $rubric)
    {
        return view('admin.rubrics.edit', [
            'r' => $rubric,
        ]);
    }

    public function update(Request $r, Rubric $rubric)
    {
        $data = $this->validated($r, $rubric);

        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        if ($r->has('is_active')) {
            $data['is_active'] = $r->boolean('is_active');
        }

        $rubric->update($data);

        if ($r->expectsJson()) {
            return response()->json($rubric);
        }

        return back()->with('status', 'Rubrique mise à jour.');
    }

    /** Activer/Désactiver (toggle ou forcer via is_active=1|0) */
    public function toggle(Request $r, Rubric $rubric)
    {
        if ($r->has('is_active')) {
            $rubric->is_active = (bool) $r->boolean('is_active');
        } else {
            $rubric->is_active = ! (bool) $rubric->is_active;
        }
        $rubric->save();

        if ($r->expectsJson()) {
            return response()->json(['ok' => true, 'is_active' => $rubric->is_active, 'rubric' => $rubric]);
        }

        return back()->with('status', $rubric->is_active ? 'Rubrique activée.' : 'Rubrique désactivée.');
    }

    public function destroy(Rubric $rubric)
    {
        $rubric->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Rubrique supprimée.');
    }

    /* ----------------- Helpers ----------------- */

    /** Validation + unicité du slug (ignore en update) */
    private function validated(Request $r, ?Rubric $current = null): array
    {
        return $r->validate([
            'name'      => ['required', 'string', 'max:140'],
            'slug'      => [
                'nullable', 'string', 'max:160',
                // slug “propre” : minuscules/chiffres/tirets
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('rubrics', 'slug')->ignore($current?->id),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'slug.regex' => 'Le slug ne doit contenir que des minuscules, chiffres et tirets (ex: politiques-publiques).',
        ]);
    }
}
