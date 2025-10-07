<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // \App\Models\Article::class => \App\Policies\ArticlePolicy::class,
    ];

    public function boot(): void
    {
        // Droit de GESTION des utilisateurs
        Gate::define('manage-users', function ($user) {
            $role = strtolower(trim((string) ($user->role ?? '')));
            return in_array($role, [
                'admin', 'administrator', 'super admin', 'super_admin', 'super-admin'
            ], true);
        });

        // Droit de PUBLICATION d’articles (admin + moderator)
        Gate::define('publish-content', function ($user) {
            $role = strtolower(trim((string) ($user->role ?? '')));
            return in_array($role, [
                'admin', 'administrator', 'super admin', 'super_admin', 'super-admin', 'moderator'
            ], true);
        });
    }
}