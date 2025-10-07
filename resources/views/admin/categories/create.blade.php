@extends('admin.layout')
@section('title','Créer une catégorie')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Créer une catégorie</h1>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.categories._form', ['c'=>$c])
@endsection
