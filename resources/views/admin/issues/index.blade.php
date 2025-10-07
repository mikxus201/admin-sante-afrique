@extends('layouts.admin')
@section('title','Magazines')

@section('content')
<div class="container py-4">
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span><span>Magazines</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Numéros du magazine</h1>
    <a href="{{ route('admin.issues.create') }}" class="btn btn-success">+ Créer</a>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>N°</th>
            <th>Titre</th>
            <th>Date</th>
            <th>Publié</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($issues as $i)
            <tr>
              <td>N°{{ $i->number }}</td>
              <td>{{ $i->title ?? ('Santé Afrique N°'.$i->number) }}</td>
              <td>{{ $i->date ? \Carbon\Carbon::parse($i->date)->format('d/m/Y') : '—' }}</td>
              <td>{{ $i->is_published ? 'Oui' : 'Non' }}</td>
              <td class="text-end">
                <div class="flex" style="justify-content:flex-end;gap:.5rem">
                  <a href="{{ route('admin.issues.edit', $i) }}" class="btn">Éditer</a>
                  <form method="POST" action="{{ route('admin.issues.destroy', $i) }}"
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

    @if(method_exists($issues,'links'))
      <div class="mt-3">{{ $issues->links() }}</div>
    @endif
  </div>
</div>
@endsection
