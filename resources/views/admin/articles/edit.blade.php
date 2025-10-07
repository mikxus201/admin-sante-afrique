@extends('layouts.admin')

@section('title', 'Modifier — ' . ($article->title ?? 'Article'))

@section('content')
<div class="container py-4">

  {{-- Fil d’Ariane --}}
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span>
    <a href="{{ route('admin.articles.index') }}" class="muted">Articles</a>
    <span class="muted mx-2">/</span>
    <span>Modifier</span>
  </nav>

  @php
    $rubricUrl = \Route::has('admin.articles.rubric.edit')
      ? route('admin.articles.rubric.edit', $article)
      : url('/admin/articles/'.$article->id.'/rubric'); // fallback
  @endphp

  {{-- Titre + actions (wrappables) --}}
  <div class="flex between mb-3" style="gap:.5rem; flex-wrap:wrap">
    <h1 class="text-xl font-bold" style="margin:0">Modifier un article</h1>
    <div class="flex" style="gap:.5rem">
      <a href="{{ $rubricUrl }}" class="btn">Rubrique</a>
      <a href="{{ route('admin.articles.index') }}" class="btn">Retour</a>
    </div>
  </div>

  {{-- ID + badge rubrique + raccourci --}}
  <div class="muted text-sm mb-2">ID #{{ $article->id }}</div>

  <div class="mb-3">
    <span class="badge">Rubrique : {{ optional($article->rubric)->name ?? '—' }}</span>
    <a href="{{ $rubricUrl }}" class="btn" style="padding:.25rem .6rem; margin-left:.5rem">Changer</a>
  </div>

  {{-- Messages flash --}}
  @if (session('ok'))      <div class="card p-3 mb-3">✔ {{ session('ok') }}</div> @endif
  @if (session('success')) <div class="card p-3 mb-3">✔ {{ session('success') }}</div> @endif
  @if (session('ko'))      <div class="card p-3 mb-3" style="border-color:#fecaca;background:#fef2f2">✖ {{ session('ko') }}</div> @endif

  {{-- Erreurs de validation --}}
  @if ($errors->any())
    <div class="card p-3 mb-3" style="border-color:#fecaca;background:#fef2f2">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Formulaire --}}
  @include('admin.articles._form', [
    'action'     => route('admin.articles.update', $article),
    'method'     => 'PUT',
    'submit'     => 'Mettre à jour',
    'article'    => $article,
    'authors'    => $authors    ?? collect(),
    'categories' => $categories ?? collect(),
  ])

</div>
@endsection
