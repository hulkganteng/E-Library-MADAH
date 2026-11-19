@props(['label', 'value', 'icon' => null, 'color' => 'brand', 'sub' => null])
@php
$colorStyles = [
    'brand' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
    'green' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
    'gold' => 'bg-amber-50 text-amber-800 border-amber-200/60',
    'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
    'red' => 'bg-rose-50 text-rose-700 border-rose-100',
    'blue' => 'bg-sky-50 text-sky-700 border-sky-100',
    'purple' => 'bg-purple-50 text-purple-700 border-purple-100',
    'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
];
@endphp
<div class="min-w-0 border-t border-stone-950/10 py-4 sm:py-5">
    <div class="min-w-0">
            <p class="truncate text-sm font-medium text-stone-500">{{ $label }}</p>
            <p class="mt-1 truncate text-2xl font-semibold tracking-tight text-stone-900 tabular-nums sm:text-3xl">{{ $value }}</p>
            @if($sub)
                <p class="mt-1.5 flex items-center gap-1.5 truncate text-sm text-stone-500" title="{{ $sub }}">
                    <span class="truncate">{{ $sub }}</span>
                </p>
            @endif
    </div>
</div>
