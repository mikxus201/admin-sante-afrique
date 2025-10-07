@extends('admin.layout')
@section('title','Nouvel article')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Nouvel article</h1>
      <p class="card-sub">Renseignez les informations de l’article.</p>
    </div>
    <a href="{{ route('admin.articles.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.articles._form', ['a'=>$a, 'cats'=>$cats, 'authors'=>$authors])
@endsection
