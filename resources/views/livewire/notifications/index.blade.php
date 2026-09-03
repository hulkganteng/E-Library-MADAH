@props(['title' => 'Notifikasi'])
<div class="space-y-6">
    <x-page-header
        title="Pusat Notifikasi"
        subtitle="Informasi pengingat batas waktu sirkulasi, status akun, dan pemberitahuan sistem."
    >
        <x-slot:actions>
            <button
                type="button"
                wire:click="markAllRead"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer"
            >
                <svg class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                <span>Tandai Semua Dibaca</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="mx-auto max-w-2xl space-y-3">
        @forelse($notifications as $n)
            <div class="flex items-start gap-3.5 rounded-xl border bg-white p-4 shadow-soft transition {{ !$n->is_read ? 'border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50/20' : 'border-slate-200/80 opacity-80' }}">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ !$n->is_read ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                    @switch($n->type)
                        @case('warning')
                            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @break
                        @case('success')
                            <svg class="h-5 w-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @break
                        @default
                            <svg class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    @endswitch
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-bold text-slate-900">{{ $n->title }}</p>
                    @if($n->body)
                        <p class="mt-0.5 text-xs text-slate-600 leading-relaxed">{{ $n->body }}</p>
                    @endif
                    <p class="mt-1 text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                <button
                    type="button"
                    wire:click="delete({{ $n->id }})"
                    class="text-slate-300 hover:text-rose-600 transition cursor-pointer p-1"
                    title="Hapus notifikasi"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center text-xs text-slate-400">
                Tidak ada notifikasi baru.
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div class="mt-4 pt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
