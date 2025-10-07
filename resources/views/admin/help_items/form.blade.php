@if ($errors->any())
  <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
    <ul class="list-disc pl-4">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
  </div>
@endif

<form method="post" action="{{ $route }}" class="rounded border bg-white p-4 space-y-4">
  @csrf
  @if($method !== 'POST') @method($method) @endif

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <label class="block md:col-span-2">
      <span class="text-sm">Titre</span>
      <input name="title" value="{{ old('title',$item->title) }}" required class="mt-1 w-full rounded border px-3 py-2">
    </label>
    <label class="block">
      <span class="text-sm">Clé (slug)</span>
      <input name="key" value="{{ old('key',$item->key) }}" required class="mt-1 w-full rounded border px-3 py-2" placeholder="faq">
    </label>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <label class="block">
      <span class="text-sm">Groupe</span>
      <input name="group" value="{{ old('group',$item->group ?? 'subscribe') }}" class="mt-1 w-full rounded border px-3 py-2">
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_published" value="1" {{ old('is_published',$item->is_published) ? 'checked' : '' }}>
      <span>Publié</span>
    </label>
  </div>

  <label class="block">
    <span class="text-sm">Contenu</span>
    <textarea name="content" rows="10" class="mt-1 w-full rounded border px-3 py-2">{{ old('content',$item->content) }}</textarea>
  </label>

  <div class="flex items-center gap-3">
    <button class="px-4 py-2 rounded border">Enregistrer</button>
    <a href="{{ route('admin.help-items.index') }}" class="px-4 py-2 rounded border">Annuler</a>
  </div>
</form>
