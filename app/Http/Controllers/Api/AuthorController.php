<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // GET /api/authors ?page=&perPage=&search=
    public function index(Request $r)
    {
        $perPage = max(1, min(100, (int)$r->integer('perPage', 24)));
        $q       = trim((string)$r->get('search', ''));

        $base = Author::query()
            ->select('id','name','slug','bio','photo','active')
            ->when($q !== '', function ($x) use ($q) {
                $x->where('name','like',"%$q%")
                  ->orWhere('bio','like',"%$q%");
            })
            ->orderBy('name');

        // Si tu veux seulement les actifs, décommente la ligne suivante :
        // $base->where('active', true);

        return $base->paginate($perPage);
    }

    // GET /api/authors/slug/{slug}
    public function showBySlug(string $slug)
    {
        $a = Author::query()
            ->select('id','name','slug','bio','photo','active')
            ->where('slug', $slug)
            ->firstOrFail();

        // expose aussi un champ photo_url (comme ton modèle)
        $a->append('photo_url');
        return $a;
    }

    // GET /api/authors/{author} (id OU slug)
    public function show(string $author)
    {
        $a = Author::query()
            ->select('id','name','slug','bio','photo','active')
            ->where('id', $author)
            ->orWhere('slug', $author)
            ->firstOrFail();

        $a->append('photo_url');
        return $a;
    }
}
