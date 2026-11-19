@props(['heading' => null, 'subheading' => null, 'icon' => null])
<section {{ $attributes->merge(['class' => 'border-t border-stone-950/10 bg-white/35 px-5 py-5 sm:px-6 sm:py-6']) }}>
    @if($heading || isset($header))
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-base font-semibold text-stone-900">{{ $heading }}</h3>
                    @if($subheading)<p class="mt-1 text-sm text-stone-500">{{ $subheading }}</p>@endif
                </div>
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    {{ $slot }}
</section>
