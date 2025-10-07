@extends('layouts.admin')
@section('title','Éditer une rubrique')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">Éditer une rubrique</h1>

  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      <ul class="list-disc pl-4">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  <form method="post" action="{{ route('admin.rubrics.update',$r) }}" class="rounded border bg-white p-4 space-y-4">
    @csrf @method('PUT')

    <label class="block">
      <span class="text-sm">Nom</span>
      <input name="name" value="{{ old('name',$r->name) }}" required class="mt-1 w-full rounded border px-3 py-2">
    </label>

    <label class="block">
      <span class="text-sm">Slug (optionnel)</span>
      <input name="slug" value="{{ old('slug',$r->slug) }}" class="mt-1 w-full rounded border px-3 py-2">
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active',$r->is_active) ? 'checked' : '' }}>
      <span>Active</span>
    </label>

    <div class="flex items-center gap-3">
      <button class="px-4 py-2 rounded border">Enregistrer</button>
      <a href="{{ route('admin.rubrics.index') }}" class="px-4 py-2 rounded border">Annuler</a>
    </div>
  </form>
</div>
@endsection
