<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CinetpayClient
{
    private string $apiKey;
    private string $siteId;
    private string $base;

    public function __construct(?string $apiKey = null, ?string $siteId = null, ?string $base = null)
    {
        $this->apiKey = $apiKey ?: env('CINETPAY_API_KEY', '');
        $this->siteId = $siteId ?: env('CINETPAY_SITE_ID', '');
        $this->base   = rtrim($base ?: env('CINETPAY_BASE', 'https://api-checkout.cinetpay.com'), '/');
    }

    /** Init paiement → retourne le JSON de CinetPay */
    public function initPayment(array $payload): array
    {
        $body = array_merge([
            'apikey'  => $this->apiKey,
            'site_id' => $this->siteId,
        ], $payload);

        return Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent'   => 'SanteAfrique/1.0',
            ])
            ->post($this->base.'/v2/payment', $body)
            ->json();
    }

    /** Vérifier une transaction */
    public function check(string $transactionId): array
    {
        $body = [
            'apikey'         => $this->apiKey,
            'site_id'        => $this->siteId,
            'transaction_id' => $transactionId,
        ];

        return Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent'   => 'SanteAfrique/1.0',
            ])
            ->post($this->base.'/v2/payment/check', $body)
            ->json();
    }

    /** Vérif HMAC optionnelle (si tu utilises X-Token côté notif) */
    public function validHmac(?string $tokenHeader, string $rawBody): bool
    {
        $secret = env('CINETPAY_SECRET_KEY');
        if (!$secret || !$tokenHeader) return true;
        $computed = hash_hmac('sha256', $rawBody, $secret);
        return hash_equals($computed, $tokenHeader);
    }
}
