<?php

namespace App\Http\Controllers;

use App\Models\HelpBlock;
use Illuminate\Http\Request;

class HelpBlockController extends Controller
{
    public function subscribe() {
        return ['items' => HelpBlock::whereIn('key', ['sub_help_1','sub_help_2','sub_help_3'])->get()];
    }

    public function upsert(Request $request) {
        $this->authorize('admin');
        $data = $request->validate(['key'=>'required','title'=>'required','content'=>'required']);
        $h = HelpBlock::updateOrCreate(['key'=>$data['key']], $data);
        return $h;
    }
}
