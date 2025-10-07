@extends('layouts.admin')
@section('title','Modifier une offre')
@section('content')
  <div class="max-w-3xl mx-auto">
      <h1 class="text-2xl font-semibold mb-4">Modifier l’offre</h1>
      @include('admin.plans.partials.form', ['plan' => $plan])
  </div>
@endsection
