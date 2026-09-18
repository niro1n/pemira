@php
    $candidatePairs = [
        [
            'number' => '01',
            'leader' => 'SUCIPTA',
            'co_leader' => 'SUNGI',
            'vision' =>
                'Membangun organisasi mahasiswa yang inklusif, kolaboratif, dan progresif untuk kemajuan bersama.',
        ],
        [
            'number' => '02',
            'leader' => 'SUCIPTA',
            'co_leader' => 'SUNGI',
            'vision' =>
                'Mewujudkan wadah aspirasi mahasiswa yang berintegritas, responsif, dan berdampak nyata bagi kampus.',
        ],
        [
            'number' => '03',
            'leader' => 'SUCIPTA',
            'co_leader' => 'SUNGI',
            'vision' =>
                'Menjadikan BEM sebagai penggerak inovasi, kreativitas, dan kepemimpinan mahasiswa yang berdaya saing.',
        ],
    ];
@endphp

<section id="paslon"
    class="relative w-full bg-surface border-b-2 border-ink overflow-hidden py-10 sm:py-16 md:py-20 lg:py-24">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div
            class="hidden md:block absolute -top-10 -left-4 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            03
        </div>

        <svg class="absolute top-12 right-10 w-44 h-44 text-ink/10 hidden sm:block pointer-events-none"
            fill="currentColor">
            <pattern id="paslon-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#paslon-dots)" />
        </svg>

        <div class="hidden lg:block absolute top-0 left-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 right-1/4 w-px h-full bg-ink/10"></div>
        <div class="hidden sm:flex absolute top-10 left-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div
            class="hidden sm:flex absolute bottom-10 right-1/3 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden lg:block absolute top-1/4 right-8 w-3 h-3 bg-accent border border-ink"></div>
        <div class="hidden md:block absolute bottom-1/4 left-10 w-2.5 h-2.5 bg-brand"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8 sm:mb-12 lg:mb-16">
            <div
                class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-3.5 py-1 sm:py-1.5 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs sm:text-sm font-sans font-bold tracking-wider uppercase text-brand mb-4 sm:mb-6">
                <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block border border-ink"></span>
                <span>KENALI CALONMU</span>
            </div>

            <h2
                class="font-display font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight uppercase text-ink mb-3 sm:mb-4 leading-none space-y-1">
                <span class="block text-brand">PILIH PEMIMPINMU.</span>
                <span class="block">KENALI GAGASANNYA.</span>
            </h2>

            <p class="text-sm sm:text-base lg:text-lg text-ink font-sans font-medium leading-snug sm:leading-relaxed">
                Kenali pasangan calon, gagasan, visi, dan misi sebelum menentukan pilihanmu pada PEMIRA 2026.
            </p>
        </div>

        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach ($candidatePairs as $candidate)
                <div
                    class="bg-surface border-2 border-ink shadow-brutal-lg flex flex-col justify-between relative group hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                    <div>
                        <div
                            class="bg-brand text-surface px-4 py-3 border-b-2 border-ink flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                                <span class="font-display font-extrabold text-sm uppercase tracking-wider">
                                    PASLON {{ $candidate['number'] }}
                                </span>
                            </div>
                            <span
                                class="bg-accent text-ink px-2.5 py-0.5 text-[10px] sm:text-xs font-display font-bold uppercase border border-ink">
                                KANDIDAT BEM
                            </span>
                        </div>

                        <div class="relative bg-surface-muted border-b-2 border-ink p-4 sm:p-5 overflow-hidden">
                            <div
                                class="absolute -top-3 -right-2 font-display font-black text-6xl sm:text-7xl text-ink/10 select-none pointer-events-none leading-none">
                                {{ $candidate['number'] }}
                            </div>

                            <div class="grid grid-cols-2 gap-3 relative z-10">
                                <div
                                    class="bg-surface border-2 border-ink p-3 shadow-brutal-sm flex flex-col items-center justify-center text-center">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 bg-surface-muted border-2 border-ink flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-brand" fill="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[9px] sm:text-[10px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                        KETUA
                                    </span>
                                    <span class="font-display font-bold text-xs text-brand truncate max-w-full">
                                        {{ explode(' ', $candidate['leader'])[0] }}
                                    </span>
                                </div>

                                <div
                                    class="bg-surface border-2 border-ink p-3 shadow-brutal-sm flex flex-col items-center justify-center text-center">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 bg-surface-muted border-2 border-ink flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-brand" fill="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[9px] sm:text-[10px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                        WAKIL
                                    </span>
                                    <span class="font-display font-bold text-xs text-brand truncate max-w-full">
                                        {{ explode(' ', $candidate['co_leader'])[0] }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="mt-3 pt-2 border-t border-ink/20 flex items-center justify-between text-[10px] font-sans font-bold uppercase tracking-wider text-brand">
                                <span>PORTRAIT RESMI</span>
                                <span class="text-accent">&#9632; VERIFIED</span>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 space-y-3.5">
                            <div class="space-y-2.5">
                                <div class="bg-surface-muted border-2 border-ink p-2.5 shadow-brutal-sm">
                                    <div
                                        class="text-[9px] sm:text-[10px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                        CALON KETUA BEM
                                    </div>
                                    <div
                                        class="font-display font-bold text-sm sm:text-base text-brand uppercase leading-tight mt-0.5">
                                        {{ $candidate['leader'] }}
                                    </div>
                                </div>

                                <div class="bg-surface-muted border-2 border-ink p-2.5 shadow-brutal-sm">
                                    <div
                                        class="text-[9px] sm:text-[10px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                        CALON WAKIL KETUA BEM
                                    </div>
                                    <div
                                        class="font-display font-bold text-sm sm:text-base text-brand uppercase leading-tight mt-0.5">
                                        {{ $candidate['co_leader'] }}
                                    </div>
                                </div>
                            </div>

                            <div class="border-t-2 border-ink pt-3">
                                <div
                                    class="text-[10px] sm:text-xs font-sans font-bold uppercase tracking-wider text-brand mb-1">
                                    RINGKASAN VISI
                                </div>
                                <p class="text-xs sm:text-sm font-sans italic text-ink font-medium leading-relaxed">
                                    &ldquo;{{ $candidate['vision'] }}&rdquo;
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 pt-0">
                        <a href="#paslon"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                            <span>LIHAT PROFIL</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="block md:hidden overflow-hidden" x-data="{
            currentIndex: 0,
            totalSlides: {{ count($candidatePairs) }},
            touchStartX: 0,
            touchEndX: 0,
            prev() {
                if (this.currentIndex > 0) this.currentIndex--;
            },
            next() {
                if (this.currentIndex < this.totalSlides - 1) this.currentIndex++;
            },
            handleTouchStart(e) {
                this.touchStartX = e.changedTouches[0].screenX;
            },
            handleTouchEnd(e) {
                this.touchEndX = e.changedTouches[0].screenX;
                const diffX = this.touchStartX - this.touchEndX;
                if (Math.abs(diffX) > 40) {
                    if (diffX > 0 && this.currentIndex < this.totalSlides - 1) {
                        this.next();
                    } else if (diffX < 0 && this.currentIndex > 0) {
                        this.prev();
                    }
                }
            }
        }" @touchstart="handleTouchStart($event)"
            @touchend="handleTouchEnd($event)">
            <div class="flex transition-transform duration-300 ease-out"
                :style="`transform: translateX(-${currentIndex * 100}%)`">
                @foreach ($candidatePairs as $index => $candidate)
                    <div class="w-full shrink-0 px-0.5">
                        <div
                            class="bg-surface border-2 border-ink shadow-brutal-lg flex flex-col justify-between relative">
                            <div>
                                <div
                                    class="bg-brand text-surface px-4 py-2.5 border-b-2 border-ink flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                                        <span class="font-display font-extrabold text-sm uppercase tracking-wider">
                                            PASLON {{ $candidate['number'] }}
                                        </span>
                                    </div>
                                    <span
                                        class="bg-accent text-ink px-2 py-0.5 text-[10px] font-display font-bold uppercase border border-ink">
                                        KANDIDAT BEM
                                    </span>
                                </div>

                                <div class="relative bg-surface-muted border-b-2 border-ink p-4 overflow-hidden">
                                    <div
                                        class="absolute -top-3 -right-2 font-display font-black text-6xl text-ink/10 select-none pointer-events-none leading-none">
                                        {{ $candidate['number'] }}
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5 relative z-10">
                                        <div
                                            class="bg-surface border-2 border-ink p-2.5 shadow-brutal-sm flex flex-col items-center justify-center text-center">
                                            <div
                                                class="w-10 h-10 bg-surface-muted border-2 border-ink flex items-center justify-center mb-1.5">
                                                <svg class="w-5 h-5 text-brand" fill="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path
                                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-[9px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                                KETUA
                                            </span>
                                            <span
                                                class="font-display font-bold text-xs text-brand truncate max-w-full">
                                                {{ explode(' ', $candidate['leader'])[0] }}
                                            </span>
                                        </div>

                                        <div
                                            class="bg-surface border-2 border-ink p-2.5 shadow-brutal-sm flex flex-col items-center justify-center text-center">
                                            <div
                                                class="w-10 h-10 bg-surface-muted border-2 border-ink flex items-center justify-center mb-1.5">
                                                <svg class="w-5 h-5 text-brand" fill="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path
                                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-[9px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                                WAKIL
                                            </span>
                                            <span
                                                class="font-display font-bold text-xs text-brand truncate max-w-full">
                                                {{ explode(' ', $candidate['co_leader'])[0] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-2.5 pt-2 border-t border-ink/20 flex items-center justify-between text-[10px] font-sans font-bold uppercase tracking-wider text-brand">
                                        <span>PORTRAIT RESMI</span>
                                        <span class="text-accent">&#9632; VERIFIED</span>
                                    </div>
                                </div>

                                <div class="p-4 space-y-3">
                                    <div class="space-y-2">
                                        <div class="bg-surface-muted border-2 border-ink p-2 shadow-brutal-sm">
                                            <div
                                                class="text-[9px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                                CALON KETUA BEM
                                            </div>
                                            <div
                                                class="font-display font-bold text-sm text-brand uppercase leading-tight mt-0.5">
                                                {{ $candidate['leader'] }}
                                            </div>
                                        </div>

                                        <div class="bg-surface-muted border-2 border-ink p-2 shadow-brutal-sm">
                                            <div
                                                class="text-[9px] font-sans font-bold uppercase tracking-wider text-ink/70">
                                                CALON WAKIL KETUA BEM
                                            </div>
                                            <div
                                                class="font-display font-bold text-sm text-brand uppercase leading-tight mt-0.5">
                                                {{ $candidate['co_leader'] }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t-2 border-ink pt-2.5">
                                        <div
                                            class="text-[10px] font-sans font-bold uppercase tracking-wider text-brand mb-1">
                                            RINGKASAN VISI
                                        </div>
                                        <p class="text-xs font-sans italic text-ink font-medium leading-relaxed">
                                            &ldquo;{{ $candidate['vision'] }}&rdquo;
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 pt-0">
                                <a href="#paslon"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                                    <span>LIHAT PROFIL</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-3 mt-4">
                <button type="button" @click="prev()" :disabled="currentIndex === 0" aria-label="Paslon sebelumnya"
                    class="w-10 h-10 inline-flex items-center justify-center bg-surface border-2 border-ink shadow-brutal-sm text-ink font-display font-bold text-lg hover:bg-brand hover:text-surface hover:border-ink active:translate-x-0.5 active:translate-y-0.5 active:shadow-none disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-surface disabled:hover:text-ink disabled:hover:border-ink disabled:active:translate-x-0 transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                    &larr;
                </button>

                <div
                    class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-widest text-brand">
                    <span x-text="String(currentIndex + 1).padStart(2, '0')">01</span>
                    <span class="text-ink/40 mx-1">/</span>
                    <span>{{ str_pad(count($candidatePairs), 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <button type="button" @click="next()" :disabled="currentIndex === totalSlides - 1"
                    aria-label="Paslon berikutnya"
                    class="w-10 h-10 inline-flex items-center justify-center bg-surface border-2 border-ink shadow-brutal-sm text-ink font-display font-bold text-lg hover:bg-brand hover:text-surface hover:border-ink active:translate-x-0.5 active:translate-y-0.5 active:shadow-none disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-surface disabled:hover:text-ink disabled:hover:border-ink disabled:active:translate-x-0 transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                    &rarr;
                </button>
            </div>
        </div>
    </div>
</section>
