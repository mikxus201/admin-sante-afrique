<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanAdminController extends Controller
{
    // GET /api/admin/plans
    public function index()
    {
        return response()->json(
            Plan::orderByDesc('created_at')->get()
        );
    }

    // POST /api/admin/plans
    public function store(Request $r)
    {
        $data = $r->validate([
            'name'        => ['required','string','max:255'],
            'slug'        => ['required','string','max:255','alpha_dash','unique:plans,slug'],
            'description' => ['nullable','string'],
            'price_fcfa'  => ['required','integer','min:0'],
            'is_published'=> ['boolean'],
        ]);
        $data['is_published'] = $r->boolean('is_published');
        $plan = Plan::create($data);

        return response()->json($plan, 201);
    }

    // PUT /api/admin/plans/{plan}
    public function update(Request $r, Plan $plan)
    {
        $data = $r->validate([
            'name'        => ['required','string','max:255'],
            'slug'        => ['required','string','max:255','alpha_dash', Rule::unique('plans','slug')->ignore($plan->id)],
            'description' => ['nullable','string'],
            'price_fcfa'  => ['required','integer','min:0'],
            'is_published'=> ['boolean'],
        ]);
        $data['is_published'] = $r->boolean('is_published');
        $plan->update($data);

        return response()->json($plan);
    }

    // PATCH /api/admin/plans/{plan}/publish
    public function publish(Request $r, Plan $plan)
    {
        $plan->is_published = (bool) $r->boolean('is_published', true);
        $plan->save();
        return response()->json(['ok' => true]);
    }

    // DELETE /api/admin/plans/{plan}
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['ok' => true]);
    }
}
