<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Services\CinetpayClient;   // tu l’as maintenant OK
use Illuminate\Http\Request;

class CinetpayDebugController extends Controller
{
    public function __construct(private CinetpayClient $cp) {}

    public function show(string $tx, Request $r)
    {
        $res = $this->cp->check($tx);
        \Log::info('CP CHECK DEBUG', ['tx'=>$tx, 'res'=>$res, 'ip'=>$r->ip()]);
        return response()->json($res);
    }
}
