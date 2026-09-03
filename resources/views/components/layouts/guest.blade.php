@props(['title' => 'Masuk'])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'E-Library') }} | {{ $title }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @php($guestFavicon = \App\Models\Setting::get('favicon'))
    @if($guestFavicon)
        <link rel="icon" href="{{ asset('storage/' . $guestFavicon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full font-sans bg-slate-900 text-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative antialiased selection:bg-emerald-600 selection:text-white">
    {{-- Calm Islamic / institutional pattern overlay --}}
    <div class="fixed inset-0 bg-gradient-to-br from-brand-950 via-slate-900 to-slate-950 opacity-95"></div>
    <div class="fixed inset-0 bg-[radial-gradient(#166534_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>

    <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-md px-4">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
