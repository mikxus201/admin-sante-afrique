<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Back-office')</title>

    {{-- Breeze / Vite (si présent) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fallback (si pas de build) : enlève si inutile --}}
    <style>
        .container{max-width:1200px;margin:0 auto;padding:1rem}
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px}
        .shadow{box-shadow:0 1px 2px rgba(0,0,0,.06)}
        .grid{display:grid;gap:1rem}
        .grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}
        .grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
        .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .btn{display:inline-block;padding:.5rem .9rem;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb}
        .btn-primary{background:#2563eb;color:#fff;border-color:#2563eb}
        .btn-danger{background:#dc2626;color:#fff;border-color:#dc2626}
        .btn-success{background:#16a34a;color:#fff;border-color:#16a34a}
        .muted{color:#6b7280}
        table{width:100%;border-collapse:collapse}
        th,td{padding:.65rem;border-bottom:1px solid #e5e7eb;text-align:left;font-size:.95rem}
        .badge{display:inline-block;padding:.2rem .5rem;border-radius:999px;font-size:.75rem;background:#eef2ff;color:#3730a3}
        .flex{display:flex;gap:.75rem;align-items:center}
        .between{justify-content:space-between}
        .mb-2{margin-bottom:.5rem}.mb-3{margin-bottom:.75rem}.mb-4{margin-bottom:1rem}.mb-6{margin-bottom:1.5rem}
        .p-3{padding:.75rem}.p-4{padding:1rem}.p-6{padding:1.5rem}
        .text-sm{font-size:.9rem}.text-lg{font-size:1.125rem}.text-xl{font-size:1.25rem}.text-2xl{font-size:1.5rem}
        .font-bold{font-weight:700}
        .link{color:#2563eb;text-decoration:none}
        .link:hover{text-decoration:underline}
        nav a{color:#374151;text-decoration:none}
        nav a.active{color:#111827;font-weight:600}

        /* Bouton flottant */
        .fab{
            position:fixed; right:16px; bottom:16px; z-index:1050;
            background:#111827; color:#fff; border:none; text-decoration:none;
            padding:.65rem 1rem; border-radius:999px; box-shadow:0 6px 18px rgba(0,0,0,.15);
        }
        .fab:hover{opacity:.92}
    </style>
</head>
<body style="background:#f3f4f6">

    {{-- Bande 1 (topbar) --}}
    @include('admin.partials.topbar')

    {{-- Bande 2 : menu noir (navigation) --}}
    <header style="background:#111827;color:#fff">
        <div class="container flex between p-4">
            <nav class="flex" style="gap:1rem;flex-wrap:wrap">
                <a href="{{ route('admin.dashboard') }}"
                   class="link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   style="color:#fff">Tableau de bord</a>

                <a href="{{ route('admin.articles.index') }}"
                   class="link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}"
                   style="color:#fff">Articles</a>

                <a href="{{ route('admin.categories.index') }}"
                   class="link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                   style="color:#fff">Catégories</a>

                <a href="{{ route('admin.rubrics.index') }}"
                   class="link {{ request()->routeIs('admin.rubrics.*') ? 'active' : '' }}"
                   style="color:#fff">Rubriques</a>

                <a href="{{ route('admin.authors.index') }}"
                   class="link {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}"
                   style="color:#fff">Auteurs</a>

                <a href="{{ route('admin.issues.index') }}"
                   class="link {{ request()->routeIs('admin.issues.*') ? 'active' : '' }}"
                   style="color:#fff">Magazines</a>

                <a href="{{ route('admin.plans.index') }}"
                   class="link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                   style="color:#fff">Offres d’abonnement</a>

                <a href="{{ route('admin.help-items.index') }}"
                   class="link {{ request()->routeIs('admin.help-items.*') ? 'active' : '' }}"
                   style="color:#fff">Besoin d’aide ?</a>

                <a href="{{ route('admin.users.index') }}"
                   class="link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   style="color:#fff">Utilisateurs</a>
            </nav>
        </div>
    </header>

    <main class="container p-4">
        @yield('content')
    </main>

    {{-- Bouton flottant "Rubrique" uniquement sur la page d’édition d’article --}}
    @if (request()->routeIs('admin.articles.edit'))
        @php
            $art = request()->route('article'); // route model binding (peut être un objet Article ou un id/slug)
            // Normalise vers un id pour le fallback
            $artId = is_object($art) ? ($art->id ?? null) : $art;
            $rubricUrl = \Route::has('admin.articles.rubric.edit') && $art
                ? route('admin.articles.rubric.edit', $art)
                : ($artId ? url('/admin/articles/'.$artId.'/rubric') : '#');
        @endphp
        <a href="{{ $rubricUrl }}" class="fab">Rubrique</a>
    @endif

</body>
</html>
