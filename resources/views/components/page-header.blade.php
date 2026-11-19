@props(['title', 'subtitle' => null])
<div class="mb-7 flex flex-col gap-4 border-b border-stone-950/10 pb-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-xl font-semibold tracking-tight text-stone-950 sm:text-2xl">{{ $title }}</h1>
        @if($subtitle)<p class="mt-1 max-w-[68ch] text-base text-stone-500 sm:text-sm">{{ $subtitle }}</p>@endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">{{ $actions }}</div>
    @endisset
</div>
