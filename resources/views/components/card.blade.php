@props(['heading' => null, 'subheading' => null, 'icon' => null])
<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-soft transition duration-150']) }}>
    @if($heading || isset($header))
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-800 text-sm font-semibold border border-emerald-100">
                        {{ $icon }}
                    </div>
                @endif
                <div>
                    <h3 class="text-base font-bold tracking-tight text-slate-900">{{ $heading }}</h3>
                    @if($subheading)<p class="text-xs text-slate-500 mt-0.5">{{ $subheading }}</p>@endif
                </div>
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    {{ $slot }}
</div>
