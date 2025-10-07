@extends('admin.layout')
@section('title','Créer une offre')
@section('content')
  <div class="max-w-3xl mx-auto">
      <h1 class="text-2xl font-semibold mb-4">Créer une offre</h1>
      @include('admin.plans.partials.form', ['plan' => $plan ?? new \App\Models\Plan()])
  </div>
@endsection
