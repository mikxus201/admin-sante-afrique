@extends('layouts.app')
@section('title','Articles')

@section('content')
    <h1 class="mb-6">Tous les articles</h1>

    <form method="get" class="mb-6">
        <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher..."
               class="border rounded px-3 py-2 w-full md:w-80">
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($items as $item)
            @include('articles._card', ['item' => $item])
        @empty
            <p>Aucun article pour le moment.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endsection
