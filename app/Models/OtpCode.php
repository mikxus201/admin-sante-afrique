<?php


// app/Models/OtpCode.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['identifier', 'code_hash', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];
}
