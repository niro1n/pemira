@php
    $votingSteps = [
        [
            'number' => '01',
            'title' => 'DAFTAR',
            'desc' => 'Verifikasi NIM dan tanggal lahir untuk membuat akun pemilih.',
            'badge' => null,
            'is_core' => false,
        ],
        [
            'number' => '02',
            'title' => 'MASUK',
            'desc' => 'Login menggunakan akun pemilih yang sudah terverifikasi.',
            'badge' => null,
            'is_core' => false,
        ],
        [
            'number' => '03',
            'title' => 'PILIH PASLON',
            'desc' => 'Kenali kandidat dan tentukan satu pasangan pilihanmu.',
            'badge' => 'INTI PROSES',
            'is_core' => true,
        ],
        [
            'number' => '04',
            'title' => 'KONFIRMASI',
            'desc' => 'Pastikan pilihanmu sebelum suara dikirim.',
            'badge' => 'FINAL',
            'is_core' => false,
        ],
        [
            'number' => '05',
            'title' => 'SELESAI',
            'desc' => 'Suaramu tercatat dan tidak dapat diubah.',
            'badge' => 'TERCATAT',
            'is_core' => false,
        ],
    ];
@endphp

<section id="cara-memilih"
    class="relative w-full bg-surface-muted border-b-2 border-ink overflow-hidden py-8 sm:py-12 md:py-16 lg:py-20">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div
            class="hidden md:block absolute -top-10 -right-4 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            04
        </div>

        <svg class="absolute top-10 left-8 w-44 h-44 text-ink/10 hidden sm:block pointer-events-none"
            fill="currentColor">
            <pattern id="cara-memilih-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#cara-memilih-dots)" />
        </svg>

        <div class="hidden lg:block absolute top-0 right-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 left-1/4 w-px h-full bg-ink/10"></div>
        <div class="hidden sm:flex absolute top-12 left-1/3 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div
            class="hidden sm:flex absolute bottom-12 right-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden lg:block absolute top-1/3 left-8 w-3 h-3 bg-accent border border-ink"></div>
        <div class="hidden md:block absolute bottom-1/3 right-10 w-2.5 h-2.5 bg-brand"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8 sm:mb-12 lg:mb-16">
            <div
                class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-3.5 py-1 sm:py-1.5 bg-surface border-2 border-ink shadow-brutal-sm text-xs sm:text-sm font-sans font-bold tracking-wider uppercase text-brand mb-4 sm:mb-6">
                <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block border border-ink"></span>
                <span>CARA MEMILIH</span>
            </div>

            <h2
                class="font-display font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight uppercase text-ink mb-3 sm:mb-4 leading-none space-y-1">
                <span class="block">MUDAH.</span>
                <span class="block text-brand">AMAN.</span>
                <span class="block">SATU KALI.</span>
            </h2>

            <p class="text-sm sm:text-base lg:text-lg text-ink font-sans font-medium leading-snug sm:leading-relaxed">
                Gunakan hak suaramu dengan mengikuti langkah sederhana berikut.
            </p>
        </div>

        <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5 lg:gap-6">
            @foreach ($votingSteps as $step)
                <div
                    class="bg-surface border-2 border-ink p-4 sm:p-5 lg:p-6 shadow-brutal flex flex-col justify-between relative hover:translate-x-0.5 hover:translate-y-0.5 transition-all {{ $step['is_core'] ? 'ring-2 ring-brand ring-offset-2' : '' }}">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4">
                            <span
                                class="font-display font-black text-3xl sm:text-4xl lg:text-5xl text-brand leading-none">
                                {{ $step['number'] }}
                            </span>
                            @if ($step['badge'])
                                <span
                                    class="bg-accent text-ink px-2 py-0.5 text-[9px] sm:text-[10px] font-display font-bold uppercase border border-ink shadow-brutal-sm">
                                    {{ $step['badge'] }}
                                </span>
                            @else
                                <span class="w-2 h-2 bg-ink/20 inline-block"></span>
                            @endif
                        </div>

                        <div class="h-0.5 w-full bg-ink mb-3 sm:mb-4"></div>

                        <h3
                            class="font-display font-bold text-base sm:text-lg text-brand uppercase tracking-tight mb-2">
                            {{ $step['title'] }}
                        </h3>

                        <p class="text-xs sm:text-sm font-sans font-medium text-ink leading-relaxed">
                            {{ $step['desc'] }}
                        </p>
                    </div>

                    <div
                        class="mt-4 pt-3 border-t border-ink/10 flex items-center justify-between text-[10px] font-sans font-bold uppercase tracking-wider text-ink/40">
                        <span>TAHAP {{ $step['number'] }}</span>
                        @if ($step['number'] === '05')
                            <span class="w-2 h-2 bg-accent border border-ink inline-block"></span>
                        @else
                            <span>&rarr;</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="block md:hidden overflow-hidden" x-data="{
            currentIndex: 0,
            totalSlides: {{ count($votingSteps) }},
            timer: null,
            touchStartX: 0,
            touchEndX: 0,
            init() {
                this.startAutoSlide();
            },
            goTo(index) {
                if (index < 0) {
                    this.currentIndex = this.totalSlides - 1;
                } else if (index >= this.totalSlides) {
                    this.currentIndex = 0;
                } else {
                    this.currentIndex = index;
                }
                this.resetAutoSlide();
            },
            prev() {
                this.goTo(this.currentIndex - 1);
            },
            next() {
                this.goTo(this.currentIndex + 1);
            },
            startAutoSlide() {
                this.stopAutoSlide();
                if (window.innerWidth < 768) {
                    this.timer = setInterval(() => {
                        this.goTo(this.currentIndex + 1);
                    }, 5000);
                }
            },
            stopAutoSlide() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },
            resetAutoSlide() {
                this.startAutoSlide();
            },
            handleTouchStart(e) {
                this.stopAutoSlide();
                this.touchStartX = e.changedTouches[0].screenX;
            },
            handleTouchEnd(e) {
                this.touchEndX = e.changedTouches[0].screenX;
                const diffX = this.touchStartX - this.touchEndX;
                if (Math.abs(diffX) > 40) {
                    if (diffX > 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                } else {
                    this.startAutoSlide();
                }
            }
        }" @mouseenter="stopAutoSlide()"
            @mouseleave="startAutoSlide()" @touchstart="handleTouchStart($event)" @touchend="handleTouchEnd($event)"
            @resize.window="if (window.innerWidth >= 768) stopAutoSlide(); else if (!timer) startAutoSlide();">
            <div class="flex transition-transform duration-300 ease-out"
                :style="`transform: translateX(-${currentIndex * 100}%)`">
                @foreach ($votingSteps as $index => $step)
                    <div class="w-full shrink-0 px-0.5">
                        <div
                            class="bg-surface border-2 border-ink p-5 shadow-brutal-lg flex flex-col justify-between relative min-h-[210px] {{ $step['is_core'] ? 'ring-2 ring-brand ring-offset-2' : '' }}">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="font-display font-black text-4xl sm:text-5xl text-brand leading-none">
                                        {{ $step['number'] }}
                                    </span>
                                    @if ($step['badge'])
                                        <span
                                            class="bg-accent text-ink px-2.5 py-0.5 text-[10px] font-display font-bold uppercase border border-ink shadow-brutal-sm">
                                            {{ $step['badge'] }}
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-sans font-bold uppercase tracking-wider text-ink/40">
                                            LANGKAH {{ $step['number'] }} / 05
                                        </span>
                                    @endif
                                </div>

                                <div class="h-0.5 w-full bg-ink mb-3"></div>

                                <h3
                                    class="font-display font-extrabold text-lg text-brand uppercase tracking-tight mb-2">
                                    {{ $step['title'] }}
                                </h3>

                                <p class="text-xs sm:text-sm font-sans font-medium text-ink leading-relaxed">
                                    {{ $step['desc'] }}
                                </p>
                            </div>

                            <div
                                class="mt-4 pt-3 border-t border-ink/10 flex items-center justify-between text-[10px] font-sans font-bold uppercase tracking-wider text-ink/40">
                                <span>PROSES PEMILIHAN</span>
                                @if ($step['number'] === '05')
                                    <span class="w-2.5 h-2.5 bg-accent border border-ink inline-block"></span>
                                @else
                                    <span>&rarr;</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col items-center gap-2.5 mt-4">
                <div class="flex items-center justify-center gap-3">
                    <button type="button" @click="prev()" aria-label="Langkah sebelumnya"
                        class="w-10 h-10 inline-flex items-center justify-center bg-surface border-2 border-ink shadow-brutal-sm text-ink font-display font-bold text-lg hover:bg-brand hover:text-surface hover:border-ink active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                        &larr;
                    </button>

                    <div
                        class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-widest text-brand">
                        <span x-text="String(currentIndex + 1).padStart(2, '0')">01</span>
                        <span class="text-ink/40 mx-1">/</span>
                        <span>{{ str_pad(count($votingSteps), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <button type="button" @click="next()" aria-label="Langkah berikutnya"
                        class="w-10 h-10 inline-flex items-center justify-center bg-surface border-2 border-ink shadow-brutal-sm text-ink font-display font-bold text-lg hover:bg-brand hover:text-surface hover:border-ink active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                        &rarr;
                    </button>
                </div>

                <div class="flex items-center gap-1.5" aria-hidden="true">
                    @for ($i = 0; $i < count($votingSteps); $i++)
                        <button type="button" @click="goTo({{ $i }})"
                            aria-label="Langkah {{ $i + 1 }}" class="inline-block transition-all duration-300"
                            :class="currentIndex === {{ $i }} ? 'w-5 h-2 bg-brand border border-ink' :
                                'w-2 h-2 bg-surface border border-ink/40'"></button>
                    @endfor
                </div>
            </div>
        </div>

        <div class="mt-8 sm:mt-12 bg-surface border-2 border-ink p-4 sm:p-6 lg:p-7 shadow-brutal-lg">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6 items-center">
                <div class="md:col-span-8 flex items-start gap-3 sm:gap-4">
                    <div
                        class="w-9 h-9 sm:w-11 sm:h-11 bg-accent border-2 border-ink shadow-brutal-sm flex items-center justify-center shrink-0">
                        <span class="font-display font-black text-base sm:text-lg text-ink">&#9632;</span>
                    </div>
                    <div>
                        <div
                            class="font-display font-bold text-sm sm:text-base text-brand uppercase tracking-wide mb-1 flex flex-wrap items-center gap-2">
                            <span>PILIH DENGAN TENANG</span>
                            <span
                                class="bg-surface-muted text-ink px-2 py-0.5 text-[9px] font-display font-bold uppercase border border-ink">
                                RAHASIA & AMAN
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-sans font-medium text-ink leading-relaxed">
                            Setelah suara dikonfirmasi, pilihanmu tidak dapat diubah. Sistem juga tidak menampilkan
                            hubungan antara identitas pemilih dan pilihan paslon pada hasil pemilihan.
                        </p>
                    </div>
                </div>

                <div
                    class="md:col-span-4 flex flex-col sm:flex-row md:flex-col items-start md:items-end justify-center gap-2 border-t-2 md:border-t-0 md:border-l-2 border-ink pt-4 md:pt-0 md:pl-6">
                    <div class="text-[10px] sm:text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                        SUDAH SIAP MEMILIH?
                    </div>
                    <a href="{{ Route::has('login') ? route('login') : '#' }}"
                        class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                        <span>MASUK SEKARANG</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
