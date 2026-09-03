@props(['href', 'active' => false, 'icon' => ''])
@php
$classes = $active
    ? 'bg-emerald-800 text-white font-semibold shadow-xs'
    : 'text-emerald-100/85 hover:bg-emerald-800/50 hover:text-white font-medium';
@endphp
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors duration-150 ' . $classes]) }}>
    <span class="grid h-5 w-5 shrink-0 place-items-center text-emerald-300 group-hover:text-white transition-colors {{ $active ? 'text-white' : '' }}">
        {{ $icon }}
    </span>
    <span class="truncate">{{ $slot }}</span>
</a>
