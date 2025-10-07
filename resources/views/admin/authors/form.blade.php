@extends('admin.layout')
@section('title', $a->exists ? 'Modifier un auteur' : 'Nouvel auteur')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">
    {{ $a->exists ? 'Modifier un auteur' : 'Nouvel auteur' }}
  </h1>

  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      <ul class="list-disc pl-4">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="post"
        action="{{ $a->exists ? route('admin.authors.update',$a) : route('admin.authors.store') }}"
        enctype="multipart/form-data"
        class="rounded border bg-white p-4 space-y-4">
    @csrf
    @if($a->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Nom</span>
        <input name="name" value="{{ old('name',$a->name) }}" required class="mt-1 w-full rounded border px-3 py-2">
      </label>

      <label class="block">
        <span class="text-sm">Slug (optionnel)</span>
        <input name="slug" value="{{ old('slug',$a->slug) }}" class="mt-1 w-full rounded border px-3 py-2" placeholder="jean-michel">
      </label>
    </div>

    <label class="block">
      <span class="text-sm">Biographie</span>
      <textarea name="bio" rows="6" class="mt-1 w-full rounded border px-3 py-2">{{ old('bio',$a->bio) }}</textarea>
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Photo (upload)</span>
        <input type="file" name="photo_file" accept="image/*" class="mt-1 block w-full">
      </label>
      @if($a->photo)
        <div class="flex items-end">
          <img src="{{ asset('storage/'.$a->photo) }}" class="h-20 w-20 rounded object-cover" alt="">
        </div>
      @endif
    </div>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="active" value="1" {{ old('active',$a->active) ? 'checked' : '' }}>
      <span>Actif</span>
    </label>

    <div class="flex items-center gap-3 pt-2">
      <button class="px-4 py-2 rounded border">Enregistrer</button>
      <a href="{{ route('admin.authors.index') }}" class="px-4 py-2 rounded border">Annuler</a>
    </div>
  </form>
</div>
@endsection
