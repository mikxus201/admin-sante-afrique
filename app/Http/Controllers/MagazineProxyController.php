<?php

namespace App\Http\Controllers;

class MagazineProxyController extends Controller
{
    protected function frontUrl(): string
    {
        $front = config('app.front_url', 'http://localhost:3000');
        return rtrim($front, '/');
    }

    // /magazine
    public function index()
    {
        return redirect()->away($this->frontUrl().'/magazine', 302);
    }

    // /magazine/{id}
    public function show(string $id)
    {
        return redirect()->away($this->frontUrl().'/magazine/'.urlencode($id), 302);
    }
}
