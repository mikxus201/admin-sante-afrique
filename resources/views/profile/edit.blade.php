<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mon profil
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">

                @if (session('status') === 'profile-updated')
                    <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
                        Profil mis à jour avec succès.
                    </div>
                @endif

                {{-- Formulaire d’édition du profil --}}
                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-sm font-medium">Nom</label>
                        <input class="mt-1 w-full border rounded p-2"
                               type="text" name="name" value="{{ old('name', $user->name) }}">
                        @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input class="mt-1 w-full border rounded p-2"
                               type="email" name="email" value="{{ old('email', $user->email) }}">
                        @error('email') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Enregistrer
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded">
                            Annuler
                        </a>
                        <a href="/admin" class="ml-auto px-4 py-2 border rounded">Aller à l’admin</a>
                    </div>
                </form>

                {{-- Bouton de déconnexion (utile pour recharger les droits admin) --}}
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-4 py-2 bg-gray-700 text-white rounded" type="submit">
                        Se déconnecter
                    </button>
                </form>

                {{-- (Optionnel) Suppression du compte --}}
                {{-- 
                <form method="post" action="{{ route('profile.destroy') }}" class="mt-4">
                    @csrf
                    @method('delete')
                    <input type="password" name="password" class="border rounded p-2" placeholder="Mot de passe actuel" required>
                    <button class="ml-2 px-4 py-2 bg-red-600 text-white rounded">Supprimer mon compte</button>
                </form>
                --}}
            </div>
        </div>
    </div>
</x-app-layout>
