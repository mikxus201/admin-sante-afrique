@props(['title' => '', 'link' => null, 'linkText' => 'Voir tout'])

<div class="card shadow p-4">
    <div class="flex between mb-3">
        <div class="text-lg font-bold">{{ $title }}</div>
        @if($link)
            <a href="{{ $link }}" class="link text-sm">{{ $linkText }}</a>
        @endif
    </div>

    <div class="grid" style="gap:.75rem">
        {{ $slot }}
    </div>
</div>
