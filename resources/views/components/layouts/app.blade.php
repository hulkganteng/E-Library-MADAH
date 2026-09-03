<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Library') }} | {{ $title ?? 'Perpustakaan MA Assadah' }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @php($appFavicon = \App\Models\Setting::get('favicon'))
    @php($appLogo = \App\Models\Setting::get('logo'))
    @php($appSchoolName = \App\Models\Setting::get('school_name', 'MA Assadah'))
    @php($appLibName = \App\Models\Setting::get('library_name', 'E-Library'))

    @if($appFavicon)
        <link rel="icon" href="{{ asset('storage/' . $appFavicon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans bg-slate-100/70 text-slate-800 antialiased selection:bg-emerald-600 selection:text-white">
<div x-data="{ open: false }" class="min-h-full lg:flex">
    @php($user = auth()->user())
    @php($isAuth = (bool)$user)

    {{-- Mobile Overlay Backdrop --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
    ></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-brand-950 text-slate-100 transition-transform duration-200 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:translate-x-0 shrink-0 border-r border-brand-900/60"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
        {{-- Logo Header --}}
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-brand-900/80 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white/10 text-lg font-bold text-brand-950 shadow-sm transition-transform group-hover:scale-105 overflow-hidden p-1">
                    @if($appLogo)
                        <img src="{{ asset('storage/' . $appLogo) }}" class="h-full w-full object-contain" alt="Logo">
                    @else
                        <div class="grid h-full w-full place-items-center rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 text-lg font-bold text-brand-950">
                            م
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold tracking-tight text-white leading-tight">{{ $appLibName ?: 'E-Library' }}</p>
                    <p class="truncate text-[11px] font-medium text-emerald-300/80">{{ $appSchoolName ?: 'MA Assadah' }}</p>
                </div>
            </a>
            <button
                type="button"
                @click="open = false"
                class="grid h-8 w-8 place-items-center rounded-lg text-emerald-200/70 hover:bg-brand-900 hover:text-white lg:hidden transition"
                aria-label="Tutup Menu"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 space-y-6 overflow-y-auto px-3.5 py-4 text-sm">
            {{-- Dashboard & Utama --}}
            <div>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot:icon>
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                    </x-slot:icon>
                    Dashboard
                </x-nav-link>
            </div>

            @if(!$isAuth)
                <div class="space-y-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Koleksi Publik</p>
                    <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">
                        <x-slot:icon>
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        </x-slot:icon>
                        Katalog Buku
                    </x-nav-link>
                    <x-nav-link :href="route('display.index')" :active="request()->routeIs('display.*')">
                        <x-slot:icon>
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
                        </x-slot:icon>
                        Monitor Display
                    </x-nav-link>
                    <x-nav-link :href="route('login')">
                        <x-slot:icon>
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                        </x-slot:icon>
                        Masuk Sistem
                    </x-nav-link>
                </div>
            @else
                {{-- Katalog & Display --}}
                @if($user->can('katalog.view'))
                    <div class="space-y-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Katalog</p>
                        <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            </x-slot:icon>
                            Katalog Buku
                        </x-nav-link>
                        <x-nav-link :href="route('display.index')" :active="request()->routeIs('display.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
                            </x-slot:icon>
                            Monitor Display
                        </x-nav-link>
                    </div>
                @endif

                {{-- Koleksi Buku --}}
                @if($user->canAny(['buku.view', 'eksemplar.view', 'kategori.view', 'penulis.view', 'penerbit.view', 'rak.view']))
                    <div class="space-y-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Koleksi</p>
                        @can('buku.view')
                        <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" /></svg>
                            </x-slot:icon>
                            Manajemen Buku
                        </x-nav-link>
                        @endcan
                        @can('eksemplar.view')
                        <x-nav-link :href="route('copies.index')" :active="request()->routeIs('copies.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /></svg>
                            </x-slot:icon>
                            Eksemplar & QR
                        </x-nav-link>
                        @endcan
                        @can('kategori.view')
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                            </x-slot:icon>
                            Kategori
                        </x-nav-link>
                        @endcan
                        @can('penulis.view')
                        <x-nav-link :href="route('authors.index')" :active="request()->routeIs('authors.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                            </x-slot:icon>
                            Penulis
                        </x-nav-link>
                        @endcan
                        @can('penerbit.view')
                        <x-nav-link :href="route('publishers.index')" :active="request()->routeIs('publishers.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.007v.008h-.007v-.008zm0 3h.007v.008h-.007v-.008zm0 3h.007v.008h-.007v-.008z" /></svg>
                            </x-slot:icon>
                            Penerbit
                        </x-nav-link>
                        @endcan
                        @can('rak.view')
                        <x-nav-link :href="route('shelves.index')" :active="request()->routeIs('shelves.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" /></svg>
                            </x-slot:icon>
                            Rak Buku
                        </x-nav-link>
                        @endcan
                    </div>
                @endif

                {{-- Layanan Sirkulasi --}}
                @if(auth()->user()->can('peminjaman.view'))
                    <div class="space-y-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Layanan</p>
                        <x-nav-link :href="route('loans.index')" :active="request()->routeIs('loans.*')">
                            <x-slot:icon>
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                            </x-slot:icon>
                            Peminjaman & Sirkulasi
                        </x-nav-link>
                    </div>
                @endif

                {{-- Anggota --}}
                @if(auth()->user()->can('siswa.view') || auth()->user()->can('guru.view'))
                    <div class="space-y-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Keanggotaan</p>
                        @can('siswa.view')
                            <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
                                </x-slot:icon>
                                Data Siswa
                            </x-nav-link>
                        @endcan
                        @can('guru.view')
                            <x-nav-link :href="route('teachers.index')" :active="request()->routeIs('teachers.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </x-slot:icon>
                                Data Guru
                            </x-nav-link>
                        @endcan
                        @can('kelas.view')
                            <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6A1.125 1.125 0 012.25 9.375v-2.25zM2.25 14.625c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-2.25zM13.5 7.125c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6A1.125 1.125 0 0113.5 9.375v-2.25zM13.5 14.625c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-2.25z" /></svg>
                                </x-slot:icon>
                                Data Kelas
                            </x-nav-link>
                        @endcan
                    </div>
                @endif

                {{-- Administrasi & Laporan --}}
                @if($user->canAny(['laporan.view', 'pengumuman.view', 'audit.view', 'pengaturan.view']))
                    <div class="space-y-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Administrasi</p>
                        @can('laporan.view')
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                                </x-slot:icon>
                                Laporan & Statistik
                            </x-nav-link>
                        @endcan

                        @can('pengumuman.view')
                            <x-nav-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.455a28.047 28.047 0 01-2.16-6.038m3.822 1.819l3.86-7.72m-3.86 7.72c.451.04 1.054.08 1.8.08 2.08 0 3.75-1.67 3.75-3.75S13.43 8.25 11.35 8.25c-.746 0-1.349.04-1.8.08m0 7.51l3.86-7.72m-3.86 7.72H10.34" /></svg>
                                </x-slot:icon>
                                Pengumuman
                            </x-nav-link>
                        @endcan
                        @can('audit.view')
                            <x-nav-link :href="route('audit.index')" :active="request()->routeIs('audit.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                                </x-slot:icon>
                                Audit Log
                            </x-nav-link>
                        @endcan
                        @can('pengaturan.view')
                            <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                                <x-slot:icon>
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </x-slot:icon>
                                Pengaturan Sistem
                            </x-nav-link>
                        @endcan
                    </div>
                @endif
            @endif
        </nav>

        {{-- Sidebar Footer: User profile / Guest info --}}
        <div class="shrink-0 border-t border-brand-900/80 p-3">
            @if(!$isAuth)
                <div class="rounded-lg bg-brand-900/50 p-3 text-center">
                    <p class="text-xs font-medium text-emerald-200/80">Silakan login untuk meminjam buku & fitur lengkap.</p>
                </div>
            @else
                <div class="flex items-center gap-3 rounded-lg bg-brand-900/40 p-2.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-emerald-800 text-xs font-bold text-emerald-100 uppercase border border-emerald-700/60">
                        {{ str($user->name)->substr(0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-white">{{ $user->name }}</p>
                        <p class="truncate text-[11px] font-medium text-emerald-300/75 capitalize">{{ $user->roles->first()->name ?? 'Pengguna' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </aside>

    {{-- Main Canvas --}}
    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Sticky Top Header --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/90 px-4 sm:px-6 lg:px-8 backdrop-blur-md">
            <div class="flex items-center gap-3 min-w-0">
                <button
                    type="button"
                    @click="open = true"
                    class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 lg:hidden transition"
                    aria-label="Buka Menu"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <div class="min-w-0">
                    <span class="truncate text-sm font-semibold text-slate-800 sm:text-base">{{ $title ?? 'Perpustakaan' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                @if($isAuth)
                    <a
                        href="{{ route('notifications.index') }}"
                        class="relative grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition"
                        title="Notifikasi"
                    >
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        @livewire('notification-bell')
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition"
                            title="Keluar"
                        >
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition"
                    >
                        <span>Masuk</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                @endif
            </div>
        </header>

        {{-- Page Body --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-slate-200/80 bg-white px-6 py-4 text-center text-xs text-slate-500">
            © {{ date('Y') }} Perpustakaan Madrasah Aliyah Assadah. Semua hak dilindungi.
        </footer>
    </div>
</div>

@livewireScripts
<livewire:toast />
</body>
</html>
