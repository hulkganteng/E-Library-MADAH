@props(['color' => 'slate', 'size' => 'md', 'dot' => false])
@php
$colors = [
    'slate' => 'bg-slate-100/90 text-slate-700 border-slate-200/70',
    'brand' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
    'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
    'amber' => 'bg-amber-50 text-amber-800 border-amber-200/80',
    'red' => 'bg-rose-50 text-rose-700 border-rose-200/70',
    'blue' => 'bg-sky-50 text-sky-700 border-sky-200/70',
    'gold' => 'bg-amber-100/70 text-amber-800 border-amber-300/60',
    'purple' => 'bg-purple-50 text-purple-700 border-purple-200/70',
];

$dotColors = [
    'slate' => 'bg-slate-500',
    'brand' => 'bg-emerald-600',
    'green' => 'bg-emerald-500',
    'amber' => 'bg-amber-500',
    'red' => 'bg-rose-500',
    'blue' => 'bg-sky-500',
    'gold' => 'bg-amber-600',
    'purple' => 'bg-purple-500',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-[11px] font-medium tracking-tight gap-1 rounded-md',
    'md' => 'px-2.5 py-0.5 text-xs font-semibold gap-1.5 rounded-md',
];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center border font-medium ' . ($colors[$color] ?? $colors['slate']) . ' ' . ($sizes[$size] ?? $sizes['md'])]) }}>
    @if($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$color] ?? 'bg-slate-400' }}"></span>
    @endif
    {{ $slot }}
</span>
