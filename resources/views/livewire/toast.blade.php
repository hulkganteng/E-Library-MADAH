@props(['title' => ''])
<div
    x-data
    x-cloak
    x-show="$wire.visible"
    x-init="$watch('$wire.visible', v => { if(v) setTimeout(() => $wire.set('visible', false), 3500) })"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="pointer-events-none fixed inset-x-0 bottom-5 z-[70] flex justify-center px-4 sm:justify-end sm:px-6"
>
    <div class="pointer-events-auto flex items-center gap-3 rounded-xl px-4 py-3 text-xs sm:text-sm font-semibold shadow-soft border {{ $type === 'error' ? 'bg-rose-900/95 text-white border-rose-800' : 'bg-emerald-900/95 text-white border-emerald-800' }} backdrop-blur">
        @if($type === 'error')
            <svg class="h-4.5 w-4.5 text-rose-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        @else
            <svg class="h-4.5 w-4.5 text-emerald-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endif
        <span class="leading-snug">{{ $message }}</span>
        <button
            type="button"
            @click="$wire.set('visible', false)"
            class="ml-2 rounded p-0.5 text-white/70 hover:text-white hover:bg-white/10 transition cursor-pointer"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
</div>
