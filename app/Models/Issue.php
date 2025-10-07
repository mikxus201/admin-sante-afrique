<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Issue extends Model
{
    protected $table = 'issues';

    protected $fillable = [
        'number', 'date', 'is_published',
        'cover', 'cover_disk', 'summary',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'date'         => 'datetime',
        'summary'      => 'array',
    ];

    protected $appends = ['cover_url'];

    // URL publique de la couverture (fonctionne avec storage:link)
    protected function coverUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->cover) return null;
            $p = ltrim($this->cover, '/');
            if (preg_match('#^https?://#i', $p)) return $p;
            $p = preg_replace('#^(public|storage)/#i', '', $p);
            return asset('storage/'.$p);
        });
    }

    // Confort pour l’admin
    public function getTitleAttribute(): string
    {
        return 'Santé Afrique N°'.(int)$this->number;
    }
}
