<?php

namespace App\Http\Controllers\paymentcontroller;

// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Plan;

class PaymentController extends Controller {
  public function init(Request $r) {
    $data = $r->validate([
      'planId' => ['required','integer','exists:plans,id'],
      'planSlug' => ['nullable','string'],
      'amount' => ['required','integer','min:0'],
      'https://api-checkout.cinetpay.com/v2/payment' => ['required','url'],
      'failUrl' => ['required','url'],
    ]);
    // TODO: appel CinetPay ici
    // Pour l’instant on simule une redirection OK
    return response()->json([
      'checkoutUrl' => $data['https://api-checkout.cinetpay.com/v2/payment']      // stub : remplace par l’URL CinetPay
    ]);
  }
}
