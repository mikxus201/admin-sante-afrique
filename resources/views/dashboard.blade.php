@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
{{-- Force la barre noire ici au cas où le layout ne l’injecte pas sur cette page --}}

<div class="container py-4">

  <h1 class="text-xl font-bold mb-3">Tableau de bord</h1>

  {{-- ====== KPI / Tuiles ====== --}}
  <div class="grid grid-4 mb-4">
    @foreach($kpis as $kpi)
      <div class="card p-3">
        <div class="muted text-sm">{{ $kpi['title'] }}</div>
        <div class="text-2xl font-bold">{{ number_format($kpi['value'], 0, ',', ' ') }}</div>
        @if(!empty($kpi['subtle']))
          <div class="muted text-sm">{{ $kpi['subtle'] }}</div>
        @endif
      </div>
    @endforeach
  </div>

  {{-- ====== Actions rapides ====== --}}
  <div class="card p-3 mb-4">
    <div class="flex" style="flex-wrap:wrap;gap:.5rem">
      <a href="{{ route('admin.issues.create') }}" class="btn">Nouveau numéro</a>
      <a href="{{ route('admin.categories.create') }}" class="btn">Nouvelle catégorie</a>
      <a href="{{ route('admin.authors.create') }}" class="btn">Nouvel auteur</a>
      <a href="{{ route('admin.rubrics.index') }}" class="btn">Rubriques</a>
      <a href="{{ route('admin.plans.create') }}" class="btn">Créer une offre</a>
      <a href="{{ route('admin.help-items.create') }}" class="btn">Nouveau bloc d’aide</a>
    </div>
  </div>

  {{-- ====== Abonnements (si table détectée) ====== --}}
  @if(!empty($subsStats['table']))
    <div class="grid grid-4 mb-4">
      <div class="card p-3">
        <div class="muted text-sm">Total abonnements</div>
        <div class="text-2xl font-bold">{{ number_format($subsStats['total'], 0, ',', ' ') }}</div>
      </div>
      @if(!is_null($subsStats['active']))
        <div class="card p-3">
          <div class="muted text-sm">Actifs</div>
          <div class="text-2xl font-bold">{{ number_format($subsStats['active'], 0, ',', ' ') }}</div>
        </div>
      @endif
      @if(!is_null($subsStats['ending_30']))
        <div class="card p-3">
          <div class="muted text-sm">Échéance ≤ 30 jours</div>
          <div class="text-2xl font-bold">{{ number_format($subsStats['ending_30'], 0, ',', ' ') }}</div>
        </div>
      @endif
      @if(!is_null($subsStats['expired']))
        <div class="card p-3">
          <div class="muted text-sm">Expirés</div>
          <div class="text-2xl font-bold">{{ number_format($subsStats['expired'], 0, ',', ' ') }}</div>
        </div>
      @endif
    </div>

    @if(!empty($lists['subs_per_plan']))
      <div class="card p-3 mb-4">
        <div class="font-bold mb-2">Abonnements par offre</div>
        <div class="table-responsive">
          <table>
            <thead style="background:#f9fafb">
              <tr>
                <th>Offre</th>
                <th class="text-end">Total</th>
                <th class="text-end">Actifs</th>
              </tr>
            </thead>
            <tbody>
              @foreach($lists['subs_per_plan'] as $row)
                <tr>
                  <td>{{ $row['plan'] }}</td>
                  <td class="text-end">{{ number_format($row['total'], 0, ',', ' ') }}</td>
                  <td class="text-end">{{ is_null($row['active']) ? '—' : number_format($row['active'], 0, ',', ' ') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
  @endif

  {{-- ====== Derniers articles ====== --}}
  <div class="card p-3 mb-4">
    <div class="flex between mb-2">
      <div class="font-bold">Derniers articles</div>
      <a class="link" href="{{ route('admin.articles.index') }}">Voir tout</a>
    </div>
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>#</th>
            <th>Titre</th>
            <th>Publié</th>
            <th>À la une</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($lists['latest_articles'] as $a)
            <tr>
              <td>{{ $a->id }}</td>
              <td class="text-truncate" style="max-width:560px">{{ $a->title }}</td>
              <td>{{ $a->published_at ? $a->published_at->format('d/m/Y') : '—' }}</td>
              <td>{{ $a->is_featured ? 'Oui' : 'Non' }}</td>
              <td class="text-end">
                <a href="{{ route('admin.articles.edit', $a) }}" class="btn">Éditer</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- ====== Derniers numéros ====== --}}
  <div class="card p-3">
    <div class="flex between mb-2">
      <div class="font-bold">Derniers numéros</div>
      <a class="link" href="{{ route('admin.issues.index') }}">Voir tout</a>
    </div>
    <div class="table-responsive">
      <table>
        <thead style="background:#f9fafb">
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Publié</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($lists['latest_issues'] as $i)
            <tr>
              <td>N° {{ $i->number }}</td>
              <td>{{ $i->date ? \Carbon\Carbon::parse($i->date)->format('d/m/Y') : '—' }}</td>
              <td>{{ $i->is_published ? 'Oui' : 'Non' }}</td>
              <td class="text-end">
                <a href="{{ route('admin.issues.edit', $i) }}" class="btn">Éditer</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
