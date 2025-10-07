<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class HelpItemWebController extends Controller
{
    /** Liste + recherche (?q= ou ?search=) + filtre par groupe (default: subscribe) */
    public function index(Request $r)
    {
        $group = $r->string('group')->toString() ?: 'subscribe';
        $q     = trim($r->string('q')->toString() ?: $r->string('search')->toString());

        $items = HelpItem::query()
            ->where('group', $group)
            ->when($q !== '', function ($qb) use ($q) {
                $like = "%{$q}%";
                $qb->where(function ($qq) use ($like) {
                    $qq->where('key',    'like', $like)
                       ->orWhere('title',  'like', $like)
                       ->orWhere('content','like', $like);
                });
            })
            ->orderBy('position')
            ->orderBy('id')
            ->paginate(50)
            ->withQueryString();

        if ($r->expectsJson()) {
            return response()->json(compact('items', 'group'));
        }

        // NB: vues en dossier admin/help-items/*
        return view('admin.help_items.index', compact('items', 'group', 'q'));
    }

    public function create()
    {
        $item = new HelpItem([
            'group'        => 'subscribe',
            'is_published' => true,
        ]);

        return view('admin.help_items.create', compact('item'));
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        // Normalisations
        $data['group']        = $data['group'] ?: 'subscribe';
        $data['key']          = Str::slug($data['key']); // évite espaces/accents
        $data['is_published'] = (bool) $r->boolean('is_published');

        // Position: dernier + 1 dans le groupe
        $data['position'] = (HelpItem::where('group', $data['group'])->max('position') ?? 0) + 1;

        $item = HelpItem::create($data);

        if ($r->expectsJson()) {
            return response()->json($item, 201);
        }

        return redirect()->route('admin.help-items.index', ['group' => $data['group']])
            ->with('status', 'Bloc créé.');
    }

    public function edit(HelpItem $helpItem)
    {
        return view('admin.help_items.edit', ['item' => $helpItem]);
    }

    public function update(Request $r, HelpItem $helpItem)
    {
        $data = $this->validated($r, $helpItem);

        // Normalisations
        if (!empty($data['key'])) {
            $data['key'] = Str::slug($data['key']);
        }
        $data['is_published'] = (bool) $r->boolean('is_published');

        $helpItem->update($data);

        if ($r->expectsJson()) {
            return response()->json($helpItem);
        }

        return redirect()->route('admin.help-items.index', ['group' => $helpItem->group])
            ->with('status', 'Bloc mis à jour.');
    }

    public function destroy(HelpItem $helpItem)
    {
        $group = $helpItem->group;
        $helpItem->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('admin.help-items.index', ['group' => $group])
            ->with('status', 'Bloc supprimé.');
    }

    /** Publier / Dépublier (toggle ou forcer via is_published=1|0) */
    public function publish(Request $r, HelpItem $helpItem)
    {
        if ($r->has('is_published')) {
            $helpItem->is_published = (bool) $r->boolean('is_published');
        } else {
            $helpItem->is_published = ! (bool) $helpItem->is_published;
        }
        $helpItem->save();

        if ($r->expectsJson()) {
            return response()->json(['ok' => true, 'is_published' => $helpItem->is_published, 'item' => $helpItem]);
        }

        return back()->with('status', $helpItem->is_published ? 'Publié.' : 'Dépublié.');
    }

    /** Monter d’un cran dans l’ordre */
    public function moveUp(HelpItem $helpItem)
    {
        $prev = HelpItem::where('group', $helpItem->group)
            ->where('position', '<', $helpItem->position)
            ->orderByDesc('position')
            ->first();

        if ($prev) {
            [$helpItem->position, $prev->position] = [$prev->position, $helpItem->position];
            $helpItem->save();
            $prev->save();
        }

        return back();
    }

    /** Descendre d’un cran dans l’ordre */
    public function moveDown(HelpItem $helpItem)
    {
        $next = HelpItem::where('group', $helpItem->group)
            ->where('position', '>', $helpItem->position)
            ->orderBy('position')
            ->first();

        if ($next) {
            [$helpItem->position, $next->position] = [$next->position, $helpItem->position];
            $helpItem->save();
            $next->save();
        }

        return back();
    }

    /** --------------- Helpers --------------- */

    /**
     * Validation + unicité de la clé par groupe.
     * $current est optionnel pour ignorer l’ID en update.
     */
    private function validated(Request $r, ?HelpItem $current = null): array
    {
        $group = (string) $r->input('group', 'subscribe');

        return $r->validate([
            'group'        => ['required', 'string', 'max:50'],
            'key'          => [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('help_items', 'key')
                    ->where(fn ($q) => $q->where('group', $group))
                    ->ignore($current?->id),
            ],
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ]);
    }
}
