<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Facture {{ $inv->number }}</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #111; font-size: 12px; }
    .wrap { width: 100%; padding: 24px; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .brand h1 { margin: 0; font-size: 18px; color: #0a58ca; }
    .muted { color:#555; }
    .box { border: 1px solid #ddd; padding: 12px; border-radius: 6px; }
    .mt-2{ margin-top:8px; } .mt-3{ margin-top:12px; } .mt-4{ margin-top:16px; } .mb-2{ margin-bottom:8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
    .total { font-weight: bold; }
    .right { text-align: right; }
    .small { font-size: 11px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div class="brand">
        <h1>santé afrique</h1>
        <div class="small muted">Se comprendre, mieux vivre.</div>
      </div>
      <div class="box">
        <div><strong>Facture</strong> : {{ $inv->number }}</div>
        <div class="muted small">Émise le : {{ now()->format('d/m/Y') }}</div>
        <div class="muted small">Statut : {{ strtoupper($inv->status) }}</div>
      </div>
    </div>

    <table>
      <tr>
        <td class="box">
          <div class="mb-2"><strong>Facturer à</strong></div>
          <div>{{ $inv->user?->display_name ?? 'Client' }}</div>
          @if($inv->user?->email)<div class="small muted">{{ $inv->user->email }}</div>@endif
          @if($inv->user?->phone)<div class="small muted">{{ $inv->user->phone }}</div>@endif
          @if($inv->user?->country)<div class="small muted">{{ $inv->user->country }}</div>@endif
        </td>
        <td class="box">
          <div class="mb-2"><strong>Période</strong></div>
          <div>Du {{ optional($inv->period_from)->format('d/m/Y') }} au {{ optional($inv->period_to)->format('d/m/Y') }}</div>
        </td>
      </tr>
    </table>

    <div class="mt-4">
      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th class="right">Montant</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Abonnement — accès aux contenus Santé Afrique</td>
            <td class="right">{{ number_format($inv->amount_fcfa, 0, ',', ' ') }} FCFA</td>
          </tr>
          <tr>
            <td class="total">Total à payer</td>
            <td class="right total">{{ number_format($inv->amount_fcfa, 0, ',', ' ') }} FCFA</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="small muted mt-4">
      Merci pour votre soutien à l’information santé en Afrique.  
      Cette facture a été générée automatiquement — Réf. {{ $inv->number }}.
    </p>
  </div>
</body>
</html>
