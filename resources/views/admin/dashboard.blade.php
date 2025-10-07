@extends('layouts.admin')

@section('title','Tableau de bord')

@section('content')

{{-- Fil d’Ariane (harmonisé comme les autres pages) --}}
<nav class="mb-3 small">
  <a href="{{ route('admin.dashboard') }}" class="muted">← Back-office</a>
  <span class="muted mx-2">/</span>
  <span>Tableau de bord</span>
</nav>

{{-- 2ᵉ barre (gris clair avec séparateurs) --}}
<div class="container py-4">
  <div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Titre --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Tableau de bord</h1>
      <div class="text-sm text-neutral-500">Synthèse & actions rapides</div>
    </div>

    {{-- Cartes KPI (affichées seulement si $kpis existe) --}}
    @isset($kpis)
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($kpis as $k)
          <div class="rounded border bg-white p-4">
            <div class="text-sm text-neutral-500">{{ $k['title'] }}</div>
            <div class="mt-1 text-3xl font-semibold">{{ $k['value'] }}</div>
            @isset($k['subtle'])
              <div class="mt-1 text-sm text-neutral-500">{{ $k['subtle'] }}</div>
            @endisset
          </div>
        @endforeach
      </div>
    @endisset

    {{-- Actions rapides --}}
    <div class="rounded border bg-white p-4 mb-8">
      <div class="font-medium mb-3">Actions rapides</div>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('admin.articles.create') }}" class="px-3 py-2 rounded bg-neutral-900 text-white text-center">Nouvel article</a>
        <a href="{{ route('admin.issues.create') }}" class="px-3 py-2 rounded border text-center">Nouveau numéro</a>
        <a href="{{ route('admin.categories.create') }}" class="px-3 py-2 rounded border text-center">Nouvelle catégorie</a>
        <a href="{{ route('admin.authors.create') }}" class="px-3 py-2 rounded border text-center">Nouvel auteur</a>
        <a href="{{ route('admin.plans.create') }}" class="px-3 py-2 rounded border text-center">Créer une offre</a>
        <a href="{{ route('admin.help-items.create') }}" class="px-3 py-2 rounded border text-center">Nouveau bloc d’aide</a>
      </div>
    </div>

    @php
      // Récupérations "tolérantes" selon ce que renvoie ton contrôleur
      $latestArticles = (isset($lists) && isset($lists['latest_articles'])) ? $lists['latest_articles'] : ($latest_articles ?? collect());
      $latestIssues   = (isset($lists) && isset($lists['latest_issues']))   ? $lists['latest_issues']   : ($latest_issues   ?? collect());
      $latestPlans    = (isset($lists) && isset($lists['latest_plans']))    ? $lists['latest_plans']    : ($latest_plans    ?? collect());
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Derniers articles --}}
      <div class="rounded border bg-white overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b bg-neutral-50">
          <div class="font-medium">Derniers articles</div>
          <a href="{{ route('admin.articles.index') }}" class="text-sm text-neutral-600 hover:underline">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-neutral-100">
              <tr>
                <th class="px-3 py-2 text-left">Titre</th>
                <th class="px-3 py-2 text-left">Publié</th>
                <th class="px-3 py-2 text-left">À la une</th>
                <th class="px-3 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestArticles as $a)
                <tr class="border-t">
                  <td class="px-3 py-2 max-w-[380px] truncate font-medium">{{ $a->title }}</td>
                  <td class="px-3 py-2">
                    @if(!empty($a->published_at))
                      <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">
                        {{ \Illuminate\Support\Carbon::parse($a->published_at)->format('d/m/Y') }}
                      </span>
                    @else
                      <span class="inline-flex rounded-full border px-2 py-0.5 text-xs">Brouillon</span>
                    @endif
                  </td>
                  <td class="px-3 py-2">{{ ($a->is_featured ?? $a->featured ?? false) ? 'Oui' : 'Non' }}</td>
                  <td class="px-3 py-2">
                    <div class="flex items-center gap-2 justify-end">
                      <a href="{{ route('admin.articles.edit', $a) }}" class="px-2 py-1 rounded border">Éditer</a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-neutral-600">Aucun article…</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Derniers numéros --}}
      <div class="rounded border bg-white overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b bg-neutral-50">
          <div class="font-medium">Derniers numéros</div>
          <a href="{{ route('admin.issues.index') }}" class="text-sm text-neutral-600 hover:underline">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-neutral-100">
              <tr>
                <th class="px-3 py-2 text-left">#</th>
                <th class="px-3 py-2 text-left">Date</th>
                <th class="px-3 py-2 text-left">Publié</th>
                <th class="px-3 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestIssues as $i)
                <tr class="border-t">
                  <td class="px-3 py-2">{{ $i->number }}</td>
                  <td class="px-3 py-2">{{ $i->date ?? '—' }}</td>
                  <td class="px-3 py-2">{{ $i->is_published ? 'Oui' : 'Non' }}</td>
                  <td class="px-3 py-2">
                    <div class="flex items-center gap-2 justify-end">
                      <a href="{{ route('admin.issues.edit', $i->id ?? $i) }}" class="px-2 py-1 rounded border">Éditer</a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-neutral-600">Aucun numéro…</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Dernières offres d’abonnement --}}
      <div class="rounded border bg-white overflow-hidden lg:col-span-2">
        <div class="flex items-center justify-between px-4 py-3 border-b bg-neutral-50">
          <div class="font-medium">Offres d’abonnement (récentes)</div>
          <a href="{{ route('admin.plans.index') }}" class="text-sm text-neutral-600 hover:underline">Voir toutes les offres</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-neutral-100">
              <tr>
                <th class="px-3 py-2 text-left">Nom</th>
                <th class="px-3 py-2 text-left">Slug</th>
                <th class="px-3 py-2 text-left">Prix (FCFA)</th>
                <th class="px-3 py-2 text-left">Publié</th>
                <th class="px-3 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestPlans as $p)
                <tr class="border-t">
                  <td class="px-3 py-2 font-medium">{{ $p->name }}</td>
                  <td class="px-3 py-2 text-neutral-600">{{ $p->slug }}</td>
                  <td class="px-3 py-2">{{ number_format($p->price_fcfa, 0, '', ' ') }}</td>
                  <td class="px-3 py-2">{{ $p->is_published ? 'Oui' : 'Non' }}</td>
                  <td class="px-3 py-2">
                    <div class="flex items-center gap-2 justify-end">
                      <form action="{{ route('admin.plans.publish', $p->id ?? $p) }}" method="post">@csrf
                        <button class="px-2 py-1 rounded border">{{ $p->is_published ? 'Dépublier' : 'Publier' }}</button>
                      </form>
                      <a href="{{ route('admin.plans.edit', $p->id ?? $p) }}" class="px-2 py-1 rounded border">Éditer</a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="px-3 py-6 text-center text-neutral-600">Aucune offre…</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
