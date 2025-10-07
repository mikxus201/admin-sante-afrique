<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Rubric extends Model
{
    protected $fillable = ['name','slug','description','is_active'];

    protected static function booted()
    {
        static::saving(function (Rubric $r) {
            // si pas de slug ou slug vide => dérivé du nom
            $r->slug = $r->slug ? Str::slug($r->slug) : Str::slug($r->name);
        });
    }

    public function articles()
    {
        return $this->hasMany(\App\Models\Article::class);
    }
}
