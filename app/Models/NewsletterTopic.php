<?php

// app/Models/NewsletterTopic.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NewsletterTopic extends Model
{
    protected $fillable = ['slug','name','is_active'];
    public function users(){
        return $this->belongsToMany(User::class, 'newsletter_user', 'topic_id', 'user_id')
            ->withPivot(['subscribed','unsubscribed_at'])->withTimestamps();
    }
}
