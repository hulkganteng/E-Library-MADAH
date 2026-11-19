@props(['title' => 'Masuk'])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'E-Library') }} | {{ $title }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @php($guestFavicon = \App\Models\Setting::get('favicon'))
    @if($guestFavicon)
        <link rel="icon" href="{{ asset('storage/' . $guestFavicon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="relative flex min-h-dvh flex-col justify-center bg-brand-950 py-12 font-sans text-stone-100 antialiased selection:bg-emerald-600 selection:text-white sm:px-6 lg:px-8">
    <div class="absolute inset-y-0 left-0 w-1 bg-amber-400" aria-hidden="true"></div>
    <div class="relative z-10 px-4 sm:mx-auto sm:w-full sm:max-w-sm">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
