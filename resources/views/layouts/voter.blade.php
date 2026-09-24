<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description ?? 'Portal Pemilih Pemilihan Raya Politeknik Negeri Bali' }}">
    <title>{{ $title ?? 'Portal Pemilih' }} — PEMIRA PNB</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Space+Grotesk:wght@300..700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen bg-surface-muted text-ink font-sans antialiased flex flex-col selection:bg-accent selection:text-ink">
    @php
        $user = auth()->user();
        $eligibleVoter = $user?->voterAccount?->eligibleVoter;
        $displayName = $eligibleVoter?->name ?? $user?->email;
        $nim = $eligibleVoter?->nim;
    @endphp

    <header class="sticky top-0 z-40 bg-surface border-b-2 border-ink">
        <nav
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
            x-data="{ mobileOpen: false }"
        >
            <div class="flex items-center justify-between h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <a href="{{ route('voter.dashboard') }}" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none focus:ring-2 focus:ring-brand">
                        <img
                            src="{{ asset('img/logos/organization/kpr-logo-no-text.png') }}"
                            alt="Logo KPR"
                            class="h-9 w-auto sm:h-10 object-contain shrink-0"
                            onerror="this.style.display='none'"
                        >
                        <div class="flex flex-col">
                            <div class="flex items-baseline font-display">
                                <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-brand">PEMIRA</span>
                                <span class="text-xl sm:text-2xl font-extrabold text-accent ml-1">PORTAL</span>
                            </div>
                            <span class="text-[10px] font-bold tracking-widest text-ink/70 uppercase -mt-1 hidden sm:block">Politeknik Negeri Bali</span>
                        </div>
                    </a>
                </div>

                <div class="hidden md:flex items-center gap-1 sm:gap-2">
                    <a
                        href="{{ route('voter.dashboard') }}"
                        class="px-4 py-2 text-sm font-display font-bold tracking-wide uppercase transition-all border-2 {{ request()->routeIs('voter.dashboard') ? 'border-ink bg-brand text-surface shadow-brutal-sm' : 'border-transparent text-ink hover:border-ink hover:bg-surface' }} focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        Dashboard
                    </a>

                    <a
                        href="{{ route('voter.profile') }}"
                        class="px-4 py-2 text-sm font-display font-bold tracking-wide uppercase transition-all border-2 {{ request()->routeIs('voter.profile') ? 'border-ink bg-brand text-surface shadow-brutal-sm' : 'border-transparent text-ink hover:border-ink hover:bg-surface' }} focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        Profil DPT
                    </a>

                    <div class="h-6 w-0.5 bg-ink/20 mx-2"></div>

                    <div class="flex items-center gap-2.5 px-3 py-1.5 bg-surface border-2 border-ink shadow-brutal-sm text-left">
                        <div class="w-8 h-8 rounded-none bg-brand text-surface font-display font-black text-sm flex items-center justify-center border border-ink shrink-0">
                            {{ strtoupper(substr($displayName, 0, 1)) }}
                        </div>
                        <div class="flex flex-col leading-tight max-w-35 truncate">
                            <span class="text-xs font-bold text-ink truncate">{{ $displayName }}</span>
                            @if ($nim)
                                <span class="text-[10px] font-mono font-medium text-ink/70">{{ $nim }}</span>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline-block ml-2">
                        @csrf
                        <button
                            type="submit"
                            class="px-3.5 py-2 text-xs font-display font-bold uppercase tracking-wider text-surface bg-red-600 border-2 border-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none hover:bg-red-700 active:translate-x-1 active:translate-y-1 transition-all focus:outline-none focus:ring-2 focus:ring-red-600"
                            title="Keluar dari akun"
                        >
                            Keluar
                        </button>
                    </form>
                </div>

                <div class="flex md:hidden items-center gap-2">
                    <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        :aria-expanded="mobileOpen ? 'true' : 'false'"
                        aria-label="Menu navigasi"
                        class="inline-flex items-center justify-center p-2 text-ink bg-surface border-2 border-ink shadow-brutal-sm hover:bg-surface-muted active:translate-x-0.5 active:translate-y-0.5 active:shadow-none focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        <svg x-show="!mobileOpen" class="w-6 h-6 block" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-linejoin="miter" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6 block" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="square" stroke-linejoin="miter" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div
                x-show="mobileOpen"
                x-cloak
                @click.outside="mobileOpen = false"
                class="md:hidden border-t-2 border-ink bg-surface px-4 pt-3 pb-5 space-y-3"
            >
                <div class="p-3 bg-surface-muted border-2 border-ink flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand text-surface font-display font-black text-base flex items-center justify-center border border-ink shrink-0">
                        {{ strtoupper(substr($displayName, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-ink truncate">{{ $displayName }}</p>
                        @if ($nim)
                            <p class="text-xs font-mono text-ink/70">NIM: {{ $nim }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-1">
                    <a
                        href="{{ route('voter.dashboard') }}"
                        class="block px-3 py-2 text-sm font-display font-bold uppercase border-2 {{ request()->routeIs('voter.dashboard') ? 'border-ink bg-brand text-surface shadow-brutal-sm' : 'border-transparent text-ink hover:border-ink hover:bg-surface-muted' }}"
                    >
                        Dashboard
                    </a>
                    <a
                        href="{{ route('voter.profile') }}"
                        class="block px-3 py-2 text-sm font-display font-bold uppercase border-2 {{ request()->routeIs('voter.profile') ? 'border-ink bg-brand text-surface shadow-brutal-sm' : 'border-transparent text-ink hover:border-ink hover:bg-surface-muted' }}"
                    >
                        Profil DPT
                    </a>
                </div>

                <div class="pt-2 border-t border-ink/20">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-center px-4 py-2.5 text-sm font-display font-bold uppercase tracking-wider text-surface bg-red-600 border-2 border-ink shadow-brutal-sm hover:bg-red-700 active:translate-x-0.5 active:translate-y-0.5 transition-all"
                        >
                            Keluar dari Akun
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    @if (session('success') || session('error') || session('warning') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            @if (session('success'))
                <div class="bg-green-100 border-2 border-ink p-4 shadow-brutal-sm flex items-start gap-3">
                    <svg class="w-6 h-6 text-green-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <div class="text-sm font-semibold text-green-900">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-2 border-ink p-4 shadow-brutal-sm flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm font-semibold text-red-900">{{ session('error') }}</div>
                </div>
            @endif
            @if (session('warning'))
                <div class="bg-amber-100 border-2 border-ink p-4 shadow-brutal-sm flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm font-semibold text-amber-900">{{ session('warning') }}</div>
                </div>
            @endif
        </div>
    @endif

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 w-full">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="bg-surface border-t-2 border-ink mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-ink/70">
            <div class="flex items-center gap-2">
                <span class="font-display font-bold text-ink">PEMIRA POLITEKNIK NEGERI BALI</span>
                <span>&bull;</span>
                <span>Bilik Suara Terenkripsi & Anonim</span>
            </div>
            <div>
                &copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR). Hak cipta dilindungi.
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>
