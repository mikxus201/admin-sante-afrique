@extends('admin.layout')
@section('title','Nouveau numéro')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Nouveau numéro</h1>
    </div>
    <a href="{{ route('admin.issues.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.issues._form', ['issue' => $issue])
@endsection
