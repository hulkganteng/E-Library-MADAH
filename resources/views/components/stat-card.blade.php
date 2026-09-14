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
<div class="group relative overflow-hidden rounded-xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-soft transition hover:border-slate-300">
    <div class="flex items-start justify-between gap-2.5 sm:gap-3">
        <div class="min-w-0 flex-1">
            <p class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate">{{ $label }}</p>
            <p class="mt-1.5 sm:mt-2 text-xl font-extrabold tracking-tight text-slate-900 sm:text-3xl truncate">{{ $value }}</p>
            @if($sub)
                <p class="mt-1.5 flex items-center gap-1.5 text-[11px] sm:text-xs font-medium text-slate-500 truncate" title="{{ $sub }}">
                    <span class="inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-slate-300"></span>
                    <span class="truncate">{{ $sub }}</span>
                </p>
            @endif
        </div>
        @if($icon)
            <div class="grid h-9 w-9 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-xl border text-sm sm:text-base font-semibold {{ $colorStyles[$color] ?? $colorStyles['brand'] }}">
                {{ $icon }}
            </div>
        @endif
    </div>
</div>
