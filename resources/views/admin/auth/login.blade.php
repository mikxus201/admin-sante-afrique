

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Connexion admin</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100">
  <div class="min-h-screen flex items-center justify-center">
    <form method="POST" action="{{ route('admin.login.post') }}" class="bg-white shadow p-6 rounded w-full max-w-sm">
      @csrf
      <h1 class="text-lg font-semibold mb-4">Connexion</h1>
      @if($errors->any())
        <div class="mb-3 text-sm text-red-600">{{ $errors->first() }}</div>
      @endif
      <label class="block text-sm">Email</label>
      <input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('email') }}" required>
      <label class="block text-sm mt-3">Mot de passe</label>
      <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required>
      <label class="inline-flex items-center gap-2 mt-3 text-sm">
        <input type="checkbox" name="remember"> Se souvenir de moi
      </label>
      <button class="mt-4 w-full bg-white-600 text-grey rounded px-3 py-2">Se connecter</button>
    </form>
  </div>
</body>
</html>
