@extends('layouts.admin')
@section('title','Offres d’abonnement')

@section('content')
<div class="container py-4">
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span><span>Offres d’abonnement</span>
  </nav>

  <div class="flex between mb-3">
    <h1 class="text-xl font-bold">Offres d’abonnement</h1>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-success">+ Créer</a>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>Nom</th>
            <th>Slug</th>
            <th class="text-end">Prix (FCFA)</th>
            <th>Publié</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($plans as $p)
            <tr>
              <td>{{ $p->name }}</td>
              <td>{{ $p->slug }}</td>
              <td class="text-end">{{ isset($p->price_fcfa) ? number_format($p->price_fcfa,0,',',' ') : '—' }}</td>
              <td>{{ $p->is_published ? 'Oui' : 'Non' }}</td>
              <td class="text-end">
                <div class="flex" style="justify-content:flex-end;gap:.5rem">
                  @if (Route::has('admin.plans.publish'))
                    <form method="POST" action="{{ route('admin.plans.publish', $p) }}" style="display:inline">
                      @csrf
                      <button type="submit" class="btn">{{ $p->is_published ? 'Retirer' : 'Publier' }}</button>
                    </form>
                  @endif
                  <a href="{{ route('admin.plans.edit', $p) }}" class="btn">Éditer</a>
                  <form method="POST" action="{{ route('admin.plans.destroy', $p) }}"
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

    @if(method_exists($plans,'links'))
      <div class="mt-3">{{ $plans->links() }}</div>
    @endif
  </div>
</div>
@endsection
