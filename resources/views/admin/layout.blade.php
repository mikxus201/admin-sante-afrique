<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Admin') – {{ config('app.name','Laravel') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen flex flex-col">

    {{-- Bande 1 : gauche Back-office / centre user / droite logout --}}
    <div class="bg-white border-b">
        <div class="mx-auto max-w-7xl px-4 py-3 grid grid-cols-3 items-center text-gray-700">
            <div class="text-left font-semibold">
                @php($hasDashboard = \Illuminate\Support\Facades\Route::has('admin.dashboard'))
                <a href="{{ $hasDashboard ? route('admin.dashboard') : url('/admin') }}">Back-office</a>
            </div>

            <div class="text-center text-sm">
                @auth
                    Connecté : <strong>{{ auth()->user()->name }}</strong>
                    @if(!empty(auth()->user()->role)) ({{ auth()->user()->role }}) @endif
                @endauth
            </div>

            <div class="text-right text-sm">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="hover:underline">Se déconnecter</button>
                    </form>
                @else
                    @if(\Illuminate\Support\Facades\Route::has('login'))
                        <a href="{{ route('login') }}" class="hover:underline">Se connecter</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- Bande 2 : navigation étalée avec séparateurs --}}
    <nav class="bg-gray-50 border-b">
        <div class="mx-auto max-w-7xl px-4">
            @include('admin.partials.nav')
        </div>
    </nav>

    {{-- Contenu --}}
    <main class="flex-1 mx-auto max-w-7xl w-full px-4 py-6">
        @if(session('status'))
            <div class="mb-4 rounded bg-green-50 border border-green-200 px-4 py-2 text-green-800">
                {{ session('status') }}
            </div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
