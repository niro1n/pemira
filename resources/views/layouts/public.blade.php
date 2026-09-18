<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description ?? 'Pemilihan Raya Politeknik Negeri Bali' }}">
    <title>{{ $title ?? 'PEMIRA' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen bg-surface text-ink font-sans antialiased">
    {{ $slot ?? '' }}
    @yield('content')

    @livewireScripts
</body>

</html>
