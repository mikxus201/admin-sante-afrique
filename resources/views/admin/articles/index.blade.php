@extends('layouts.admin')
@section('title','Articles')

@section('content')
<div class="container py-4">
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span><span>Articles</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Articles</h1>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-success">+ Nouvel article</a>
  </div>

  <form method="GET" action="{{ route('admin.articles.index') }}" class="mb-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher titre, slug, catégorie, rubrique…"
           style="width:100%;padding:.6rem .8rem;border:1px solid #e5e7eb;border-radius:8px">
  </form>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>#</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Rubrique</th>
            <th>À la une</th>
            <th>Publiée</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($articles as $a)
            <tr>
              <td>{{ $a->id }}</td>
              <td class="text-truncate" style="max-width:420px">{{ $a->title }}</td>
              <td>{{ $a->category->name ?? '—' }}</td>
              <td>{{ $a->rubric->name ?? '—' }}</td>
              <td>{{ $a->is_featured ? 'Oui' : 'Non' }}</td>
              <td>{{ $a->published_at ? $a->published_at->format('d/m/Y') : '—' }}</td>
              <td class="text-end">
  <div class="flex" style="justify-content:flex-end;gap:.5rem">
    @php
      $rubricUrl = \Route::has('admin.articles.rubric.edit')
        ? route('admin.articles.rubric.edit', $a)
        : url('/admin/articles/'.$a->id.'/rubric'); // fallback
    @endphp

    <a href="{{ $rubricUrl }}" class="btn">Rubrique</a>
    <a href="{{ route('admin.articles.edit', $a) }}" class="btn">Éditer</a>

    <form method="POST" action="{{ route('admin.articles.destroy', $a) }}"
          onsubmit="return confirm('Supprimer cet article ?')" style="display:inline">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Supprimer</button>
    </form>
  </div>
</td>

            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if(method_exists($articles,'links'))
      <div class="mt-3">{{ $articles->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
