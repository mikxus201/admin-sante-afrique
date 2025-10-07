@extends('admin.layout')
@section('title', $c->exists ? 'Modifier une catégorie' : 'Nouvelle catégorie')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">{{ $c->exists ? 'Modifier une catégorie' : 'Nouvelle catégorie' }}</h1>

  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      <ul class="list-disc pl-4">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  <form method="post"
        action="{{ $c->exists ? route('admin.categories.update',$c) : route('admin.categories.store') }}"
        class="rounded border bg-white p-4 space-y-4">
    @csrf
    @if($c->exists) @method('PUT') @endif

    <label class="block">
      <span class="text-sm">Nom</span>
      <input name="name" value="{{ old('name',$c->name) }}" required class="mt-1 w-full rounded border px-3 py-2">
    </label>

    <label class="block">
      <span class="text-sm">Slug (optionnel)</span>
      <input name="slug" value="{{ old('slug',$c->slug) }}" class="mt-1 w-full rounded border px-3 py-2" placeholder="ex: dossiers">
    </label>

    <label class="block">
      <span class="text-sm">Description</span>
      <textarea name="description" rows="5" class="mt-1 w-full rounded border px-3 py-2">{{ old('description',$c->description) }}</textarea>
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active',$c->is_active) ? 'checked' : '' }}>
      <span>Active</span>
    </label>

    <div class="flex items-center gap-3">
      <button class="px-4 py-2 rounded border">Enregistrer</button>
      <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded border">Annuler</a>
    </div>
  </form>
</div>
@endsection
