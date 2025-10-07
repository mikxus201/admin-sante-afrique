{{-- resources/views/admin/_inline_kpi.blade.php --}}
@props(['title' => '', 'value' => 0, 'delta' => null, 'deltaLabel' => null])

<div class="rounded-xl border p-4">
  <div class="text-sm text-neutral-500">{{ $title }}</div>
  <div class="mt-1 text-2xl font-bold">{{ number_format((int)$value, 0, ',', ' ') }}</div>
  @if(!is_null($delta))
    <div class="mt-1 text-xs text-neutral-500">
      <span class="{{ (int)$delta >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
        {{ (int)$delta >= 0 ? '+' : '' }}{{ (int)$delta }}
      </span>
      <span class="ml-1">{{ $deltaLabel }}</span>
    </div>
  @endif
</div>
