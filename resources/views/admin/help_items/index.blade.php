@extends('layouts.admin')
@section('title','Besoin d’aide ?')

@section('content')
<div class="container py-4">

  {{-- Fil d’Ariane --}}
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span>
    <span>Entrées “Besoin d’aide ?”</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Entrées “Besoin d’aide ?”</h1>
    <a href="{{ route('admin.help-items.create') }}" class="btn btn-success">+ Nouveau bloc</a>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>Clé</th>
            <th>Titre</th>
            <th>Groupe</th>
            <th>Ordre</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $i)
            <tr>
              <td><span class="muted" style="font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace">{{ $i->key }}</span></td>
              <td class="text-truncate" style="max-width:420px">{{ $i->title }}</td>
              <td>{{ $i->group ?? '—' }}</td>
              <td>{{ $i->position ?? $i->order ?? '—' }}</td>
              <td>{{ $i->is_published ? 'Publiée' : 'Brouillon' }}</td>
              <td class="text-end">
                <div class="flex" style="justify-content:flex-end;gap:.5rem">
                  @if (Route::has('admin.help-items.up'))
                    <form method="POST" action="{{ route('admin.help-items.up', $i) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn" title="Monter">↑</button>
                    </form>
                  @endif

                  @if (Route::has('admin.help-items.down'))
                    <form method="POST" action="{{ route('admin.help-items.down', $i) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn" title="Descendre">↓</button>
                    </form>
                  @endif

                  @if (Route::has('admin.help-items.publish'))
                    <form method="POST" action="{{ route('admin.help-items.publish', $i) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn">{{ $i->is_published ? 'Dépublier' : 'Publier' }}</button>
                    </form>
                  @endif

                  <a href="{{ route('admin.help-items.edit', $i) }}" class="btn">Éditer</a>

                  <form method="POST" action="{{ route('admin.help-items.destroy', $i) }}"
                        style="display:inline" onsubmit="return confirm('Supprimer ce bloc ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="muted" style="text-align:center;padding:1rem 0">
                Aucun bloc…
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($items,'links'))
      <div class="mt-3">{{ $items->withQueryString()->links() }}</div>
    @endif
  </div>

</div>
@endsection
