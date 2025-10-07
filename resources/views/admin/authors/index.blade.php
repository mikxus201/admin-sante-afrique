@extends('layouts.admin')

@section('title', 'Auteurs')

@section('content')
<div class="container py-4">

  {{-- Fil d’Ariane --}}
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span>
    <span>Auteurs</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Auteurs</h1>
    <a href="{{ route('admin.authors.create') }}" class="btn btn-success">+ Créer</a>
  </div>

  @if(session('ok'))      <div class="card p-3 mb-3">✔ {{ session('ok') }}</div> @endif
  @if(session('success')) <div class="card p-3 mb-3">✔ {{ session('success') }}</div> @endif
  @if(session('ko'))      <div class="card p-3 mb-3" style="border-color:#fecaca;background:#fef2f2">✖ {{ session('ko') }}</div> @endif

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th style="width:64px">Photo</th>
            <th>Nom</th>
            <th>Slug</th>
            <th>Actif</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @if($authors->count())
            @foreach($authors as $a)
              <tr>
                <td>
                  @php $photo = $a->photo_url ?? $a->photo ?? null; @endphp
                  @if($photo)
                    <img src="{{ $photo }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:50%">
                  @else
                    —
                  @endif
                </td>
                <td>{{ $a->name }}</td>
                <td>{{ $a->slug }}</td>
                <td>{{ ($a->is_active ?? true) ? 'Oui' : 'Non' }}</td>
                <td class="text-end">
                  <div class="flex" style="justify-content:flex-end; gap:.5rem">
                    <a href="{{ route('admin.authors.edit', $a) }}" class="btn">Éditer</a>

                    <form method="POST" action="{{ route('admin.authors.toggle', $a) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn">
                        {{ ($a->is_active ?? true) ? 'Désactiver' : 'Activer' }}
                      </button>
                    </form>

                    <form method="POST" action="{{ route('admin.authors.destroy', $a) }}" style="display:inline"
                          onsubmit="return confirm('Supprimer cet auteur ?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="muted">Aucun auteur pour le moment.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    @if(method_exists($authors,'links'))
      <div class="mt-3">{{ $authors->links() }}</div>
    @endif
  </div>

</div>
@endsection
