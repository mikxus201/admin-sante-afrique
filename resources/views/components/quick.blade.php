@props(['title','text'=>null,'to'=>'#','cta'=>'Ouvrir'])
<a href="{{ $to }}" class="rounded-lg border p-4 bg-white hover:bg-gray-50 block">
  <div class="font-semibold">{{ $title }}</div>
  @if($text)<div class="text-sm text-gray-500 mt-1">{{ $text }}</div>@endif
  <div class="mt-3 inline-flex items-center text-blue-600">{{ $cta }} →</div>
</a>
