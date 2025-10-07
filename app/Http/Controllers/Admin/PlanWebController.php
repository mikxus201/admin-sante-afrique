<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanWebController extends Controller
{
    /** Liste + recherche (?q= ou ?search=) */
    public function index(Request $r)
    {
        $q = trim($r->string('q')->toString() ?: $r->string('search')->toString());

        $plans = Plan::query()
            ->when($q !== '', function ($qb) use ($q) {
                $like = "%{$q}%";
                $qb->where(function ($qq) use ($like) {
                    $qq->where('name', 'like', $like)
                       ->orWhere('slug', 'like', $like)
                       ->orWhere('description', 'like', $like);
                });
            })
            ->orderByDesc('price_fcfa')
            ->paginate(50)
            ->withQueryString();

        if ($r->expectsJson()) {
            return response()->json($plans);
        }

        return view('admin.plans.index', [
            'plans'  => $plans,
            'items'  => $plans, // compat éventuelle
            'search' => $q,
            'q'      => $q,
        ]);
    }

    public function create()
    {
        $plan = new Plan();
        return view('admin.plans.create', compact('plan'));
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        // Slug: si vide → dérivé du nom; sinon nettoyage (sans accents/espaces)
        $data['slug'] = $data['slug']
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        $data['is_published'] = (bool) $r->boolean('is_published');

        $plan = Plan::create($data);

        if ($r->expectsJson()) {
            return response()->json($plan, 201);
        }
        return redirect()->route('admin.plans.index')->with('status','Offre créée.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $r, Plan $plan)
    {
        $data = $this->validated($r, $plan);

        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }
        $data['is_published'] = (bool) $r->boolean('is_published');

        $plan->update($data);

        if ($r->expectsJson()) {
            return response()->json($plan);
        }
        return back()->with('status','Offre mise à jour.');
    }

    /** Publier / Dépublier (toggle ou forcer via is_published=1|0) */
    public function publish(Request $request, Plan $plan)
    {
        if ($request->has('is_published')) {
            $plan->is_published = (bool) $request->boolean('is_published');
        } else {
            $plan->is_published = ! (bool) $plan->is_published;
        }
        $plan->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'is_published' => (bool) $plan->is_published,
                'plan' => $plan,
            ]);
        }
        return back()->with('status', $plan->is_published ? 'Offre publiée.' : 'Offre dépubliée.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('status','Offre supprimée.');
    }

    /** ---------------- Helpers ---------------- */
    private function validated(Request $r, ?Plan $current = null): array
    {
        return $r->validate([
            'name'        => ['required','string','max:255'],
            'slug'        => [
                'nullable','string','max:255',
                // n'autoriser que minuscules/chiffres/tirets -> évite les accents
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('plans','slug')->ignore($current?->id),
            ],
            'description' => ['nullable','string'],
            'price_fcfa'  => ['required','integer','min:0'],
            'is_published'=> ['sometimes','boolean'],
        ], [
            'slug.regex'  => 'Le slug ne doit contenir que des minuscules, chiffres et tirets (ex: annuel-numerique).',
        ]);
    }
}
