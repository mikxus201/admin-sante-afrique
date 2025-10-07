@extends('layouts.admin')
@section('title', $a->exists ? 'Modifier un article' : 'Nouvel article')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">{{ $a->exists ? 'Modifier un article' : 'Nouvel article' }}</h1>

  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      <ul class="list-disc pl-4">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  <form method="post"
        action="{{ $a->exists ? route('admin.articles.update', $a) : route('admin.articles.store') }}"
        enctype="multipart/form-data"
        class="rounded border bg-white p-4 space-y-4">
    @csrf
    @if($a->exists) @method('PUT') @endif

    <label class="block">
      <span class="text-sm">Titre</span>
      <input name="title" value="{{ old('title',$a->title) }}" required class="mt-1 w-full rounded border px-3 py-2">
    </label>

    <label class="block">
      <span class="text-sm">Slug (optionnel)</span>
      <input name="slug" value="{{ old('slug',$a->slug) }}" class="mt-1 w-full rounded border px-3 py-2" placeholder="titre-de-l-article">
    </label>

    <label class="block">
      <span class="text-sm">Chapeau</span>
      <textarea name="excerpt" rows="3" class="mt-1 w-full rounded border px-3 py-2">{{ old('excerpt',$a->excerpt) }}</textarea>
    </label>

    <label class="block">
      <span class="text-sm">Contenu</span>
      <textarea name="body" rows="12" class="mt-1 w-full rounded border px-3 py-2">{{ old('body',$a->body) }}</textarea>
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Auteur (libre)</span>
        <input name="author" value="{{ old('author',$a->author) }}" class="mt-1 w-full rounded border px-3 py-2">
      </label>
      <label class="block">
        <span class="text-sm">Auteur (référence)</span>
        <select name="author_id" class="mt-1 w-full rounded border px-3 py-2">
          <option value="">—</option>
          @foreach($authors as $au)
            <option value="{{ $au->id }}" @selected(old('author_id',$a->author_id)==$au->id)>{{ $au->name }}</option>
          @endforeach
        </select>
      </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Catégorie (libre)</span>
        <input name="category" value="{{ old('category',$a->category) }}" class="mt-1 w-full rounded border px-3 py-2">
      </label>
      <label class="block">
        <span class="text-sm">Catégorie (référence)</span>
        <select name="category_id" class="mt-1 w-full rounded border px-3 py-2">
          <option value="">—</option>
          @foreach($cats as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id',$a->category_id)==$cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$a->is_featured) ? 'checked' : '' }}>
        <span>À la une</span>
      </label>

      <label class="block">
        <span class="text-sm">Publication (date/heure)</span>
        <input type="datetime-local" name="published_at"
               value="{{ old('published_at', $a->published_at ? $a->published_at->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 w-full rounded border px-3 py-2">
      </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Vignette (upload)</span>
        <input type="file" name="thumbnail_file" accept="image/*" class="mt-1 block w-full">
      </label>
      @if($a->thumbnail)
        <div class="flex items-end">
          <img src="{{ asset('storage/'.$a->thumbnail) }}" class="h-24 w-24 rounded object-cover" alt="">
        </div>
      @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Tags (séparés par virgule)</span>
        <input name="tags" value="{{ is_array(old('tags',$a->tags)) ? implode(', ',$a->tags) : old('tags',$a->tags) }}" class="mt-1 w-full rounded border px-3 py-2">
      </label>
      <label class="block">
        <span class="text-sm">Sources (séparées par virgule)</span>
        <input name="sources" value="{{ is_array(old('sources',$a->sources)) ? implode(', ',$a->sources) : old('sources',$a->sources) }}" class="mt-1 w-full rounded border px-3 py-2">
      </label>
    </div>

    <div class="flex items-center gap-3">
      <button class="px-4 py-2 rounded border">Enregistrer</button>
      <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 rounded border">Annuler</a>
    </div>
  </form>
</div>
@endsection
