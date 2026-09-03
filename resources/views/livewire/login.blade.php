@props(['title' => 'Masuk'])
<div class="w-full max-w-md mx-auto">
    <div class="rounded-2xl border border-slate-200/90 bg-white p-7 sm:p-9 shadow-soft text-slate-800">
        {{-- Header & Branding --}}
        @php($loginLogo = \App\Models\Setting::get('logo'))
        @php($loginSchoolName = \App\Models\Setting::get('school_name', 'Madrasah Aliyah Assadah'))
        @php($loginLibName = \App\Models\Setting::get('library_name', 'E-Library'))

        <div class="text-center mb-7">
            <div class="mx-auto mb-3.5 grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 border border-emerald-100 shadow-2xs overflow-hidden p-1.5">
                @if($loginLogo)
                    <img src="{{ asset('storage/' . $loginLogo) }}" class="h-full w-full object-contain" alt="Logo">
                @else
                    <div class="grid h-full w-full place-items-center rounded-xl bg-emerald-700 text-white font-serif font-bold text-xl">
                        م
                    </div>
                @endif
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 font-serif">{{ $loginLibName ?: 'E-Library' }}</h1>
            <p class="mt-1 text-xs text-slate-500 font-medium">{{ $loginSchoolName ?: 'Madrasah Aliyah Assadah' }}</p>
        </div>

        {{-- Session Flash Alert --}}
        @if(session('status'))
            <div class="mb-5 flex items-center gap-2 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-xs font-medium text-emerald-800">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Form Login --}}
        <form wire:submit="login" class="space-y-4">
            <div>
                <x-input
                    wire:model="email"
                    type="email"
                    label="Alamat Email"
                    required
                    placeholder="nama@assaadah.sch.id"
                    :error="$errors->first('email')"
                />
            </div>

            <div>
                <x-input
                    wire:model="password"
                    type="password"
                    label="Kata Sandi"
                    required
                    placeholder="••••••••"
                    :error="$errors->first('password')"
                />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-emerald-600/20 active:bg-emerald-900 transition duration-150 cursor-pointer"
                >
                    <span wire:loading.remove wire:target="login">Masuk ke Akun</span>
                    <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memverifikasi...</span>
                    </span>
                </button>
            </div>
        </form>

        {{-- Back to Catalog Link --}}
        <div class="mt-7 pt-5 border-t border-slate-100 text-center">
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-700 transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                <span>Kembali ke Katalog Buku</span>
            </a>
        </div>
    </div>
</div>
