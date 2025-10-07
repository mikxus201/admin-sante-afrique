@php
  /** @var \App\Models\Issue $issue */
  $isEdit = isset($issue) && $issue->exists;
  $action = $isEdit ? route('admin.issues.update', $issue->id) : route('admin.issues.store');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="card">
    <div class="card-body space-y-6">
      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Numéro</label>
          <input type="number" name="number" min="1" required
                 value="{{ old('number', $issue->number ?? '') }}"
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">
          @error('number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Titre (optionnel)</label>
          <input type="text" name="title"
                 value="{{ old('title', $issue->title ?? '') }}"
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">
          @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Date</label>
          <input type="date" name="date"
                 value="{{ old('date', $issue->date ?? '') }}"
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">
          @error('date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Sommaire (texte, 1 item / ligne)</label>
          <textarea name="summary" rows="6"
                    placeholder="Exemple :&#10;Édito | 3&#10;Grand dossier | 12"
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">{{ old('summary', is_array($issue->summary ?? null) ? collect($issue->summary)->pluck('title')->implode(PHP_EOL) : ($issue->summary ?? '')) }}</textarea>
          @error('summary') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Sommaire (JSON optionnel)</label>
          <textarea name="summary_json" rows="6" placeholder='[{"title":"Édito","page":3}]'
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">{{ old('summary_json', '') }}</textarea>
          @error('summary_json') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Couverture (upload)</label>
          <input type="file" name="cover" accept="image/*"
                 class="mt-1 block w-full text-sm text-slate-700">
          @error('cover') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="pt-6">
          <input type="hidden" name="is_published" value="0">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_published" value="1"
                   @checked(old('is_published', $issue->is_published ?? false))
                   class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
            <span class="text-sm text-slate-700">Publié</span>
          </label>
          @error('is_published') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button class="btn btn-primary">{{ $isEdit ? 'Enregistrer' : 'Créer' }}</button>
    <a href="{{ route('admin.issues.index') }}" class="btn">Annuler</a>
  </div>
</form>
