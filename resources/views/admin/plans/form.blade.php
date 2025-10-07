@if ($errors->any())
  <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
    <ul class="list-disc pl-4">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
  </div>
@endif

<form method="post" action="{{ $route }}" class="rounded border bg-white p-4 space-y-4">
  @csrf
  @if($method !== 'POST') @method($method) @endif

  <label class="block">
    <span class="text-sm">Nom</span>
    <input name="name" value="{{ old('name',$plan->name) }}" required class="mt-1 w-full rounded border px-3 py-2">
  </label>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <label class="block">
      <span class="text-sm">Slug</span>
      <input name="slug" value="{{ old('slug',$plan->slug) }}" required class="mt-1 w-full rounded border px-3 py-2" placeholder="annuel-numerique">
    </label>
    <label class="block">
      <span class="text-sm">Prix (FCFA)</span>
      <input type="number" min="0" step="1" name="price_fcfa" value="{{ old('price_fcfa',$plan->price_fcfa) }}" required class="mt-1 w-full rounded border px-3 py-2">
    </label>
  </div>

  <label class="block">
    <span class="text-sm">Description</span>
    <textarea name="description" rows="5" class="mt-1 w-full rounded border px-3 py-2">{{ old('description',$plan->description) }}</textarea>
  </label>

  <label class="inline-flex items-center gap-2">
    <input type="checkbox" name="is_published" value="1" {{ old('is_published',$plan->is_published) ? 'checked' : '' }}>
    <span>Publié</span>
  </label>

  <div class="flex items-center gap-3">
    <button class="px-4 py-2 rounded border">Enregistrer</button>
    <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 rounded border">Annuler</a>
  </div>
</form>
