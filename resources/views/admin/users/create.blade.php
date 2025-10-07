@extends('admin.layout')

@section('title','Créer un utilisateur')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Créer un utilisateur</h1>
      <p class="card-sub">Renseignez les informations du compte.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.users._form', ['u' => $u, 'roles' => $roles])
@endsection
