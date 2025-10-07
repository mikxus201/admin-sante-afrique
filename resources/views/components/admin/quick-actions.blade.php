<div class="card shadow p-4">
    <div class="text-lg font-bold mb-3">Actions rapides</div>
    <div class="grid grid-2">
        <a class="btn btn-primary" href="{{ route('admin.articles.create') }}">Nouvel article</a>
        <a class="btn" href="{{ route('admin.categories.create') }}">Nouvelle catégorie</a>
        <a class="btn" href="{{ route('admin.authors.create') }}">Nouvel auteur</a>
        <a class="btn" href="{{ route('admin.issues.create') }}">Nouveau magazine</a>
    </div>
</div>
