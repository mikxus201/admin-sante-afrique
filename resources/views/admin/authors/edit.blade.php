@extends('admin.layout')
@section('title','Modifier un auteur')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Modifier l’auteur</h1>
      <p class="card-sub">{{ $a->name }}</p>
    </div>
    <a href="{{ route('admin.authors.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.authors._form', ['a'=>$a])
@endsection
