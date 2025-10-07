@props(['item'])

<a href="{{ route('articles.show', $item->slug ?: $item->id) }}" class="block group">
    @if($item->thumbnail)
        <img class="w-full h-44 object-cover rounded mb-2" src="{{ asset('storage/'.$item->thumbnail) }}" alt="{{ $item->title }}">
    @endif
    <div class="text-xs text-gray-500 mb-1">
        @if($item->category) <span>{{ $item->category }}</span> · @endif
        @if($item->published_at) <time datetime="{{ $item->published_at->toDateString() }}">{{ $item->published_at->translatedFormat('d M Y') }}</time> @endif
        · {{ $item->read_time_minutes }} min
    </div>
    <div class="font-semibold group-hover:underline">{{ $item->title }}</div>
    @if($item->excerpt)
        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($item->excerpt, 120) }}</p>
    @endif
</a>
