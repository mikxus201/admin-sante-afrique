@extends('admin.layout')
@section('title','Créer un auteur')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Créer un auteur</h1>
    </div>
    <a href="{{ route('admin.authors.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.authors._form', ['a'=>$a])
@endsection
