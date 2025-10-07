@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 space-y-6">
  <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:underline">← Retour</a>

  {{-- ================== PROFIL ================== --}}
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-bold mb-3">Profil</h2>
    <div class="grid md:grid-cols-2 gap-4 text-sm">
      <div><span class="text-neutral-500">Nom complet :</span> {{ $user->full_name }}</div>
      <div><span class="text-neutral-500">Email :</span> {{ $user->email }}</div>
      <div><span class="text-neutral-500">Téléphone :</span> {{ $user->phone }}</div>
      <div><span class="text-neutral-500">Pays :</span> {{ $user->country }}</div>
      <div><span class="text-neutral-500">Genre :</span> {{ $user->gender }}</div>
    </div>
  </div>

  {{-- ================== ABONNEMENTS ================== --}}
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-bold mb-3">Abonnements</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-neutral-100">
          <tr>
            <th class="text-left px-3 py-2">Période</th>
            <th class="text-left px-3 py-2">Offre</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2">Paiement</th>
          </tr>
        </thead>
        <tbody>
          @forelse($user->subscriptions->sortByDesc('starts_at') as $s)
            @php
              // Styles compacts pour badges
              $badgeBase = 'display:inline-block;padding:.15rem .45rem;border-radius:6px;font-size:11px;font-weight:600;';
              $subMap = [
                'active'    => 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;',
                'pending'   => 'background:#fff7ed;color:#9a3412;border:1px solid #ffedd5;',
                'failed'    => 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;',
                'cancelled' => 'background:#e5e7eb;color:#374151;border:1px solid #d1d5db;',
                'expired'   => 'background:#f3f4f6;color:#4b5563;border:1px solid #e5e7eb;',
              ];
              $subStyle = $subMap[$s->status] ?? 'background:#f3f4f6;color:#111827;border:1px solid #e5e7eb;';
            @endphp
            <tr class="border-t">
              <td class="px-3 py-2">
                {{ optional($s->starts_at)->format('d/m/Y') }} → {{ optional($s->ends_at)->format('d/m/Y') }}
                @if($s->is_active)
                  <span class="ml-2 inline-block rounded bg-green-600 text-white text-xs px-2 py-0.5">Actif</span>
                @endif
              </td>

              <td class="px-3 py-2">
                {{ $s->plan->name ?? '—' }}
                @if($s->plan)
                  ({{ number_format($s->plan->price_fcfa, 0, ',', ' ') }} FCFA)
                @endif
              </td>

              <td class="px-3 py-2">
                <span style="{{ $badgeBase }}{{ $subStyle }}">{{ $s->status }}</span>
              </td>

              <td class="px-3 py-2">
                {{ $s->payment_method }}
                @if($s->payment_ref)
                  <span class="text-neutral-400">·</span>
                  <code class="text-xs" style="background:#f3f4f6;padding:.1rem .3rem;border-radius:4px;border:1px solid #e5e7eb;">
                    {{ $s->payment_ref }}
                  </code>
                @endif
              </td>
            </tr>
          @empty
            <tr><td class="px-3 py-3 text-neutral-500" colspan="4">Aucun abonnement.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ================== FACTURES ================== --}}
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-bold mb-3">Factures</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-neutral-100">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">Période</th>
            <th class="text-left px-3 py-2">Montant</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2">PDF</th>
          </tr>
        </thead>
        <tbody>
          @forelse($user->invoices->sortByDesc('period_to') as $inv)
            @php
              $badgeBase = 'display:inline-block;padding:.15rem .45rem;border-radius:6px;font-size:11px;font-weight:600;';
              $invMap = [
                'paid'     => 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;',
                'unpaid'   => 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;',
                'refunded' => 'background:#e0f2fe;color:#075985;border:1px solid #bae6fd;',
              ];
              $invStyle = $invMap[$inv->status] ?? 'background:#f3f4f6;color:#111827;border:1px solid #e5e7eb;';
            @endphp
            <tr class="border-t">
              <td class="px-3 py-2">#{{ $inv->number }}</td>

              <td class="px-3 py-2">
                {{ optional($inv->period_from)->format('d/m/Y') }} → {{ optional($inv->period_to)->format('d/m/Y') }}
              </td>

              <td class="px-3 py-2">{{ number_format($inv->amount_fcfa, 0, ',', ' ') }} FCFA</td>

              <td class="px-3 py-2">
                <span style="{{ $badgeBase }}{{ $invStyle }}">{{ $inv->status }}</span>
              </td>

              <td class="px-3 py-2">
                @if($inv->pdf_url)
                  <a class="text-blue-600 hover:underline" href="{{ $inv->pdf_url }}" target="_blank">Télécharger</a>
                @else
                  <span class="text-neutral-500">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td class="px-3 py-3 text-neutral-500" colspan="5">Aucune facture.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
