<ul class="flex w-full divide-x divide-gray-300 text-gray-700 text-sm">
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.articles.index') }}">Articles</a>
    </li>
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.categories.index') }}">Catégories</a>
    </li>
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.rubrics.index') }}">Rubriques</a>
    </li>
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.authors.index') }}">Auteurs</a>
    </li>
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.issues.index') }}">Magazines</a>
    </li>
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.plans.index') }}">Offres d’abonnement</a>
    </li>
    <li class="flex-1 text-center py-2">
        <a class="hover:underline" href="{{ route('admin.help-items.index') }}">Besoin d’aide ?</a>
    </li>
    @can('manage-users')
        <li class="flex-1 text-center py-2">
            <a class="hover:underline" href="{{ route('admin.users.index') }}">Utilisateurs</a>
        </li>
    @endcan
</ul>
