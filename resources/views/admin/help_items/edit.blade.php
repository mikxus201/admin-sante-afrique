@extends('admin.layout')
@section('title','Modifier un bloc d’aide')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">Modifier un bloc</h1>
  @include('admin.help_items.partials.form', ['item' => $item, 'route' => route('admin.help-items.update',$item), 'method' => 'PUT'])
</div>
@endsection
