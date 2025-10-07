@extends('admin.layout')

@section('title','Modifier un utilisateur')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Modifier l’utilisateur</h1>
      <p class="card-sub">{{ $u->name }} — {{ $u->email }}</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.users._form', ['u' => $u, 'roles' => $roles])

  {{-- Actions rapides (désactiver/supprimer) --}}
  <div class="mt-6 flex items-center gap-3">
    <form method="POST" action="{{ route('admin.users.active', $u) }}">
      @csrf
      <button class="btn">{{ $u->is_active ? 'Désactiver' : 'Activer' }}</button>
    </form>

    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Supprimer cet utilisateur ?')">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Supprimer</button>
    </form>
  </div>
@endsection
