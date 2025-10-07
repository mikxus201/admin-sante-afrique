<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Author extends Model
{
    protected $fillable = ['name', 'slug', 'bio', 'photo', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) return null;
        if (Str::startsWith($this->photo, ['http://','https://'])) return $this->photo;
        return asset('storage/'.$this->photo); // storage/app/public/authors/xxx.jpg
    }

    protected static function booted()
    {
        static::saving(function (Author $a) {
            if (blank($a->slug) && filled($a->name)) {
                $a->slug = Str::slug($a->name);
            }
        });
    }

    public function scopeActive($q) { return $q->where('active', true); }
}
