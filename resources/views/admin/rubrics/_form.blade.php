
@php
  /** @var \App\Models\Rubric $r */
  $isEdit = isset($r) && $r->exists;
  $action = $isEdit ? route('admin.rubrics.update', $r) : route('admin.rubrics.store');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="card">
    <div class="card-body space-y-6">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Nom</label>
          <input type="text" name="name" required
                 value="{{ old('name', $r->name ?? '') }}"
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">
          @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Slug (optionnel)</label>
          <input type="text" name="slug"
                 value="{{ old('slug', $r->slug ?? '') }}"
                 placeholder="ex. politiques-publiques"
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500">
          @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div>
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="is_active" value="1"
                 @checked(old('is_active', $r->is_active ?? true))
                 class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
          <span class="text-sm text-slate-700">Active</span>
        </label>
        @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button class="btn btn-primary">{{ $isEdit ? 'Enregistrer' : 'Créer' }}</button>
    <a href="{{ route('admin.rubrics.index') }}" class="btn">Annuler</a>
  </div>
</form>
