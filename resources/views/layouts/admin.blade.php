<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Admin PEMIRA Politeknik Negeri Bali">
    <title>{{ $title ?? 'Panel Admin PEMIRA' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="h-full bg-surface-muted text-ink font-sans antialiased selection:bg-accent selection:text-ink overflow-hidden"
      x-data="{ mobileMenuOpen: false }">

    <div class="h-full flex flex-col lg:flex-row overflow-hidden">
        <div x-show="mobileMenuOpen"
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 z-40 bg-ink/70 lg:hidden backdrop-blur-xs"></div>

        <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 bg-surface border-r-2 border-ink flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:h-screen lg:shrink-0 overflow-y-auto">
            
            <div class="flex flex-col h-full justify-between">
                <div>
                    <div class="p-4 sm:p-5 border-b-2 border-ink bg-surface flex items-center justify-between">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                            <div class="w-9 h-9 bg-brand text-accent border-2 border-ink shadow-brutal-sm flex items-center justify-center font-display font-black text-lg group-hover:bg-accent group-hover:text-brand transition-colors">
                                P
                            </div>
                            <div>
                                <span class="font-display font-black text-sm text-brand uppercase tracking-tight block leading-none">
                                    PEMIRA PNB
                                </span>
                                <span class="text-[10px] font-sans font-bold uppercase tracking-wider text-ink/60 mt-1 block">
                                    Panel Admin
                                </span>
                            </div>
                        </a>

                        <button @click="mobileMenuOpen = false"
                                type="button"
                                class="lg:hidden p-1.5 border-2 border-ink bg-surface-muted shadow-brutal-sm hover:bg-accent focus:outline-none"
                                aria-label="Tutup navigasi">
                            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <nav class="p-3 sm:p-4 space-y-1 font-sans" aria-label="Navigasi Admin">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center justify-between px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-brand text-surface' : 'text-ink/80 hover:bg-surface-muted' }} border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wide">
                            <span>Dashboard</span>
                            @if (request()->routeIs('admin.dashboard'))
                                <span class="w-2 h-2 bg-accent inline-block"></span>
                            @endif
                        </a>

                        <a href="{{ route('admin.elections.index') }}"
                           class="flex items-center justify-between px-3 py-2 {{ request()->routeIs('admin.elections.*') ? 'bg-brand text-surface' : 'text-ink/80 hover:bg-surface-muted' }} border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wide">
                            <span>Pemilihan</span>
                            @if (request()->routeIs('admin.elections.*'))
                                <span class="w-2 h-2 bg-accent inline-block"></span>
                            @endif
                        </a>

                        <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                            <span>Pasangan Calon</span>
                            <span class="text-[9px] px-1 py-0.5 bg-surface-muted border border-ink/20 text-ink/50 font-display font-bold">SEGERA</span>
                        </div>

                        <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                            <span>Daftar Pemilih</span>
                            <span class="text-[9px] px-1 py-0.5 bg-surface-muted border border-ink/20 text-ink/50 font-display font-bold">SEGERA</span>
                        </div>

                        <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                            <span>Hasil Perhitungan</span>
                            <span class="text-[9px] px-1 py-0.5 bg-surface-muted border border-ink/20 text-ink/50 font-display font-bold">SEGERA</span>
                        </div>

                        <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                            <span>Masukan Pemilih</span>
                            <span class="text-[9px] px-1 py-0.5 bg-surface-muted border border-ink/20 text-ink/50 font-display font-bold">SEGERA</span>
                        </div>

                        <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                            <span>Audit & Log</span>
                            <span class="text-[9px] px-1 py-0.5 bg-surface-muted border border-ink/20 text-ink/50 font-display font-bold">SEGERA</span>
                        </div>

                        @if (auth()->user()?->isSuperAdmin())
                            <div class="pt-3 mt-3 border-t-2 border-ink/15">
                                <div class="px-3 pb-1 text-[10px] font-display font-black text-brand uppercase tracking-wider">
                                    Super Admin
                                </div>
                                <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                                    <span>Manajemen Admin</span>
                                    <span class="text-[9px] px-1 py-0.5 bg-accent/20 border border-ink/20 text-brand font-display font-bold">SEGERA</span>
                                </div>
                                <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                                    <span>Pengajuan Jadwal</span>
                                    <span class="text-[9px] px-1 py-0.5 bg-accent/20 border border-ink/20 text-brand font-display font-bold">SEGERA</span>
                                </div>
                                <div class="flex items-center justify-between px-3 py-2 border-2 border-transparent text-ink/40 text-xs font-semibold uppercase tracking-wide cursor-not-allowed">
                                    <span>Tindakan Khusus</span>
                                    <span class="text-[9px] px-1 py-0.5 bg-accent/20 border border-ink/20 text-brand font-display font-bold">SEGERA</span>
                                </div>
                            </div>
                        @endif
                    </nav>
                </div>

                <div class="p-3.5 border-t-2 border-ink bg-surface relative" x-data="{ menuOpen: false }">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-8 h-8 bg-brand text-surface border-2 border-ink shadow-brutal-sm flex items-center justify-center font-display font-black text-xs uppercase shrink-0">
                                {{ substr(auth()->user()?->email ?? 'A', 0, 2) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-display font-bold text-xs text-ink truncate uppercase leading-tight">
                                    {{ auth()->user()?->getAdminDisplayName() }}
                                </div>
                                <div class="text-[10px] font-sans text-ink/60 truncate leading-tight mt-0.5">
                                    {{ auth()->user()?->email }}
                                </div>
                            </div>
                        </div>

                        <button @click="menuOpen = !menuOpen"
                                type="button"
                                class="w-8 h-8 border-2 border-ink bg-surface-muted hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                                aria-label="Menu akun admin"
                                :aria-expanded="menuOpen">
                            <svg class="w-4 h-4 text-ink" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="2"/>
                                <circle cx="12" cy="12" r="2"/>
                                <circle cx="12" cy="19" r="2"/>
                            </svg>
                        </button>
                    </div>

                    <div x-show="menuOpen"
                         x-cloak
                         @click.outside="menuOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute bottom-full left-3.5 right-3.5 mb-2 bg-surface border-2 border-ink shadow-brutal p-1.5 space-y-1 z-50">
                        <a href="{{ route('home') }}"
                           class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-display font-bold uppercase tracking-wide text-ink hover:bg-accent border border-transparent hover:border-ink transition-colors">
                            <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Halaman Utama</span>
                        </a>

                        <div class="border-t border-ink/15 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-display font-bold uppercase tracking-wide text-red-700 hover:bg-red-600 hover:text-white border border-transparent hover:border-ink transition-colors cursor-pointer text-left">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Keluar Panel</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
            <header class="sticky top-0 z-30 bg-surface border-b-2 border-ink px-3 sm:px-6 lg:px-8 py-2.5 sm:py-3 flex items-center justify-between gap-2.5 sm:gap-4 shrink-0">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <button @click="mobileMenuOpen = true"
                            type="button"
                            class="lg:hidden w-10 h-10 border-2 border-ink bg-surface shadow-brutal-sm hover:bg-accent focus:outline-none flex items-center justify-center shrink-0"
                            aria-label="Buka menu navigasi">
                        <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="font-display font-extrabold text-sm sm:text-lg text-ink tracking-tight uppercase leading-tight truncate min-w-0">
                        {{ $heading ?? 'PEMIRA PNB' }}
                    </h1>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold"
                         x-data="{
                            clock: '',
                            updateClock() {
                                const now = new Date();
                                const options = { timeZone: 'Asia/Makassar', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                                this.clock = new Intl.DateTimeFormat('en-GB', options).format(now) + ' WITA';
                            },
                            init() {
                                this.updateClock();
                                setInterval(() => this.updateClock(), 1000);
                            }
                         }">
                        <span class="w-2 h-2 bg-brand inline-block"></span>
                        <span class="font-display font-bold text-brand" x-text="clock">--:--:-- WITA</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-1 bg-brand text-surface border-2 border-ink shadow-brutal-sm text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 bg-accent inline-block"></span>
                        <span>{{ auth()->user()?->isSuperAdmin() ? 'SUPER ADMIN' : 'ADMIN KPR' }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-3 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-4 sm:space-y-8 min-w-0">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>
