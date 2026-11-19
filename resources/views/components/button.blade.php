@props(['variant' => 'primary', 'type' => 'button', 'size' => 'md'])
@php
$variants = [
    'primary' => 'bg-emerald-800 text-white hover:bg-emerald-900 active:bg-emerald-950',
    'secondary' => 'bg-white text-stone-700 ring-1 ring-stone-950/15 hover:bg-stone-50 active:bg-stone-100',
    'danger' => 'bg-white text-rose-700 ring-1 ring-rose-700/25 hover:bg-rose-50 active:bg-rose-100',
    'ghost' => 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 focus-visible:ring-slate-400/20 active:bg-slate-200/60',
    'brand' => 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100/80 focus-visible:ring-emerald-500/20 active:bg-emerald-200/80',
];

$sizes = [
    'sm' => 'px-2.5 py-1.5 text-sm font-medium gap-1.5 rounded-md',
    'md' => 'px-3 py-2 text-sm font-medium gap-2 rounded-md',
    'lg' => 'px-4 py-2.5 text-sm font-medium gap-2 rounded-md',
];
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $slot }}
</button>
