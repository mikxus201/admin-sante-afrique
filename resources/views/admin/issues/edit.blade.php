@extends('admin.layout')
@section('title','Modifier un numéro')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="page-title">Modifier le numéro</h1>
      <p class="card-sub">N°{{ $issue->number }}</p>
    </div>
    <a href="{{ route('admin.issues.index') }}" class="btn">Retour</a>
  </div>

  @include('admin.issues._form', ['issue' => $issue])
@endsection
