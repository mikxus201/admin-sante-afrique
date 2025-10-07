<?php

// app/Http/Controllers/Api/InvoiceController.php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function index(Request $r){
        $items = $r->user()->invoices()->latest('period_from')->paginate(12);
        return response()->json($items);
    }

    public function download(Request $r, Invoice $invoice): StreamedResponse {
        abort_unless($invoice->user_id === $r->user()->id, 403);
        abort_unless($invoice->pdf_path && Storage::disk('public')->exists($invoice->pdf_path), 404);
        $filename = 'facture-'.$invoice->number.'.pdf';
        return Storage::disk('public')->download($invoice->pdf_path, $filename);
    }
}
