@extends('layouts.admin')
@section('title','Rubriques')

@section('content')
<div class="container py-4">

  {{-- Fil d’Ariane --}}
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span>
    <span>Rubriques</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Rubriques</h1>
    <a href="{{ route('admin.rubrics.create') }}" class="btn btn-success">+ Nouvelle rubrique</a>
  </div>

  {{-- Recherche --}}
  <form method="GET" action="{{ route('admin.rubrics.index') }}" class="mb-3">
    <input
      type="text"
      name="search"
      value="{{ request('search', $search ?? '') }}"
      placeholder="Rechercher nom / slug…"
      style="width:100%;padding:.6rem .8rem;border:1px solid #e5e7eb;border-radius:8px"
    >
  </form>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>Nom</th>
            <th>Slug</th>
            <th>Active</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rubrics as $r)
            <tr>
              <td class="font-bold">{{ $r->name }}</td>
              <td class="muted">{{ $r->slug }}</td>
              <td>{{ $r->is_active ? 'Oui' : 'Non' }}</td>
              <td class="text-end">
                <div class="flex" style="justify-content:flex-end;gap:.5rem">
                  @if (Route::has('admin.rubrics.toggle'))
                    <form method="POST" action="{{ route('admin.rubrics.toggle', $r) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn">
                        {{ $r->is_active ? 'Désactiver' : 'Activer' }}
                      </button>
                    </form>
                  @endif

                  <a href="{{ route('admin.rubrics.edit', $r) }}" class="btn">Éditer</a>

                  <form method="POST" action="{{ route('admin.rubrics.destroy', $r) }}"
                        style="display:inline" onsubmit="return confirm('Supprimer cette rubrique ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="muted" style="text-align:center;padding:1rem 0">
                Aucune rubrique
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($rubrics,'links'))
      <div class="mt-3">{{ $rubrics->withQueryString()->links() }}</div>
    @endif
  </div>

</div>
@endsection
