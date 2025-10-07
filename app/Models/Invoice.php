<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'period_from',
        'period_to',
        'amount_fcfa',
        'status',
        'pdf_path',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to'   => 'date',
    ];

    public function user() { return $this->belongsTo(\App\Models\User::class); }

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) return null;
        return Storage::disk('public')->url($this->pdf_path);
    }

    public function pdfFilename(): string
    {
        return sprintf('INV-%s.pdf', $this->number ?? now()->format('YmdHis'));
    }

    public function pdfDirectory(): string
    {
        return 'invoices';
    }

    /** Génère (ou régénère) le PDF avec dompdf/dompdf natif */
    public function generateAndStorePdf(): string
    {
        $html = view('pdf.invoice', ['inv' => $this])->render();

        $opts = new Options();
        $opts->set('isRemoteEnabled', true);       // autorise images/ressources externes
        $opts->set('isHtml5ParserEnabled', true);
        $opts->set('defaultFont', 'DejaVu Sans');  // support unicode

        $dompdf = new Dompdf($opts);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $content = $dompdf->output();
        $dir  = $this->pdfDirectory();
        $name = $this->pdfFilename();
        $path = $dir . '/' . $name;

        Storage::disk('public')->put($path, $content);

        if ($this->pdf_path !== $path) {
            $this->pdf_path = $path;
            $this->saveQuietly();
        }

        return $path;
    }

    public function ensurePdfExists(): string
    {
        if ($this->pdf_path && Storage::disk('public')->exists($this->pdf_path)) {
            return $this->pdf_path;
        }
        return $this->generateAndStorePdf();
    }
}
