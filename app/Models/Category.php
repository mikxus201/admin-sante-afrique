<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name','slug','description','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    /** Génère un slug si vide, normalise si fourni */
    protected static function booted(): void
    {
        static::saving(function (Category $c) {
            $c->slug = $c->slug ? Str::slug($c->slug) : Str::slug($c->name);
        });
    }

    /** Scopes utiles */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeSearch(Builder $q, ?string $s): Builder
    {
        if (!$s) return $q;
        return $q->where(function ($x) use ($s) {
            $x->where('name','like',"%{$s}%")
              ->orWhere('slug','like',"%{$s}%")
              ->orWhere('description','like',"%{$s}%");
        });
    }
    // app/Models/Category.php
     public function articles()
    {return $this->hasMany(\App\Models\Article::class);
    }

}
