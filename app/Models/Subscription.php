<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id','plan_id','status','payment_method','payment_ref','starts_at','ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }

    /** Abonnements valides à l’instant T */
    public function scopeCurrent($q) {
        return $q->where('status','active')->where('ends_at','>=', now());
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->ends_at && $this->ends_at->isFuture();
    }
}
