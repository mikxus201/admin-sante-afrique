@extends('layouts.admin')
@section('title','Catégories')

@section('content')
<div class="container py-4">
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span><span>Catégories</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Catégories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success">+ Créer</a>
  </div>

  <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher nom, slug, description…"
           style="width:100%;padding:.6rem .8rem;border:1px solid #e5e7eb;border-radius:8px">
  </form>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>Nom</th>
            <th>Slug</th>
            <th>Actif</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($categories as $c)
            <tr>
              <td>{{ $c->name }}</td>
              <td>{{ $c->slug }}</td>
              <td>{{ $c->is_active ? 'Oui' : 'Non' }}</td>
              <td class="text-end">
                <div class="flex" style="justify-content:flex-end;gap:.5rem">
                  @if (Route::has('admin.categories.toggle'))
                    <form method="POST" action="{{ route('admin.categories.toggle', $c) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn">{{ $c->is_active ? 'Désactiver' : 'Activer' }}</button>
                    </form>
                  @endif
                  <a href="{{ route('admin.categories.edit', $c) }}" class="btn">Éditer</a>
                  <form method="POST" action="{{ route('admin.categories.destroy', $c) }}"
                        onsubmit="return confirm('Supprimer ?')" style="display:inline">
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

    @if(method_exists($categories,'links'))
      <div class="mt-3">{{ $categories->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
