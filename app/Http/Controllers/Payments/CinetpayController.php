<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\{Plan, Subscription, Invoice, User};
use App\Services\CinetpayClient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CinetpayController extends Controller
{
    public function __construct(private CinetpayClient $cp) {}

    /** GET/POST /pay/cinetpay/start?plan_id=&plan_slug= */
    public function start(Request $r)
    {
        $plan = Plan::query()
            ->when($r->filled('plan_id'), fn($q) => $q->whereKey($r->integer('plan_id')))
            ->when($r->filled('plan_slug'), fn($q) => $q->orWhere('slug', $r->string('plan_slug')))
            ->firstOrFail();

        $userId = optional($r->user())->id ?? 0;

        $tx = 'SA-'.$userId.'-'.$plan->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $payload = [
            'transaction_id' => $tx,
            'amount'         => (int) $plan->price_fcfa,
            'currency'       => env('CINETPAY_CURRENCY', 'XOF'),
            'description'    => 'Abonnement '.$plan->name,
            'notify_url'     => route('pay.cinetpay.notify'),
            'return_url'     => route('pay.cinetpay.return'),
            'channels'       => env('CINETPAY_CHANNELS','ALL'),
            'lang'           => env('CINETPAY_LANG','FR'),
            'metadata'       => json_encode(['user_id'=>$userId, 'plan_id'=>$plan->id]),
        ];

        $res = $this->cp->initPayment($payload);

        if (($res['code'] ?? null) !== '201') {
            \Log::warning('CinetPay init error', ['res'=>$res, 'payload'=>$payload]);
            return response('Impossible de créer le paiement', 500);
        }

        $url = $res['data']['payment_url'] ?? null;
        if (!$url) return response('URL de paiement manquante', 500);

        return redirect()->away($url);
    }

    /** GET/POST /pay/cinetpay/return : redirige vers le front (succès/échec) */
    public function return(Request $r)
    {
        $tx = (string) $r->query('transaction_id', $r->input('transaction_id', ''));
        $statusFromCp = '';
        if ($tx !== '') {
            $check = $this->cp->check($tx);
            $statusFromCp = strtoupper((string) data_get($check, 'data.status', ''));
        }

        $ok = ($statusFromCp === 'ACCEPTED');
        $to = $ok ? env('FRONT_SUCCESS_URL', url('/abonnement/retour/succes'))
                  : env('FRONT_FAIL_URL',    url('/abonnement/retour/echec'));

        // On renvoie quelques infos utiles au front
        $qs = http_build_query([
            'transaction_id' => $tx,
            'status'         => $statusFromCp ?: ($r->query('status') ?? ''),
        ]);

        return redirect()->away(rtrim($to, '/').'?'.$qs);
    }

    /** GET/POST /pay/cinetpay/notify : webhook serveur → crée/active l’abo + facture */
    public function notify(Request $r)
    {
        // (Optionnel) HMAC si tu configures CINETPAY_SECRET_KEY
        $raw  = $r->getContent();
        $hmac = $r->header('X-Token') ?: $r->header('x-token');
        if (!$this->cp->validHmac($hmac, $raw)) {
            \Log::warning('CinetPay HMAC invalide', ['ip'=>$r->ip()]);
            return response()->noContent();
        }

        $tx = (string) ($r->input('transaction_id') ?? '');
        if ($tx === '') return response()->noContent();

        $check  = $this->cp->check($tx);
        $status = strtoupper((string) data_get($check, 'data.status', ''));

        \Log::info('CinetPay notify', ['tx'=>$tx, 'status'=>$status, 'payload'=>$r->all(), 'check'=>$check]);

        if ($status !== 'ACCEPTED') {
            // on ignore les autres statuts
            return response()->noContent();
        }

        // Déduire user/plan depuis transaction_id ou metadata
        $userId = 0; $planId = 0;
        if (preg_match('/^SA-(\d+)-(\d+)-/', $tx, $m)) { $userId=(int)$m[1]; $planId=(int)$m[2]; }
        $meta = json_decode((string) data_get($check,'data.metadata','[]'), true) ?: [];
        $userId = $userId ?: (int)($meta['user_id'] ?? 0);
        $planId = $planId ?: (int)($meta['plan_id'] ?? 0);

        $user = $userId ? User::find($userId) : null;
        $plan = $planId ? Plan::find($planId) : null;
        if (!$plan) return response()->noContent();

        // Idempotence : si déjà traité, on sort
        $already = Subscription::where('payment_ref', $tx)->exists()
                || Invoice::where('number', $tx)->exists();
        if ($already) return response()->noContent();

        $start = now();
        $end   = (clone $start)->addDays(365); // adapte si besoin (mensuel, etc.)

        // Créer l’abonnement
        Subscription::create([
            'user_id'        => $user?->id,
            'plan_id'        => $plan->id,
            'status'         => 'active',
            'payment_method' => 'CINETPAY',
            'payment_ref'    => $tx,
            'starts_at'      => $start,
            'ends_at'        => $end,
        ]);

        // Créer la facture (numéro = tx pour éviter doublons)
        Invoice::create([
            'user_id'     => $user?->id,
            'number'      => $tx, // unique par nature
            'period_from' => $start->toDateString(),
            'period_to'   => $end->toDateString(),
            'amount_fcfa' => (int) $plan->price_fcfa,
            'status'      => 'paid',
        ]);

        return response()->noContent();
    }
}
