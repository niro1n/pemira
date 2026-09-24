<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description ?? 'Masuk ke Portal Pemilihan Raya Mahasiswa (PEMIRA) Politeknik Negeri Bali' }}">
    <title>{{ $title ?? 'Masuk — PEMIRA 2026' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Space+Grotesk:wght@300..700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen bg-surface text-ink font-sans antialiased selection:bg-accent selection:text-ink">
    {{ $slot ?? '' }}
    @yield('content')

    @livewireScripts
</body>

</html>
