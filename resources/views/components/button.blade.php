@props(['variant' => 'primary', 'type' => 'button', 'size' => 'md'])
@php
$variants = [
    'primary' => 'bg-emerald-700 text-white hover:bg-emerald-800 focus-visible:ring-emerald-700/30 border border-emerald-800/40 shadow-xs active:bg-emerald-900',
    'secondary' => 'bg-white text-slate-700 border border-slate-300/80 hover:bg-slate-50 hover:border-slate-400/80 focus-visible:ring-slate-400/20 shadow-2xs active:bg-slate-100',
    'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:ring-rose-600/30 border border-rose-700/40 shadow-xs active:bg-rose-800',
    'ghost' => 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 focus-visible:ring-slate-400/20 active:bg-slate-200/60',
    'brand' => 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100/80 focus-visible:ring-emerald-500/20 active:bg-emerald-200/80',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs font-semibold gap-1.5 rounded-lg',
    'md' => 'px-4 py-2 text-sm font-semibold gap-2 rounded-lg',
    'lg' => 'px-5 py-2.5 text-base font-semibold gap-2.5 rounded-xl',
];
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center font-medium transition duration-150 ease-in-out cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $slot }}
</button>
