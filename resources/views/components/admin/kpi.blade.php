@props(['title' => '', 'value' => '-', 'sub' => null])

<div class="card shadow p-4">
    <div class="text-sm muted mb-2">{{ $title }}</div>
    <div class="text-2xl font-bold">{{ $value }}</div>
    @if($sub)
        <div class="text-sm muted mt-1">{{ $sub }}</div>
    @endif
</div>
