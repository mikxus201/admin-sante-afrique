<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MeController extends Controller
{
    /** GET /api/me */
    public function me(Request $request)
    {
        $u = $request->user();
        [$nom, $prenoms] = $this->splitName($u->name ?? '');

        return response()->json([
            'data' => [
                'id'         => $u->id,
                'name'       => $u->name,
                'nom'        => $nom,        // <-- pour le front (séparé)
                'prenoms'    => $prenoms,    // <-- pour le front (séparé)
                'email'      => $u->email,
                'phone'      => $u->phone ?? null,
                'avatar'     => $u->avatar ?? null,
                'created_at' => $u->created_at?->toISOString(),
                 // ✅ AJOUTS
                'email_verified_at' => $u->email_verified_at?->toISOString(),
                'email_verified'    => (bool) $u->email_verified_at,
                'newsletter' => $this->isNewslettersEnabled($u),
            ],
        ]);
    }
        public function update(Request $request)
    {
       $u = $request->user();
       $data = $request->validate([
        'name'      => ['sometimes','string','max:150'],
        'nom'       => ['sometimes','string','max:100'],
        'prenoms'   => ['sometimes','string','max:150'],
        'phone'     => ['sometimes','string','max:30'],
        'email'     => ['sometimes','email','max:150', \Illuminate\Validation\Rule::unique('users','email')->ignore($u->id)],
        'avatar'    => ['sometimes','string','max:255'],
    ]);

    if (!empty($data['nom']) || !empty($data['prenoms'])) {
        $u->name = trim(($data['nom'] ?? '').' '.($data['prenoms'] ?? ''));
    }
    if (!empty($data['name']))   $u->name = $data['name'];
    if (array_key_exists('phone', $data))  $u->phone  = $data['phone'];
    if (array_key_exists('email', $data))  $u->email  = $data['email'];
    if (array_key_exists('avatar', $data)) $u->avatar = $data['avatar'];

    $u->save();

        // renvoie nom/prenoms pour pré-remplir le front
         [$nom, $prenoms] = explode(' ', trim($u->name ?? ''), 2) + ['', ''];
        return response()->json([
        'ok'   => true,
        'data' => [
            'id'      => $u->id,
            'name'    => $u->name,
            'nom'     => $nom,
            'prenoms' => $prenoms,
            'email'   => $u->email,
            'phone'   => $u->phone,
            'avatar'  => $u->avatar ?? null,
          ],
      ]);
   }

    /** Petit helper pour séparer un "Nom Prénoms" en deux champs */
    protected function splitName(string $full): array
    {
        $full = trim(preg_replace('/\s+/', ' ', $full));
        if ($full === '') return ['', ''];
        // Heuristique simple : 1er token = Nom, le reste = Prénoms
        $parts = explode(' ', $full, 2);
        $nom = $parts[0] ?? '';
        $prenoms = $parts[1] ?? '';
        return [$nom, $prenoms];
    }

    /** GET /api/me/newsletters (global bool — compat) */
    public function newsletters(Request $request)
    {
        return response()->json([
            'subscribed' => $this->isNewslettersEnabled($request->user()),
        ]);
    }

    /** POST /api/me/newsletters (global toggle — compat) */
    public function toggleNewsletter(Request $request)
    {
        $u = $request->user();
        $current = $this->isNewslettersEnabled($u);
        $this->setNewslettersEnabled($u, !$current);

        return response()->json(['subscribed' => !$current]);
    }

    /** GET /api/me/newsletters (multi-thèmes) */
    public function newsletterIndex(Request $request)
    {
        $u = $request->user();

        $topics = DB::table('newsletter_topics')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $userMap = DB::table('newsletter_user')
            ->where('user_id', $u->id)
            ->pluck('subscribed', 'topic_id')
            ->all();

        $data = $topics->map(function ($t) use ($userMap) {
            $sub = array_key_exists($t->id, $userMap) ? (bool)$userMap[$t->id] : false;
            return [
                'id'         => $t->id,
                'slug'       => $t->slug,
                'name'       => $t->name,
                'subscribed' => $sub,
            ];
        })->all();

        return response()->json(['data' => $data]);
    }

    /** POST /api/me/newsletters/toggle (multi-thèmes) */
    public function newsletterToggleTopic(Request $request)
    {
        $u = $request->user();
        $data = $request->validate([
            'topic_id'   => ['required','integer','exists:newsletter_topics,id'],
            'subscribed' => ['sometimes','boolean'],
        ]);

        $existing = DB::table('newsletter_user')
            ->where('user_id', $u->id)
            ->where('topic_id', $data['topic_id'])
            ->first();

        $newValue = $data['subscribed'] ?? !($existing?->subscribed ?? false);

        DB::table('newsletter_user')->updateOrInsert(
            ['user_id' => $u->id, 'topic_id' => $data['topic_id']],
            [
                'subscribed'      => $newValue,
                'unsubscribed_at' => $newValue ? null : now(),
                'updated_at'      => now(),
                'created_at'      => $existing?->created_at ?? now(),
            ]
        );

        return response()->json([
            'ok'         => true,
            'topic_id'   => (int)$data['topic_id'],
            'subscribed' => (bool)$newValue,
        ]);
    }

    /** GET /api/me/invoices */
    public function invoices(Request $request)
    {
        $u = $request->user();
        $items = [];

        if (class_exists(\App\Models\Invoice::class)) {
            $table = (new \App\Models\Invoice)->getTable();

            $hasPeriodFrom = Schema::hasColumn($table, 'period_from');
            $hasPeriodTo   = Schema::hasColumn($table, 'period_to');
            $hasAmountFcfa = Schema::hasColumn($table, 'amount_fcfa');
            $hasPeriod     = Schema::hasColumn($table, 'period');
            $hasAmount     = Schema::hasColumn($table, 'amount');
            $hasCurrency   = Schema::hasColumn($table, 'currency');

            $select = ['id','created_at','pdf_path'];
            foreach (['number','status'] as $c) if (Schema::hasColumn($table,$c)) $select[]=$c;
            if ($hasPeriodFrom) $select[]='period_from';
            if ($hasPeriodTo)   $select[]='period_to';
            if ($hasAmountFcfa) $select[]='amount_fcfa';
            if ($hasPeriod)     $select[]='period';
            if ($hasAmount)     $select[]='amount';
            if ($hasCurrency)   $select[]='currency';

            $items = \App\Models\Invoice::where('user_id',$u->id)
                ->orderByDesc('created_at')
                ->get($select)
                ->map(function ($inv) use ($hasPeriodFrom,$hasPeriodTo,$hasAmountFcfa,$hasPeriod,$hasAmount,$hasCurrency) {
                    $period_from = $hasPeriodFrom ? ($inv->period_from?->toDateString()) : null;
                    $period_to   = $hasPeriodTo   ? ($inv->period_to?->toDateString())   : null;
                    $period      = $hasPeriod     ? ($inv->period ?? null)               : null;

                    if ($hasAmountFcfa) {
                        $amount_cfa = (int)($inv->amount_fcfa ?? 0);
                        $currency   = 'XOF';
                    } else {
                        $amount_cfa = (int)($inv->amount ?? 0);
                        $currency   = $hasCurrency ? ($inv->currency ?? 'XOF') : 'XOF';
                    }

                    return [
                        'id'           => $inv->id,
                        'number'       => $inv->number ?? ('INV-'.$inv->id),
                        'status'       => $inv->status ?? 'unpaid',
                        'period_from'  => $period_from,
                        'period_to'    => $period_to,
                        'period'       => $period,
                        'amount_cfa'   => $amount_cfa,
                        'currency'     => $currency,
                        'created_at'   => $inv->created_at?->toISOString(),
                        'pdf_url'      => route('api.me.invoices.pdf', ['id' => $inv->id]),
                    ];
                })->all();
        }

        return response()->json(['data' => $items]);
    }

    /** GET /api/me/invoices/{id}/pdf */
    public function invoicePdf(Request $request, $id)
    {
        if (class_exists(\App\Models\Invoice::class)) {
            $inv = \App\Models\Invoice::where('user_id', $request->user()->id)->find($id);
            if (!$inv) return response()->json(['message' => 'Invoice not found'], 404);

            $disk = $inv->storage_disk ?? null;
            $pdf  = $inv->pdf_path;

            if ($pdf) {
                if ($disk && Storage::disk($disk)->exists($pdf)) {
                    $stream = Storage::disk($disk)->readStream($pdf);
                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                    }, 200, [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="invoice-'.$id.'.pdf"',
                    ]);
                }
                $abs = storage_path('app/'.$pdf);
                if (is_file($abs)) {
                    return response()->file($abs, ['Content-Type'=>'application/pdf']);
                }
            }
        }

        $content = "%PDF-1.4\n1 0 obj<<>>endobj\n2 0 obj<<>>endobj\nxref\n0 3\n0000000000 65535 f \n0000000010 00000 n \n0000000053 00000 n \ntrailer<<>>\nstartxref\n86\n%%EOF";
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-'.$id.'.pdf"',
        ]);
    }

    /* ---------- Helpers newsletter (global bool compat) ---------- */

    protected function isNewslettersEnabled($user): bool
    {
        if (Schema::hasColumn($user->getTable(), 'newsletter_optin'))      return (bool) $user->newsletter_optin;
        if (Schema::hasColumn($user->getTable(), 'newsletter'))            return (bool) $user->newsletter;
        if (Schema::hasColumn($user->getTable(), 'newsletter_subscribed')) return (bool) $user->newsletter_subscribed;
        if (Schema::hasColumn($user->getTable(), 'settings') && is_array($user->settings ?? null)) {
            return (bool) ($user->settings['newsletter_optin'] ?? false);
        }
        return false;
    }

    protected function setNewslettersEnabled($user, bool $value): void
    {
        if (Schema::hasColumn($user->getTable(), 'newsletter_optin'))      { $user->newsletter_optin = $value;      $user->save(); return; }
        if (Schema::hasColumn($user->getTable(), 'newsletter'))            { $user->newsletter = $value;            $user->save(); return; }
        if (Schema::hasColumn($user->getTable(), 'newsletter_subscribed')) { $user->newsletter_subscribed = $value; $user->save(); return; }
        if (Schema::hasColumn($user->getTable(), 'settings')) {
            $s = is_array($user->settings ?? null) ? $user->settings : [];
            $s['newsletter_optin'] = $value;
            $user->settings = $s;
            $user->save();
        }
    }
}
