@php($isEdit = isset($item) && $item->exists)
<form method="POST"
      action="{{ $isEdit ? route('admin.help-items.update',$item) : route('admin.help-items.store') }}"
      class="space-y-6 bg-white rounded-xl border p-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Groupe</label>
            <input name="group" type="text"
                   value="{{ old('group', $item->group ?? 'subscribe') }}"
                   class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">
            @error('group') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Clé (slug court)</label>
            <input name="key" type="text"
                   value="{{ old('key', $item->key ?? '') }}"
                   placeholder="ex. faq, infos"
                   class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">
            @error('key') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Titre</label>
            <input name="title" type="text"
                   value="{{ old('title', $item->title ?? '') }}"
                   class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">
            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Contenu</label>
            <textarea name="content" rows="6"
                      class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring">{{ old('content', $item->content ?? '') }}</textarea>
            @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2 pt-2">
            <input id="is_published" name="is_published" type="checkbox" value="1"
                   {{ old('is_published', (bool)($item->is_published ?? true)) ? 'checked' : '' }}>
            <label for="is_published" class="text-sm">Publié</label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button class="px-4 py-2 rounded-md bg-gray-900 text-white">
            {{ $isEdit ? 'Enregistrer' : 'Créer' }}
        </button>
        <a class="px-4 py-2 rounded-md border" href="{{ route('admin.help-items.index') }}">Annuler</a>
    </div>
</form>
