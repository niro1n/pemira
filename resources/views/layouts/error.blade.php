<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Terjadi Kesalahan' }} — PEMIRA 2026</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logos/organization/kpr-logo-no-text.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Space+Grotesk:wght@300..700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-surface-muted text-ink font-sans antialiased selection:bg-accent selection:text-ink flex flex-col justify-between">
    <header class="w-full bg-surface border-b-2 border-ink">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none focus:ring-2 focus:ring-brand">
                    <img
                        src="{{ asset('img/logos/organization/kpr-logo-no-text.png') }}"
                        alt="Logo KPR"
                        class="h-9 w-auto sm:h-11 object-contain shrink-0"
                    >
                    <div class="flex items-baseline font-display">
                        <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-brand">
                            PEMIRA
                        </span>
                        <span class="text-xl sm:text-2xl font-extrabold text-accent ml-1">
                            '26
                        </span>
                    </div>
                </a>

                <div class="flex items-center">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-display font-bold uppercase tracking-wider bg-surface-muted border-2 border-ink shadow-brutal-sm text-brand">
                        STATUS SISTEM
                    </span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="w-full bg-surface border-t-2 border-ink py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-mono text-ink/70">
            <span>KPR PEMIRA POLITEKNIK NEGERI BALI 2026</span>
            <span>PEMILIHAN UMUM RAYA MAHASISWA</span>
        </div>
    </footer>
</body>

</html>
