@extends('layouts.app')

@section('title', $article->title)
@section('meta_description', $article->excerpt ?? Str::limit(strip_tags($article->body), 160))

@section('content')
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ url('/') }}">Accueil</a> ›
        <a href="{{ route('articles.index') }}">Articles</a>
        @if($article->category)
            › <a href="{{ route('articles.index', ['q' => $article->category]) }}">{{ $article->category }}</a>
        @endif
    </nav>

    <article class="prose max-w-none">
        <header class="mb-6">
            <h1 class="mb-2">{{ $article->title }}</h1>

            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                @if($article->category)
                    <span class="inline-flex items-center rounded-full px-3 py-1 bg-gray-100">
                        {{ $article->category }}
                    </span>
                @endif

                <span>Par <strong>{{ $article->author ?: 'Rédaction' }}</strong></span>

                @if($article->published_at)
                    <time datetime="{{ $article->published_at->toDateString() }}">
                        {{ $article->published_at->translatedFormat('d F Y') }}
                    </time>
                @endif

                <span>• {{ $article->read_time_minutes }} min de lecture</span>
            </div>
        </header>

        @if($article->thumbnail)
            <figure class="mb-6">
                <img class="w-full rounded" src="{{ asset('storage/'.$article->thumbnail) }}"
                     alt="{{ $article->title }}">
            </figure>
        @endif

        @if($article->excerpt)
            <p class="text-lg text-gray-700 font-medium mb-6">{{ $article->excerpt }}</p>
        @endif

        <div class="article-body mb-8">
            {!! $article->body !!}
        </div>

        @if(!empty($article->tags))
            <div class="mb-8">
                <h3 class="text-sm uppercase tracking-wide text-gray-500 mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <a class="px-3 py-1 rounded-full bg-gray-100 text-sm"
                           href="{{ route('articles.index', ['q' => $tag]) }}">{{ $tag }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($article->sources))
            <div class="mb-10">
                <h3 class="text-sm uppercase tracking-wide text-gray-500 mb-2">Sources</h3>
                <ul class="list-disc ml-6">
                    @foreach($article->sources as $src)
                        <li>
                            @php $isUrl = str_starts_with($src,'http'); @endphp
                            @if($isUrl)
                                <a href="{{ $src }}" target="_blank" rel="noopener" class="underline">{{ $src }}</a>
                            @else
                                {{ $src }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Partage simple --}}
        <div class="mb-10 flex items-center gap-3 text-sm">
            <span class="text-gray-500">Partager :</span>
            @php
                $shareUrl = urlencode($article->url);
                $shareText = urlencode($article->title);
            @endphp
            <a class="underline" href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener">Twitter/X</a>
            <a class="underline" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener">Facebook</a>
            <a class="underline" href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareUrl }}&title={{ $shareText }}" target="_blank" rel="noopener">LinkedIn</a>
        </div>

        {{-- Navigation précédent / suivant --}}
        <div class="flex justify-between border-t pt-6 mt-6 text-sm">
            <div>
                @if($prev)
                    <div class="text-gray-500">Article précédent</div>
                    <a class="underline" href="{{ route('articles.show', $prev->slug ?: $prev->id) }}">{{ $prev->title }}</a>
                @endif
            </div>
            <div class="text-right">
                @if($next)
                    <div class="text-gray-500">Article suivant</div>
                    <a class="underline" href="{{ route('articles.show', $next->slug ?: $next->id) }}">{{ $next->title }}</a>
                @endif
            </div>
        </div>
    </article>

    {{-- Articles liés --}}
    @if($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-lg font-semibold mb-4">À lire aussi</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($related as $r)
                    <a href="{{ route('articles.show', $r->slug ?: $r->id) }}" class="block group">
                        @if($r->thumbnail)
                            <img class="w-full h-44 object-cover rounded mb-2" src="{{ asset('storage/'.$r->thumbnail) }}" alt="{{ $r->title }}">
                        @endif
                        <div class="text-sm text-gray-500">
                            @if($r->category) <span>{{ $r->category }}</span> · @endif
                            @if($r->published_at) <time datetime="{{ $r->published_at->toDateString() }}">{{ $r->published_at->translatedFormat('d M Y') }}</time> @endif
                        </div>
                        <div class="font-medium group-hover:underline">{{ $r->title }}</div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- JSON-LD (SEO) --}}
    @push('jsonld')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->title,
            'datePublished' => optional($article->published_at)->toAtomString(),
            'dateModified'  => optional($article->updated_at)->toAtomString(),
            'author' => [
                '@type' => 'Person',
                'name'  => $article->author ?: 'Rédaction'
            ],
            'image' => $article->thumbnail ? [asset('storage/'.$article->thumbnail)] : null,
            'articleSection' => $article->category ?: null,
            'keywords' => $article->tags ?: [],
            'mainEntityOfPage' => $article->url,
            'publisher' => [
                '@type' => 'Organization',
                'name'  => config('app.name'),
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => asset('favicon.ico')
                ],
            ],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
        </script>
    @endpush
@endsection
