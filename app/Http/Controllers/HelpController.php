<?php

namespace App\Http\Controllers;

use App\Models\HelpItem;

class HelpController extends Controller
{
    public function subscribe()
    {
        $items = HelpItem::where('group', 'subscribe')
            ->where('is_published', true)
            ->orderBy('position')
            ->get(['key','title','content']);

        return response()->json(['items' => $items]);
    }
}
