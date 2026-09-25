@props([
    'sponsors' => collect(),
])

@php
    $humasWaNumber = env('HUMAS_WHATSAPP', config('pemira.contacts.humas.whatsapp_number', '6281337534761'));
    $ketuaWaNumber = env('KETUA_PANITIA_WHATSAPP', config('pemira.contacts.ketua_panitia.whatsapp_number', '628970898383'));

    $humasWhatsappUrl = "https://wa.me/{$humasWaNumber}?text=".rawurlencode('Halo kak Sintya (Humas PEMIRA), kami tertarik untuk menjalin kerja sama sponsorship PEMIRA.');
    $ketuaWhatsappUrl = "https://wa.me/{$ketuaWaNumber}?text=".rawurlencode('Halo kak Diana (Ketua Panitia PEMIRA), kami tertarik untuk berdiskusi terkait sponsorship PEMIRA.');
    $whatsappUrl = $humasWhatsappUrl;
@endphp

<section id="sponsors"
         class="relative w-full bg-surface border-b-2 border-ink overflow-hidden py-10 sm:py-16 md:py-20">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <svg class="absolute top-10 right-8 w-36 h-36 text-ink/10 hidden sm:block pointer-events-none"
             fill="currentColor">
            <pattern id="sponsor-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#sponsor-dots)" />
        </svg>
        <div class="hidden sm:flex absolute bottom-8 left-12 text-ink/20 font-display font-bold text-xl leading-none">
            +
        </div>
        <div class="hidden lg:block absolute top-12 left-1/4 w-2.5 h-2.5 bg-accent border border-ink"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($sponsors->isNotEmpty())
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs sm:text-sm font-sans font-bold tracking-wider uppercase text-brand mb-3 sm:mb-4">
                    <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                    <span>MITRA & SPONSOR</span>
                </div>

                <h2 class="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl text-ink uppercase tracking-tight leading-tight">
                    DIDUKUNG OLEH MITRA TERPERCAYA
                </h2>

                <p class="text-xs sm:text-sm md:text-base text-ink/70 font-sans font-medium mt-2 max-w-xl mx-auto">
                    Apresiasi kepada organisasi dan mitra yang turut mendukung kelancaran pelaksanaan PEMIRA Politeknik Negeri Bali.
                </p>
            </div>

            <div x-data="{
                active: 0,
                perPage: 5,
                total: {{ $sponsors->count() }},
                timer: null,
                touchStartX: 0,
                touchEndX: 0,
                updatePerPage() {
                    if (window.innerWidth < 640) {
                        this.perPage = 2;
                    } else if (window.innerWidth < 768) {
                        this.perPage = 3;
                    } else if (window.innerWidth < 1024) {
                        this.perPage = 4;
                    } else {
                        this.perPage = 5;
                    }
                },
                maxIndex() {
                    return Math.max(0, this.total - this.perPage);
                },
                next() {
                    if (this.maxIndex() === 0) return;
                    this.active = this.active >= this.maxIndex() ? 0 : this.active + 1;
                },
                prev() {
                    if (this.maxIndex() === 0) return;
                    this.active = this.active <= 0 ? this.maxIndex() : this.active - 1;
                },
                goTo(index) {
                    this.active = Math.min(Math.max(0, index), this.maxIndex());
                },
                startAutoplay() {
                    this.stopAutoplay();
                    if (this.total > this.perPage) {
                        this.timer = setInterval(() => {
                            this.next();
                        }, 2800);
                    }
                },
                stopAutoplay() {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                },
                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    if (this.touchStartX - this.touchEndX > 50) {
                        this.next();
                    } else if (this.touchEndX - this.touchStartX > 50) {
                        this.prev();
                    }
                },
                init() {
                    this.updatePerPage();
                    window.addEventListener('resize', () => {
                        this.updatePerPage();
                        if (this.active > this.maxIndex()) {
                            this.active = this.maxIndex();
                        }
                    });
                    this.startAutoplay();
                }
            }"
            @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()"
            @touchstart.passive="handleTouchStart($event)"
            @touchend.passive="handleTouchEnd($event)"
            class="relative w-full">

                <div class="overflow-hidden py-4 -mx-2 sm:-mx-3">
                    <div class="flex items-center transition-transform duration-500 ease-out {{ $sponsors->count() < 5 ? 'lg:justify-center' : '' }}"
                         :style="'transform: translateX(-' + (active * (100 / perPage)) + '%);'">
                        @foreach ($sponsors as $sponsor)
                            @php
                                $hasWebsite = ! empty($sponsor->website_url);
                                $logoUrl = $sponsor->logoUrl();
                            @endphp

                            <div class="w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/5 shrink-0 px-3 sm:px-4 md:px-6 flex items-center justify-center">
                                @if ($hasWebsite)
                                    <a href="{{ $sponsor->website_url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="{{ $sponsor->name }}"
                                       class="group flex items-center justify-center w-full h-16 sm:h-20 md:h-24 transition-transform duration-200 hover:scale-105">
                                        <img src="{{ $logoUrl }}"
                                             alt="{{ $sponsor->name }}"
                                             loading="lazy"
                                             class="max-h-12 sm:max-h-16 md:max-h-20 w-auto max-w-full object-contain filter group-hover:drop-shadow-xs transition-all" />
                                    </a>
                                @else
                                    <div title="{{ $sponsor->name }}"
                                         class="flex items-center justify-center w-full h-16 sm:h-20 md:h-24">
                                        <img src="{{ $logoUrl }}"
                                             alt="{{ $sponsor->name }}"
                                             loading="lazy"
                                             class="max-h-12 sm:max-h-16 md:max-h-20 w-auto max-w-full object-contain" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="total > perPage" class="flex items-center justify-center gap-3 mt-4 sm:mt-6">
                    <button @click="prev()"
                            type="button"
                            class="w-8 h-8 sm:w-9 sm:h-9 bg-surface border-2 border-ink shadow-brutal-sm hover:bg-accent flex items-center justify-center text-ink transition-all cursor-pointer active:translate-x-0.5 active:translate-y-0.5"
                            aria-label="Sponsor sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5 px-2">
                        <template x-for="i in (maxIndex() + 1)" :key="i">
                            <button @click="goTo(i - 1)"
                                    type="button"
                                    class="h-2 transition-all border border-ink cursor-pointer"
                                    :class="active === (i - 1) ? 'w-6 bg-brand' : 'w-2 bg-ink/20 hover:bg-ink/40'"
                                    :aria-label="'Ke slide ' + i"></button>
                        </template>
                    </div>

                    <button @click="next()"
                            type="button"
                            class="w-8 h-8 sm:w-9 sm:h-9 bg-surface border-2 border-ink shadow-brutal-sm hover:bg-accent flex items-center justify-center text-ink transition-all cursor-pointer active:translate-x-0.5 active:translate-y-0.5"
                            aria-label="Sponsor berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-10 sm:mt-12 bg-surface-muted border-2 border-ink shadow-brutal p-4 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <span class="font-display font-bold text-xs sm:text-sm uppercase tracking-wide text-brand block">
                        Tertarik Menjadi Mitra Atau Sponsor PEMIRA?
                    </span>
                    <p class="text-xs font-sans text-ink/70 mt-0.5">
                        Dukung pesta demokrasi kampus dan bangun sinergi strategis bersama mahasiswa PNB.
                    </p>
                </div>
                <a href="{{ $whatsappUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider shrink-0 transition-all">
                    <span>HUBUNGI HUMAS</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        @else
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs sm:text-sm font-sans font-bold tracking-wider uppercase text-brand mb-3 sm:mb-4">
                    <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                    <span>PELUANG KOLABORASI</span>
                </div>

                <h2 class="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl text-ink uppercase tracking-tight leading-tight">
                    TERBUKA UNTUK KERJA SAMA & SPONSORSHIP
                </h2>

                <p class="text-xs sm:text-sm md:text-base text-ink/70 font-sans font-medium mt-2 max-w-xl mx-auto">
                    Mari bersinergi menyukseskan pesta demokrasi mahasiswa Politeknik Negeri Bali dan perluas eksposur institusi Anda bersama kami.
                </p>
            </div>

            <div class="bg-brand text-surface border-2 border-ink shadow-brutal-lg p-6 sm:p-8 md:p-10 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-10 font-display font-black text-8xl lg:text-9xl text-surface/5 select-none pointer-events-none leading-none"
                     aria-hidden="true">
                    PARTNER
                </div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center">
                    <div class="lg:col-span-8 space-y-4">
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-brand-dark border border-surface/20 text-xs font-sans font-bold uppercase tracking-wider text-accent">
                            <span class="w-1.5 h-1.5 bg-accent inline-block border border-ink"></span>
                            <span>AJAKAN KERJA SAMA</span>
                        </div>

                        <h3 class="font-display font-black text-xl sm:text-2xl md:text-3xl uppercase tracking-tight text-surface leading-tight">
                            JADILAH BAGIAN DARI PERUBAHAN POSITIF KAMPUS
                        </h3>

                        <p class="text-xs sm:text-sm md:text-base font-sans text-surface/85 leading-relaxed max-w-2xl">
                            PEMIRA PNB menjangkau ribuan mahasiswa aktif dari berbagai jurusan. Kami membuka kesempatan kerja sama dan sponsorship bagi perusahaan, instansi, maupun unit usaha yang ingin berkontribusi dalam mendukung lahirnya calon-calon pemimpin masa depan.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="bg-brand-dark/60 border border-surface/15 p-3">
                                <span class="font-display font-bold text-xs uppercase text-accent block">Eksposur Kampus</span>
                                <span class="text-[11px] text-surface/75 mt-0.5 block">Jangkau audiens ribuan mahasiswa aktif PNB.</span>
                            </div>
                            <div class="bg-brand-dark/60 border border-surface/15 p-3">
                                <span class="font-display font-bold text-xs uppercase text-accent block">Branding Digital</span>
                                <span class="text-[11px] text-surface/75 mt-0.5 block">Tampil di portal resmi dan kanal publikasi acara.</span>
                            </div>
                            <div class="bg-brand-dark/60 border border-surface/15 p-3">
                                <span class="font-display font-bold text-xs uppercase text-accent block">Dukungan Nyata</span>
                                <span class="text-[11px] text-surface/75 mt-0.5 block">Bantu kelancaran demokrasi kampus berintegritas.</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex flex-col items-center lg:items-end justify-center pt-2 lg:pt-0">
                        <div class="w-full bg-surface text-ink border-2 border-ink p-4 sm:p-5 shadow-brutal text-center space-y-3">
                            <span class="font-display font-black text-xs sm:text-sm uppercase text-brand block">
                                HUBUNGI TIM HUMAS
                            </span>
                            <p class="text-xs font-sans text-ink/70">
                                Tertarik menjalin kerja sama atau ingin mendiskusikan proposal sponsorship? Hubungi panitia melalui kontak di bawah.
                            </p>
                            <div class="text-[11px] font-sans text-ink/80 text-left bg-surface-muted p-2.5 border border-ink/20 space-y-1">
                                <div>&bull; Humas: <strong>Sintya</strong></div>
                                <div>&bull; Ketua Panitia: <strong>Diana</strong></div>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ $whatsappUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent text-ink hover:bg-accent-light border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                                    <span>HUBUNGI VIA WHATSAPP</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                                <a href="{{ $ketuaWhatsappUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink text-xs font-display font-bold uppercase tracking-wider transition-all">
                                    <span>HUBUNGI KETUA PANITIA (DIANA)</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
