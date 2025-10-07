@extends('layouts.admin')

@section('title', 'Affecter une rubrique — ' . ($article->title ?? 'Article'))

@section('content')
<div class="container py-4">
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span>
    <a href="{{ route('admin.articles.index') }}" class="muted">Articles</a>
    <span class="muted mx-2">/</span>
    <a href="{{ route('admin.articles.edit', $article) }}" class="muted">Éditer</a>
    <span class="muted mx-2">/</span>
    <span>Affecter une rubrique</span>
  </nav>

  <h1 class="text-xl font-bold mb-3">Affecter une rubrique</h1>

  <div class="card p-3">
    <form method="POST" action="{{ route('admin.articles.rubric.update', $article) }}">
      @csrf @method('PUT')

      <div class="mb-3">
        <label for="rubric_id" class="muted text-sm">Rubrique</label>
        <select id="rubric_id" name="rubric_id" style="width:100%;padding:.5rem;border:1px solid #e5e7eb;border-radius:8px;">
          <option value="">— Aucune rubrique —</option>
          @foreach($rubrics as $r)
            <option value="{{ $r->id }}" @selected($article->rubric_id == $r->id)>{{ $r->name }}</option>
          @endforeach
        </select>
        @error('rubric_id') <div class="muted" style="color:#dc2626">{{ $message }}</div> @enderror
      </div>

      <div class="flex between">
        <a href="{{ route('admin.articles.edit', $article) }}" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
@endsection
