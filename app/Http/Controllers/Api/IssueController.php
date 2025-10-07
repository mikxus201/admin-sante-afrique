<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $items = Issue::query()
            ->orderByDesc('number')
            ->get()
            ->map(fn($i) => $this->serialize($i));

        return response()->json(['data' => $items]);
    }

    public function show(Issue $issue)
    {
        return response()->json($this->serialize($issue));
    }

    private function serialize(Issue $i): array
    {
        return [
            'id'           => $i->id,
            'number'       => (int) $i->number,
            'date'         => optional($i->date)->toDateString(),
            'is_published' => (bool) $i->is_published,
            'cover'        => $i->cover,
            'cover_url'    => $i->cover_url,     // accessor du modèle
            'summary'      => $i->summary ?: null,
        ];
    }
}
