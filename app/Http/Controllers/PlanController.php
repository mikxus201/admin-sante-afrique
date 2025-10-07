<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index() { return ['plans' => \App\Models\Plan::where('is_published', true)->orderBy('price_fcfa')->get()]; }

    public function show(string $slug) { return Plan::where('slug',$slug)->firstOrFail(); }

    public function store(Request $request) {
        $this->authorize('admin');
        $data = $request->validate([
    'name'        => 'required|string',
    'slug'        => 'required|string|alpha_dash|unique:plans,slug,'.($plan->id ?? 'null'),
    'description' => 'nullable|string',
    'price_fcfa'  => 'required|integer|min:0',
    'is_published'=> 'sometimes|boolean', // ← important
  ]);

      $data = $request->only(['name','slug','description','price_fcfa']);
      $data['is_published'] = (bool) $request->boolean('is_published');

      $plan->update($data); // ou Plan::create($data) en création

    }

    public function update(Request $request, Plan $plan) {
        $this->authorize('admin');
        $plan->update($request->only('name','slug','description','price_fcfa','features','is_active'));
        return $plan;
    }

    public function destroy(Plan $plan) {
        $this->authorize('admin');
        $plan->delete();
        return ['ok'=>true];
    }
}
