@props(['items' => []])

<nav class="flex items-center space-x-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
    <a href="{{ route('landing') }}" class="hover:text-blue-600 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
    </a>
    @foreach($items as $item)
        <span class="text-slate-400">/</span>
        @if(isset($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-blue-600 transition font-medium">{{ $item['label'] }}</a>
        @else
            <span class="text-slate-900 font-semibold">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>