<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoicePdfController extends Controller
{
    public function show(Invoice $invoice)
    {
        if (!auth()->check()) abort(403);
        if (!optional(auth()->user())->isAdmin() && auth()->id() !== $invoice->user_id) {
            abort(403);
        }

        $invoice->ensurePdfExists();

        if ($invoice->pdf_url) {
            return redirect()->away($invoice->pdf_url);
        }

        abort(404, 'PDF introuvable');
    }
}
