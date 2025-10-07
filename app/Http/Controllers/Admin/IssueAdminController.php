<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueAdminController extends Controller
{
    /** Liste + recherche (?q= ou ?search=) */
    public function index(Request $r)
    {
        $q = trim($r->string('q')->toString() ?: $r->string('search')->toString());

        $items = Issue::query()
            ->when($q !== '', function ($qb) use ($q) {
                $like = "%{$q}%";
                $qb->where(function ($qq) use ($like) {
                    $qq->where('number', 'like', $like)
                       ->orWhere('title',  'like', $like);
                });
            })
            ->orderByDesc('number')
            ->paginate(20)
            ->withQueryString();

        // Vue attend généralement "issues", mais on fournit aussi "items" pour les tableaux génériques
        return view('admin.issues.index', [
            'issues' => $items,
            'items'  => $items,
            'search' => $q,
            'q'      => $q,
        ]);
    }

    public function create()
    {
        $issue = new Issue();
        return view('admin.issues.create', compact('issue'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Sommaire: textarea (ligne par ligne) OU JSON (champ "summary_json")
        $data['summary'] = $this->normalizeSummary($request);

        // Couverture
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('issues/covers', 'public');
            $data['cover_disk'] = 'public';
        }

        $issue = Issue::create($data);

        return redirect()
            ->route('admin.issues.edit', $issue)
            ->with('ok', 'Numéro créé.');
    }

    public function edit(Issue $issue)
    {
        return view('admin.issues.edit', compact('issue'));
    }

    public function update(Request $request, Issue $issue)
    {
        $data = $this->validated($request);

        $data['summary'] = $this->normalizeSummary($request);

        if ($request->hasFile('cover')) {
            $newPath = $request->file('cover')->store('issues/covers', 'public');
            // Supprime l’ancienne si stockée côté public et différente
            if ($issue->cover && $issue->cover_disk === 'public' && $issue->cover !== $newPath) {
                Storage::disk('public')->delete($issue->cover);
            }
            $data['cover'] = $newPath;
            $data['cover_disk'] = 'public';
        }

        $issue->update($data);

        return redirect()
            ->route('admin.issues.edit', $issue)
            ->with('ok', 'Numéro mis à jour.');
    }

    public function destroy(Issue $issue)
    {
        if ($issue->cover && $issue->cover_disk === 'public') {
            Storage::disk('public')->delete($issue->cover);
        }
        $issue->delete();

        return redirect()
            ->route('admin.issues.index')
            ->with('ok', 'Numéro supprimé.');
    }

    /** Toggle publication depuis un bouton d’action rapide */
    public function publish(Request $r, Issue $issue)
    {
        $issue->is_published = $r->has('published')
            ? (bool) $r->boolean('published')
            : ! $issue->is_published;

        $issue->save();

        return back()->with('ok', $issue->is_published ? 'Numéro publié.' : 'Numéro dépublié.');
    }

    /** Actions groupées: publier / dépublier / supprimer */
    public function bulk(Request $r)
    {
        $ids = collect($r->input('ids', []))->filter()->all();
        $action = (string) $r->input('action');

        if (empty($ids) || !in_array($action, ['publish','unpublish','delete'], true)) {
            return back()->with('ko', 'Action invalide.');
        }

        $q = Issue::whereIn('id', $ids);
        $updated = 0; $deleted = 0;

        if ($action === 'publish')   { $updated = (clone $q)->update(['is_published' => true]); }
        if ($action === 'unpublish') { $updated = (clone $q)->update(['is_published' => false]); }
        if ($action === 'delete')    { $deleted = (clone $q)->delete(); }

        return back()->with('ok', match ($action) {
            'publish'   => "{$updated} numéro(s) publié(s).",
            'unpublish' => "{$updated} numéro(s) dépublié(s).",
            'delete'    => "{$deleted} numéro(s) supprimé(s).",
        });
    }

    /** -------------------- Helpers -------------------- */

    private function validated(Request $r): array
    {
        $v = $r->validate([
            'number'       => ['required','integer','min:1'],
            'date'         => ['nullable','date'],
            'title'        => ['nullable','string','max:255'],
            'is_published' => ['nullable','boolean'],
            'summary'      => ['nullable','string'],   // textarea (fallback)
            'summary_json' => ['nullable','string'],   // JSON optionnel
            'cover'        => ['nullable','image','max:4096'],
        ]);

        // Normalisation booléen
        $v['is_published'] = $r->boolean('is_published');

        return $v;
    }

    /** Transforme textarea/JSON en structure array normalisée */
    private function normalizeSummary(Request $r): ?array
    {
        if ($r->filled('summary_json')) {
            $json = json_decode($r->input('summary_json'), true);
            return is_array($json) ? array_values($json) : null;
        }

        $text = (string) $r->input('summary');
        if ($text === '') return null;

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $items = array_values(array_filter(array_map(function ($l) {
            $t = trim($l);
            if ($t === '') return null;

            // Optionnel: "Titre | 12" → ['title'=>'Titre','page'=>12]
            if (str_contains($t, '|')) {
                [$title, $page] = array_map('trim', explode('|', $t, 2));
                $page = is_numeric($page) ? (int) $page : null;
                return ['title' => $title, 'page' => $page];
            }

            return ['title' => $t, 'page' => null];
        }, $lines)));

        return $items ?: null;
    }
}
