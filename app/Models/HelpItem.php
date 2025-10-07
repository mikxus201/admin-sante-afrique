<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpItem extends Model
{
    protected $fillable = ['group', 'key', 'title', 'content', 'is_published', 'position'];

    protected $casts = [
        'is_published' => 'boolean',
        'position'     => 'integer',
    ];
}
