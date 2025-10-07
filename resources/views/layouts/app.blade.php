{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    {{-- Fonts (facultatif) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    {{-- Bootstrap CSS (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Tes assets Vite (si utilisés) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Styles additionnels injectés par les vues --}}
    @stack('styles')
</head>
<body class="bg-light" style="min-height:100vh; display:flex; flex-direction:column;">
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"
                    aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navMain" class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.articles.index') }}">Articles</a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">Mon profil</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary">Se déconnecter</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-sm btn-primary" href="{{ route('login') }}">Se connecter</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Contenu principal --}}
    <main class="flex-grow-1 py-4">
        <div class="container">

            {{-- Flash messages --}}
            @if (session('ok'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('ok') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Slot/Section de la vue --}}
            @yield('content')
            {{-- ou, si tu utilises des composants Blade style Breeze : --}}
            {{ $slot ?? '' }}
        </div>
    </main>

    {{-- Footer simple --}}
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-muted small d-flex justify-content-between">
            <span>© {{ date('Y') }} {{ config('app.name', 'Laravel') }}</span>
            <span>Admin • Laravel</span>
        </div>
    </footer>

    {{-- Bootstrap JS (bundle) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    {{-- Scripts additionnels injectés par les vues --}}
    @stack('scripts')
</body>
</html>
