@php($isEdit = isset($plan) && $plan->exists)
<form method="POST"
      action="{{ $isEdit ? route('admin.plans.update',$plan) : route('admin.plans.store') }}"
      class="space-y-6 bg-white rounded-xl border p-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input name="name" type="text"
                   value="{{ old('name', $plan->name ?? '') }}"
                   class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Slug</label>
            <input name="slug" type="text"
                   value="{{ old('slug', $plan->slug ?? '') }}"
                   placeholder="ex. annuel-numerique"
                   class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">
            @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Prix (FCFA)</label>
            <input name="price_fcfa" type="number" min="0" step="1"
                   value="{{ old('price_fcfa', $plan->price_fcfa ?? '') }}"
                   class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">
            @error('price_fcfa') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2 pt-6">
            <input id="is_published" name="is_published" type="checkbox" value="1"
                   {{ old('is_published', (bool)($plan->is_published ?? false)) ? 'checked' : '' }}>
            <label for="is_published" class="text-sm">Publié</label>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="4"
                  class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">{{ old('description', $plan->description ?? '') }}</textarea>
        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button class="px-4 py-2 rounded-md border">
            {{ $isEdit ? 'Enregistrer' : 'Créer' }}
        </button>
        <a class="px-4 py-2 rounded-md border" href="{{ route('admin.plans.index') }}">Annuler</a>
    </div>
</form>
