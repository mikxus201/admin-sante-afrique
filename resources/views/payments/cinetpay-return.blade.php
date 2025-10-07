@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12 text-center">
  <h1 class="text-2xl font-bold mb-4">Merci 🙏</h1>
  <p class="mb-2">Votre paiement est en cours de vérification.</p>
  @if($transaction_id)
    <p class="text-sm text-neutral-500">Référence: <code>{{ $transaction_id }}</code></p>
  @endif
  <a class="btn mt-6" href="{{ route('admin.users.show', auth()->id()) }}">Voir mon compte</a>
</div>
@endsection
