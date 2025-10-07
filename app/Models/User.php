<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * Champs modifiables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_active',       // ← utilisé par ton contrôleur (toggleActive)
        // Profil étendu
        'nom',
        'prenoms',
        'phone',
        'gender',
        'country',
        'avatar_url',
        'email_verified_at',
    ];

    /**
     * Champs masqués dans les tableaux/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts utiles (Laravel 10+ : "hashed" auto).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    /**
     * Attributs appendés automatiquement au JSON.
     */
    protected $appends = [
        'display_name',
        'full_name',
    ];

    /* ======================================================
     |  Accessors / Helpers
     |======================================================*/

    /**
     * Nom d'affichage robuste : "name" OU "prenoms nom" OU "Utilisateur".
     */
    public function getDisplayNameAttribute(): string
    {
        $n = trim((string) ($this->attributes['name'] ?? ''));
        if ($n !== '') return $n;

        $alt = trim(implode(' ', array_filter([
            $this->attributes['prenoms'] ?? null,
            $this->attributes['nom'] ?? null,
        ])));

        return $alt !== '' ? $alt : 'Utilisateur';
    }

    /**
     * Nom complet "Prénoms Nom" (retombe sur display_name sinon).
     */
    public function getFullNameAttribute(): string
    {
        $full = trim(implode(' ', array_filter([
            $this->attributes['prenoms'] ?? null,
            $this->attributes['nom'] ?? null,
        ])));

        return $full !== '' ? $full : $this->display_name;
    }

    /**
     * Raccourci rôle admin (optionnel).
     */
    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === 'admin';
    }

    /* ======================================================
     |  Relations
     |======================================================*/

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    /**
     * Dernier abonnement encore actif (status=active & ends_at >= now).
     */
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(\App\Models\Subscription::class)->ofMany(
            ['ends_at' => 'max'],
            fn ($q) => $q->where('status', 'active')->where('ends_at', '>=', now())
        );
    }

    public function newsletterTopics()
    {
        return $this->belongsToMany(
            \App\Models\NewsletterTopic::class,
            'newsletter_user',
            'user_id',
            'topic_id'
        )->withPivot(['subscribed', 'unsubscribed_at'])->withTimestamps();
    }

    /* ======================================================
     |  Scopes
     |======================================================*/

    /**
     * Filtrer les utilisateurs ayant un abonnement actif.
     */
    public function scopeWithActive($q)
    {
        return $q->withWhereHas('subscriptions', function ($s) {
            $s->where('status', 'active')->where('ends_at', '>=', now());
        });
    }
}
