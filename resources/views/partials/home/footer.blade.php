@props([
    'election' => null,
    'brandName' => 'PEMIRA',
    'period' => null,
])

@php
    $humasWaNumber = env('HUMAS_WHATSAPP', config('pemira.contacts.humas.whatsapp_number', '6281337534761'));
    $ketuaWaNumber = env('KETUA_PANITIA_WHATSAPP', config('pemira.contacts.ketua_panitia.whatsapp_number', '628970898383'));

    $humasWhatsappUrl = "https://wa.me/{$humasWaNumber}?text=".rawurlencode('Halo kak Sintya (Humas PEMIRA), saya membutuhkan informasi seputar PEMIRA.');
    $ketuaWhatsappUrl = "https://wa.me/{$ketuaWaNumber}?text=".rawurlencode('Halo kak Diana (Ketua Panitia PEMIRA), saya membutuhkan informasi seputar PEMIRA.');
    $whatsappUrl = $humasWhatsappUrl;
    $resolvedPeriod = $period ?? ($election?->year ? "'".substr((string) $election->year, -2) : "'26");
@endphp

<footer class="w-full bg-brand text-surface border-t-2 border-ink relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div class="hidden md:block absolute -bottom-10 -right-6 font-display font-black text-8xl lg:text-9xl text-surface/5 tracking-tighter leading-none">
            {{ $election ? $election->year : date('Y') }}
        </div>

        <div class="hidden lg:block absolute top-0 left-1/3 w-px h-full bg-surface/10"></div>
        <div class="hidden lg:block absolute top-0 right-1/4 w-px h-full bg-surface/10"></div>

        <div class="hidden sm:flex absolute top-8 left-12 text-surface/20 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden sm:flex absolute top-12 right-1/3 text-surface/20 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden lg:block absolute top-1/3 left-8 w-3 h-3 bg-accent border border-ink"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start">
            <div class="md:col-span-5 lg:col-span-6 flex flex-col items-start">
                <a href="{{ route('home') }}#beranda" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none focus:ring-2 focus:ring-accent mb-4">
                    <div class="inline-flex items-center gap-2 sm:gap-2.5 p-1.5 sm:p-2 bg-surface border-2 border-ink shadow-brutal-sm shrink-0">
                        <img
                            src="{{ asset('img/logos/organization/pnb-logo.png') }}"
                            alt="Logo Politeknik Negeri Bali"
                            class="h-8 w-auto sm:h-10 object-contain"
                        >
                        <div class="h-6 sm:h-7 w-0.5 bg-ink/20"></div>
                        <img
                            src="{{ asset('img/logos/organization/kpr-logo-no-text.png') }}"
                            alt="Logo KPR"
                            class="h-8 w-auto sm:h-10 object-contain"
                        >
                    </div>
                    <div class="flex items-baseline font-display">
                        <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-surface">
                            {{ $brandName }}
                        </span>
                        <span class="text-xl sm:text-2xl font-extrabold text-accent ml-1">
                            {{ $resolvedPeriod }}
                        </span>
                    </div>
                </a>

                <p class="text-xs sm:text-sm font-sans font-medium text-surface/80 max-w-md leading-relaxed mb-4 sm:mb-6">
                    Pusat informasi dan partisipasi Pemilihan Raya Mahasiswa untuk menentukan pemimpin BEM Politeknik Negeri Bali.
                </p>

                <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-brand-dark border border-surface/20 text-xs font-sans font-bold uppercase tracking-wider text-accent">
                    <span class="w-1.5 h-1.5 bg-accent inline-block border border-ink"></span>
                    <span>KOMISI PEMILIHAN RAYA {{ $election ? $election->year : date('Y') }}</span>
                </div>
            </div>

            <div class="md:col-span-3 lg:col-span-3">
                <div class="text-xs font-display font-bold uppercase tracking-widest text-accent mb-3 sm:mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-accent inline-block border border-ink"></span>
                    <span>NAVIGASI</span>
                </div>

                <ul class="space-y-2 text-xs sm:text-sm font-sans font-medium">
                    <li>
                        <a
                            href="{{ route('home') }}#beranda"
                            class="text-surface/80 hover:text-accent hover:translate-x-1 inline-block transition-all focus:outline-none focus:ring-1 focus:ring-accent"
                        >
                            BERANDA
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('home') }}#jadwal"
                            class="text-surface/80 hover:text-accent hover:translate-x-1 inline-block transition-all focus:outline-none focus:ring-1 focus:ring-accent"
                        >
                            JADWAL
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('home') }}#paslon"
                            class="text-surface/80 hover:text-accent hover:translate-x-1 inline-block transition-all focus:outline-none focus:ring-1 focus:ring-accent"
                        >
                            PASLON
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('home') }}#cara-memilih"
                            class="text-surface/80 hover:text-accent hover:translate-x-1 inline-block transition-all focus:outline-none focus:ring-1 focus:ring-accent"
                        >
                            CARA MEMILIH
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('home') }}#faq"
                            class="text-surface/80 hover:text-accent hover:translate-x-1 inline-block transition-all focus:outline-none focus:ring-1 focus:ring-accent"
                        >
                            FAQ
                        </a>
                    </li>
                </ul>
            </div>

            <div class="md:col-span-4 lg:col-span-3">
                <div class="text-xs font-display font-bold uppercase tracking-widest text-accent mb-3 sm:mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-accent inline-block border border-ink"></span>
                    <span>BANTUAN & KONTAK</span>
                </div>

                <p class="text-xs sm:text-sm font-sans font-medium text-surface/80 leading-relaxed mb-3 sm:mb-4">
                    Hubungi panitia PEMIRA melalui WhatsApp jika membutuhkan bantuan atau informasi lebih lanjut:
                </p>

                <div class="space-y-2">
                    <a
                        href="{{ $humasWhatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-between gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-ink bg-accent border-2 border-ink shadow-brutal hover:bg-accent-light hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-surface"
                    >
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] font-sans font-extrabold text-ink/75 leading-tight">HUMAS</span>
                            <span class="font-extrabold">SINTYA</span>
                        </div>
                        <span class="font-display text-xs font-bold">CHAT WHATSAPP &rarr;</span>
                    </a>

                    <a
                        href="{{ $ketuaWhatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-between gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand-dark border-2 border-surface/30 hover:border-accent hover:text-accent shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-surface"
                    >
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] font-sans font-extrabold text-accent leading-tight">KETUA PANITIA</span>
                            <span class="font-extrabold">DIANA</span>
                        </div>
                        <span class="font-display text-xs font-bold text-surface/90">CHAT WHATSAPP &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t-2 border-surface/15 bg-brand-dark py-3.5 sm:py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-sans font-medium text-surface/60 text-center sm:text-left">
            <span>{{ $election ? $election->name : 'PEMIRA' }} — Pemilihan Raya Mahasiswa</span>
            <span>&copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR). All rights reserved.</span>
        </div>
    </div>
</footer>
