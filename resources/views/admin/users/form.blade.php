@extends('layouts.admin')

@section('title', $u->exists ? 'Modifier un utilisateur' : 'Nouvel utilisateur')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">{{ $u->exists ? 'Modifier un utilisateur' : 'Nouvel utilisateur' }}</h1>

  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      <ul class="list-disc pl-4">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  <form method="post" action="{{ $u->exists ? route('admin.users.update',$u) : route('admin.users.store') }}"
        class="rounded border bg-white p-4 space-y-4">
    @csrf
    @if($u->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Nom</span>
        <input name="name" value="{{ old('name',$u->name) }}" required class="mt-1 w-full rounded border px-3 py-2">
      </label>
      <label class="block">
        <span class="text-sm">Email</span>
        <input type="email" name="email" value="{{ old('email',$u->email) }}" required class="mt-1 w-full rounded border px-3 py-2">
      </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Rôle</span>
        <select name="role" class="mt-1 w-full rounded border px-3 py-2">
          @foreach($roles as $role)
            <option value="{{ $role }}" @selected(old('role',$u->role)===$role)>{{ ucfirst($role) }}</option>
          @endforeach
        </select>
      </label>

      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active',$u->is_active ?? true) ? 'checked' : '' }}>
        <span>Actif</span>
      </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="block">
        <span class="text-sm">Mot de passe {{ $u->exists ? '(laisser vide pour ne pas changer)' : '' }}</span>
        <input type="password" name="password" class="mt-1 w-full rounded border px-3 py-2">
      </label>
      <label class="block">
        <span class="text-sm">Confirmation</span>
        <input type="password" name="password_confirmation" class="mt-1 w-full rounded border px-3 py-2">
      </label>
    </div>

    <div class="flex items-center gap-3">
      <button class="px-4 py-2 rounded border">Enregistrer</button>
      <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">Annuler</a>
    </div>
  </form>
</div>
@endsection
