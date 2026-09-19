@props([
    'election' => null,
])

<section id="beranda" class="relative w-full bg-surface border-b-2 border-ink overflow-hidden">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div
            class="hidden md:block absolute -top-10 -left-6 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            {{ $election ? $election->name : 'PEMIRA' }}
        </div>
        <div
            class="hidden lg:block absolute -bottom-16 right-10 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            {{ $election ? $election->year : date('Y') }}
        </div>

        <svg class="absolute top-12 right-12 w-48 h-48 text-ink/10 hidden sm:block" fill="currentColor">
            <pattern id="hero-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#hero-dots)" />
        </svg>

        <svg class="absolute bottom-8 left-8 w-36 h-36 text-ink/10 hidden md:block" fill="currentColor">
            <pattern id="hero-dots-2" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#hero-dots-2)" />
        </svg>

        <div class="hidden lg:block absolute top-0 left-1/4 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 right-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden md:block absolute top-1/2 left-0 w-full h-px bg-ink/10"></div>

        <div class="hidden sm:flex absolute top-8 left-12 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden sm:flex absolute top-24 left-1/3 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden lg:flex absolute bottom-24 left-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden md:flex absolute top-16 right-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div
            class="hidden sm:flex absolute bottom-12 right-1/3 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>

        <div class="hidden lg:block absolute top-1/4 right-8 w-3 h-3 bg-accent border border-ink"></div>
        <div class="hidden lg:block absolute bottom-1/3 left-6 w-2.5 h-2.5 bg-brand border border-ink"></div>
        <div class="hidden md:block absolute top-10 right-1/2 w-2 h-2 bg-accent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 md:py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-16 items-center">
            <div class="lg:col-span-7 flex flex-col items-start">
                <div
                    class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-3.5 py-1 sm:py-1.5 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs sm:text-sm font-sans font-bold tracking-wider uppercase text-brand mb-4 sm:mb-6 lg:mb-8">
                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block border border-ink"></span>
                    <span>PEMILIHAN RAYA MAHASISWA</span>
                </div>

                <h1
                    class="font-display font-extrabold text-3xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight uppercase mb-3 sm:mb-4 lg:mb-6 leading-none space-y-0.5 sm:space-y-1 lg:space-y-2">
                    <span class="block text-brand">SUARAMU.</span>
                    <span class="block text-brand">PILIHANMU.</span>
                    <span class="block text-ink">MASA DEPAN KAMPUSMU.</span>
                </h1>

                <p
                    class="text-sm sm:text-base lg:text-xl text-ink font-sans font-medium max-w-xl mb-5 sm:mb-8 lg:mb-10 leading-snug sm:leading-relaxed">
                    Tentukan pilihanmu untuk pemimpin BEM pada {{ $election ? $election->name : 'PEMIRA' }}. Satu suara, satu langkah besar untuk
                    perubahan di kampus kita.
                </p>

                <div
                    class="flex flex-row flex-wrap items-center gap-2.5 sm:gap-4 w-full sm:w-auto mb-6 sm:mb-8 lg:mb-12">
                    <a href="#paslon"
                        class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-4 sm:px-6 lg:px-7 py-2.5 sm:py-3.5 text-xs sm:text-sm lg:text-base font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                        <span>LIHAT PASLON</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="#cara-memilih"
                        class="inline-flex items-center justify-center px-4 sm:px-6 lg:px-7 py-2.5 sm:py-3.5 text-xs sm:text-sm lg:text-base font-display font-bold tracking-wide uppercase text-ink bg-surface-muted border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-surface active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                        CARA MEMILIH
                    </a>
                </div>

                <div class="w-full pt-4 sm:pt-6 lg:pt-8 border-t-2 border-ink grid grid-cols-3 gap-2 sm:gap-4 lg:gap-6">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5 sm:gap-2 mb-0.5 sm:mb-1">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-brand inline-block shrink-0"></span>
                            <span
                                class="font-display font-bold text-xs lg:text-sm uppercase tracking-tight sm:tracking-wide text-brand">
                                DARI MAHASISWA
                            </span>
                        </div>
                        <span class="hidden sm:block text-xs font-sans text-ink font-medium">
                            Untuk mahasiswa
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5 sm:gap-2 mb-0.5 sm:mb-1">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-accent inline-block shrink-0"></span>
                            <span
                                class="font-display font-bold text-xs lg:text-sm uppercase tracking-tight sm:tracking-wide text-brand">
                                TRANSPARAN
                            </span>
                        </div>
                        <span class="hidden sm:block text-xs font-sans text-ink font-medium">
                            Proses pemilihan yang jelas
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5 sm:gap-2 mb-0.5 sm:mb-1">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-brand inline-block shrink-0"></span>
                            <span
                                class="font-display font-bold text-xs lg:text-sm uppercase tracking-tight sm:tracking-wide text-brand">
                                SATU SUARA
                            </span>
                        </div>
                        <span class="hidden sm:block text-xs font-sans text-ink font-medium">
                            Satu mahasiswa, satu suara
                        </span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 w-full">
                <div class="relative w-full max-w-sm sm:max-w-md lg:max-w-none mx-auto">
                    <div
                        class="absolute -bottom-2 -right-2 sm:-bottom-4 sm:-right-4 w-full h-full bg-accent border-2 border-ink">
                    </div>

                    <div class="relative bg-surface border-2 border-ink shadow-brutal-sm sm:shadow-brutal-lg">
                        <div
                            class="bg-brand text-surface px-3 sm:px-5 py-2 sm:py-3 border-b-2 border-ink flex items-center justify-between">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block"></span>
                                <span class="font-display font-bold text-xs sm:text-sm uppercase tracking-wider">
                                    {{ $election ? $election->name : "PEMIRA '26" }}
                                </span>
                            </div>
                            <span
                                class="bg-accent text-ink px-2 sm:px-2.5 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                E-VOTING RESMI
                            </span>
                        </div>

                        <div class="p-3.5 sm:p-6 lg:p-8 space-y-3 sm:space-y-6">
                            <div class="flex items-start justify-between gap-3 sm:gap-4">
                                <div>
                                    <div
                                        class="text-xs font-sans font-bold uppercase tracking-widest text-brand mb-0.5 sm:mb-1">
                                        TAHUN PEMILIHAN
                                    </div>
                                    <div
                                        class="font-display font-black text-3xl sm:text-6xl lg:text-7xl text-brand leading-none tracking-tight">
                                        {{ $election ? $election->year : date('Y') }}
                                    </div>
                                </div>
                                <div
                                    class="bg-surface-muted border-2 border-ink p-1.5 sm:p-3 shadow-brutal-sm text-center shrink-0">
                                    <div class="text-xs font-display font-bold uppercase text-brand">
                                        STATUS
                                    </div>
                                    <div
                                        class="text-xs sm:text-sm font-display font-black text-brand mt-0.5">
                                        {{ $election ? $election->statusLabel() : 'BELUM DIBUKA' }}
                                    </div>
                                </div>
                            </div>

                            <div class="h-0.5 w-full bg-ink"></div>

                            <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:gap-4">
                                <div class="bg-surface-muted border-2 border-ink p-2 sm:p-3.5 shadow-brutal-sm">
                                    <div
                                        class="text-xs font-sans font-bold uppercase tracking-wider text-ink">
                                        ASAS
                                    </div>
                                    <div
                                        class="font-display font-bold text-xs sm:text-sm lg:text-base text-brand mt-0.5 sm:mt-1">
                                        LUBER JURDIL
                                    </div>
                                </div>
                                <div class="bg-surface-muted border-2 border-ink p-2 sm:p-3.5 shadow-brutal-sm">
                                    <div
                                        class="text-xs font-sans font-bold uppercase tracking-wider text-ink">
                                        HAK SUARA
                                    </div>
                                    <div
                                        class="font-display font-bold text-xs sm:text-sm lg:text-base text-brand mt-0.5 sm:mt-1">
                                        1 MHS 1 SUARA
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-surface-muted border-t-2 border-ink px-3 sm:px-5 py-2 sm:py-3 flex items-center justify-between text-xs font-display font-bold text-ink">
                            <span class="flex items-center gap-1.5 sm:gap-2">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-brand inline-block"></span>
                                <span>SUARA MAHASISWA BERHARGA</span>
                            </span>
                            <span class="text-brand uppercase">PNB {{ $election ? $election->year : date('Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
