@props(['type' => 'success', 'dismissible' => true])

@php
    $colors = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error'   => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info'    => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
    $icons = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'error'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'info'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ];
    $iconColors = [
        'success' => 'text-emerald-500',
        'error'   => 'text-red-500',
        'warning' => 'text-amber-500',
        'info'    => 'text-blue-500',
    ];
@endphp

<div class="mb-6 p-4 rounded-xl border {{ $colors[$type] }} flex items-start space-x-3"
     x-data="{ show: true }"
     x-show="show"
     x-transition>
    <svg class="h-5 w-5 {{ $iconColors[$type] }} flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $icons[$type] !!}
    </svg>
    <div class="text-sm font-medium flex-grow">{{ $slot }}</div>
    @if($dismissible)
    <button type="button" @click="show = false" class="{{ str_replace('text-emerald-800', 'text-emerald-500', str_replace('text-red-800', 'text-red-500', str_replace('text-amber-800', 'text-amber-500', str_replace('text-blue-800', 'text-blue-500', $colors[$type])))) }} hover:opacity-70 focus:outline-none flex-shrink-0">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
    @endif
</div>