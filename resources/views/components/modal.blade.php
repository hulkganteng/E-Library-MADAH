@props(['show' => false, 'title' => '', 'maxWidth' => 'max-w-lg'])
<div
    x-data="{ open: @entangle('showModal') }"
    x-cloak
    x-show="open"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        @click="open = false"
    ></div>

    {{-- Modal Card --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative z-10 max-h-[90vh] w-full {{ $maxWidth }} overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 shadow-dropdown"
    >
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
            <button
                type="button"
                @click="open = false"
                class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
