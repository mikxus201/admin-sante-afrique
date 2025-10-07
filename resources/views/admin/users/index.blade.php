@extends('layouts.admin') 

@section('title', 'Utilisateurs')

@section('content')
<div class="container py-4">

  {{-- Fil d’Ariane --}}
  <nav class="mb-3 small">
    <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
    <span class="muted mx-2">/</span>
    <span>Utilisateurs</span>
  </nav>

  <div class="flex between mb-3" style="align-items:center; gap:1rem;">
    <h1 class="text-xl font-bold">Utilisateurs</h1>
    <div class="flex" style="gap:.5rem;">
      <a href="{{ route('admin.users.export.csv', request()->query()) }}" class="btn">Exporter CSV</a>
      <a href="{{ route('admin.users.create') }}" class="btn btn-success">+ Créer</a>
    </div>
  </div>

  {{-- Messages flash --}}
  @if(session('ok'))      <div class="card p-3 mb-3">✔ {{ session('ok') }}</div> @endif
  @if(session('success')) <div class="card p-3 mb-3">✔ {{ session('success') }}</div> @endif
  @if(session('ko'))      <div class="card p-3 mb-3" style="border-color:#fecaca;background:#fef2f2">✖ {{ session('ko') }}</div> @endif

  {{-- Filtres --}}
  <div class="card p-3 mb-3">
    <form method="get" class="grid" style="grid-template-columns: repeat(6, minmax(0,1fr)); gap:.75rem;">
      <input type="text" name="q" value="{{ old('q', $q ?? request('q')) }}" placeholder="Nom, prénoms, email, téléphone…" class="form-control">

      {{-- Rôle (si fourni depuis le contrôleur) --}}
      <select name="role" class="form-control">
        <option value="">— Rôle —</option>
        @foreach(($roles ?? []) as $role)
          <option value="{{ $role }}" @selected(($role ?? '') === ($role ?? request('role')))>{{ $role }}</option>
        @endforeach
      </select>

      {{-- Pays (si liste transmise) --}}
      <select name="country" class="form-control">
        <option value="">— Pays —</option>
        @foreach(($countries ?? []) as $c)
          <option value="{{ $c }}" @selected($c === request('country'))>{{ $c }}</option>
        @endforeach
      </select>

      {{-- Plan (id ou slug) --}}
      <select name="plan_id" class="form-control">
        <option value="">— Plan (ID) —</option>
        @foreach(($plans ?? []) as $p)
          <option value="{{ $p->id }}" @selected((string)$p->id === (string)request('plan_id'))>
            {{ $p->name }}
          </option>
        @endforeach
      </select>

      <input type="text" name="plan" value="{{ request('plan') }}" placeholder="Plan (slug)" class="form-control">

      <label class="form-control" style="display:flex;align-items:center;gap:.5rem;">
        <input type="checkbox" name="active_only" value="1" {{ request('active_only') ? 'checked' : '' }}>
        <span>Abonnement actif</span>
      </label>

      <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="Du">
      <input type="date" name="to"   value="{{ request('to')   }}" class="form-control" placeholder="Au">

      <div style="grid-column: span 6 / span 6; display:flex; gap:.5rem; justify-content:flex-end;">
        <a href="{{ route('admin.users.index') }}" class="btn">Réinitialiser</a>
        <button class="btn btn-primary" type="submit">Filtrer</button>
      </div>
    </form>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th style="width:56px">#</th>
            <th>Utilisateur</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Pays</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Abonnement en cours</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            @php $sub = $u->currentSubscription ?? null; @endphp
            <tr>
              <td>{{ $u->id }}</td>
              <td>{{ $u->full_name ?? $u->display_name }}</td>
              <td><a href="mailto:{{ $u->email }}" class="link">{{ $u->email }}</a></td>
              <td>{{ $u->phone }}</td>
              <td>{{ $u->country }}</td>
              <td>
                <form method="POST" action="{{ route('admin.users.role', $u) }}">
                  @csrf
                  @method('PATCH')
                  <select name="role" onchange="this.form.submit()" class="form-control" style="padding:.3rem .5rem;border:1px solid #e5e7eb;border-radius:6px">
                    @php
                      // Utilise la liste passée par le contrôleur si dispo; sinon fallback basique
                      $roleOptions = $roles ?? ['viewer','moderator','editor','admin'];
                    @endphp
                    @foreach($roleOptions as $value)
                      <option value="{{ $value }}" @selected($u->role === $value)>{{ $value }}</option>
                    @endforeach
                  </select>
                </form>
              </td>
              <td>
                @if(($u->is_active ?? true))
                  <span class="badge" style="background:#dcfce7;color:#166534;border-radius:6px;padding:.2rem .4rem;">Actif</span>
                @else
                  <span class="badge" style="background:#fee2e2;color:#991b1b;border-radius:6px;padding:.2rem .4rem;">Désactivé</span>
                @endif
              </td>
              <td>
                @if($sub)
                  <div class="small" style="line-height:1.2;">
                    <div><strong>{{ $sub->plan->name ?? '—' }}</strong></div>
                    <div>Du {{ optional($sub->starts_at)->format('d/m/Y') }} au {{ optional($sub->ends_at)->format('d/m/Y') }}</div>
                    <div>
                      @if($sub->status === 'active')
                        <span class="badge" style="background:#e0f2fe;color:#075985;border-radius:6px;padding:.1rem .35rem;">{{ $sub->status }}</span>
                      @else
                        <span class="badge" style="background:#f3f4f6;color:#111827;border-radius:6px;padding:.1rem .35rem;">{{ $sub->status }}</span>
                      @endif
                    </div>
                  </div>
                @else
                  <span class="muted">—</span>
                @endif
              </td>
              <td class="text-end">
                <div class="flex" style="justify-content:flex-end; gap:.5rem; flex-wrap:wrap;">
                  <a href="{{ route('admin.users.show', $u) }}" class="btn">Fiche</a>
                  <a href="{{ route('admin.users.edit', $u) }}" class="btn">Modifier</a>

                  <form method="POST" action="{{ route('admin.users.toggle', $u) }}" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn">
                      {{ ($u->is_active ?? true) ? 'Désactiver' : 'Activer' }}
                    </button>
                  </form>

                  <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center muted py-3">Aucun utilisateur trouvé.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($users,'links'))
      <div class="mt-3">
        {{ $users->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
