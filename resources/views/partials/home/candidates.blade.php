@props([
    'election' => null,
])

@php
    $candidatePairs = $election?->candidatePairs ?? collect();
@endphp

<section id="paslon"
    class="relative w-full bg-surface border-b-2 border-ink overflow-hidden py-10 sm:py-16 md:py-20 lg:py-24">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div
            class="hidden md:block absolute -top-10 -left-4 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            {{ $candidatePairs->count() > 0 ? str_pad((string) $candidatePairs->count(), 2, '0', STR_PAD_LEFT) : '00' }}
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
                Kenali pasangan calon, gagasan, visi, dan misi sebelum menentukan pilihanmu pada {{ $election ? $election->name : 'PEMIRA' }}.
            </p>
        </div>

        @if ($candidatePairs->isEmpty())
            <div class="bg-surface border-2 border-ink shadow-brutal p-8 sm:p-12 text-center max-w-xl mx-auto space-y-3">
                <div class="w-14 h-14 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                    <svg class="w-7 h-7 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-display font-black text-base sm:text-lg text-brand uppercase">
                    BELUM ADA PASLON TERDAFTAR
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70">
                    Daftar pasangan calon untuk pemilihan ini belum ditetapkan oleh panitia KPR.
                </p>
            </div>
        @else
            <div class="hidden md:flex md:flex-wrap md:justify-center md:items-stretch gap-6 lg:gap-8 mx-auto w-full">
                @foreach ($candidatePairs as $candidate)
                    @php
                        $leader = $candidate->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter;
                        $viceLeader = $candidate->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter;
                        $leaderName = $leader?->name ?? 'Kandidat Ketua';
                        $viceLeaderName = $viceLeader?->name ?? 'Kandidat Wakil';
                        $number = $candidate->formattedNumber();
                    @endphp
                    <div
                        class="w-full max-w-sm md:w-80 lg:w-88 xl:w-90 shrink-0 bg-surface border-2 border-ink shadow-brutal-lg flex flex-col justify-between relative group hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                        <div>
                            <div
                                class="bg-brand text-surface px-4 py-3 border-b-2 border-ink flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                                    <span class="font-display font-extrabold text-sm uppercase tracking-wider">
                                        PASLON {{ $number }}
                                    </span>
                                </div>
                                <span
                                    class="bg-accent text-ink px-2.5 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                    KANDIDAT BEM
                                </span>
                            </div>

                            <div class="relative bg-surface-muted border-b-2 border-ink p-4 sm:p-5 overflow-hidden">
                                <div
                                    class="absolute -top-3 -right-2 font-display font-black text-6xl sm:text-7xl text-ink/10 select-none pointer-events-none leading-none">
                                    {{ $number }}
                                </div>

                                @if ($candidate->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($candidate->photo))
                                    <div class="border-2 border-ink shadow-brutal-sm aspect-4/5 max-w-60 mx-auto bg-surface overflow-hidden relative z-10">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($candidate->photo) }}"
                                             alt="Foto Paslon {{ $number }}"
                                             class="w-full h-full object-cover object-top" />
                                    </div>
                                @else
                                    <div class="border-2 border-ink shadow-brutal-sm aspect-4/5 max-w-60 mx-auto bg-surface overflow-hidden relative z-10 flex flex-col items-center justify-center p-4 text-center">
                                        <div class="w-12 h-12 bg-surface-muted border-2 border-ink shadow-brutal-sm flex items-center justify-center mb-1">
                                            <svg class="w-6 h-6 text-ink/40" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                        <span class="font-display font-bold text-xs uppercase text-ink/40">FOTO BELUM DIUNGGAH</span>
                                    </div>
                                @endif

                                <div
                                    class="mt-3 sm:mt-3.5 pt-2 border-t border-ink/20 flex items-center justify-between text-xs font-sans font-bold uppercase tracking-wider text-brand">
                                    <span>PORTRAIT RESMI</span>
                                    <span class="text-accent font-display font-bold">&#9632; VERIFIED</span>
                                </div>
                            </div>

                            <div class="p-4 sm:p-5 space-y-2.5">
                                <div class="bg-surface-muted border-2 border-ink p-2.5 shadow-brutal-sm">
                                    <div
                                        class="text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                                        CALON KETUA BEM
                                    </div>
                                    <div
                                        class="font-display font-bold text-sm sm:text-base text-brand uppercase leading-tight mt-0.5 truncate" title="{{ $leaderName }}">
                                        {{ $leaderName }}
                                    </div>
                                    <div class="text-xs font-sans text-ink/60 truncate mt-0.5" title="{{ $leader?->studyProgram?->name ?? '' }}">
                                        {{ $leader?->studyProgram?->name ?? '-' }}
                                    </div>
                                </div>

                                <div class="bg-surface-muted border-2 border-ink p-2.5 shadow-brutal-sm">
                                    <div
                                        class="text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                                        CALON WAKIL KETUA BEM
                                    </div>
                                    <div
                                        class="font-display font-bold text-sm sm:text-base text-brand uppercase leading-tight mt-0.5 truncate" title="{{ $viceLeaderName }}">
                                        {{ $viceLeaderName }}
                                    </div>
                                    <div class="text-xs font-sans text-ink/60 truncate mt-0.5" title="{{ $viceLeader?->studyProgram?->name ?? '' }}">
                                        {{ $viceLeader?->studyProgram?->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 pt-0">
                            <a href="{{ route('public.candidates.show', $candidate) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                                <span>DETAIL & VISI-MISI PASLON {{ $number }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($candidatePairs->count() === 1)
                @php
                    $candidate = $candidatePairs->first();
                    $leader = $candidate->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter;
                    $viceLeader = $candidate->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter;
                    $leaderName = $leader?->name ?? 'Kandidat Ketua';
                    $viceLeaderName = $viceLeader?->name ?? 'Kandidat Wakil';
                    $number = $candidate->formattedNumber();
                @endphp
                <div class="block md:hidden max-w-sm mx-auto px-1">
                    <div class="bg-surface border-2 border-ink shadow-brutal-lg flex flex-col justify-between relative">
                        <div>
                            <div class="bg-brand text-surface px-4 py-2.5 border-b-2 border-ink flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                                    <span class="font-display font-extrabold text-sm uppercase tracking-wider">
                                        PASLON {{ $number }}
                                    </span>
                                </div>
                                <span class="bg-accent text-ink px-2 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                    KANDIDAT BEM
                                </span>
                            </div>

                            <div class="relative bg-surface-muted border-b-2 border-ink p-4 overflow-hidden">
                                <div class="absolute -top-3 -right-2 font-display font-black text-6xl text-ink/10 select-none pointer-events-none leading-none">
                                    {{ $number }}
                                </div>

                                @if ($candidate->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($candidate->photo))
                                    <div class="border-2 border-ink shadow-brutal-sm aspect-4/5 max-w-55 mx-auto bg-surface overflow-hidden relative z-10">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($candidate->photo) }}"
                                             alt="Foto Paslon {{ $number }}"
                                             class="w-full h-full object-cover object-top" />
                                    </div>
                                @else
                                    <div class="border-2 border-ink shadow-brutal-sm aspect-4/5 max-w-55 mx-auto bg-surface overflow-hidden relative z-10 flex flex-col items-center justify-center p-3 text-center">
                                        <div class="w-10 h-10 bg-surface-muted border border-ink flex items-center justify-center mb-1">
                                            <svg class="w-5 h-5 text-ink/40" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                        <span class="font-display font-bold text-xs uppercase text-ink/40">FOTO BELUM DIUNGGAH</span>
                                    </div>
                                @endif

                                <div class="mt-3 pt-2 border-t border-ink/20 flex items-center justify-between text-xs font-sans font-bold uppercase tracking-wider text-brand">
                                    <span>PORTRAIT RESMI</span>
                                    <span class="text-accent font-display font-bold">&#9632; VERIFIED</span>
                                </div>
                            </div>

                            <div class="p-4 space-y-2">
                                <div class="bg-surface-muted border-2 border-ink p-2 shadow-brutal-sm">
                                    <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                                        CALON KETUA BEM
                                    </div>
                                    <div class="font-display font-bold text-sm text-brand uppercase leading-tight mt-0.5 truncate" title="{{ $leaderName }}">
                                        {{ $leaderName }}
                                    </div>
                                    <div class="text-xs font-sans text-ink/60 truncate mt-0.5" title="{{ $leader?->studyProgram?->name ?? '' }}">
                                        {{ $leader?->studyProgram?->name ?? '-' }}
                                    </div>
                                </div>

                                <div class="bg-surface-muted border-2 border-ink p-2 shadow-brutal-sm">
                                    <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                                        CALON WAKIL KETUA BEM
                                    </div>
                                    <div class="font-display font-bold text-sm text-brand uppercase leading-tight mt-0.5 truncate" title="{{ $viceLeaderName }}">
                                        {{ $viceLeaderName }}
                                    </div>
                                    <div class="text-xs font-sans text-ink/60 truncate mt-0.5" title="{{ $viceLeader?->studyProgram?->name ?? '' }}">
                                        {{ $viceLeader?->studyProgram?->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 pt-0">
                            <a href="{{ route('public.candidates.show', $candidate) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                                <span>DETAIL & VISI-MISI PASLON {{ $number }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="block md:hidden overflow-hidden max-w-sm mx-auto"
                    x-data="{
                        currentIndex: 0,
                        totalSlides: {{ $candidatePairs->count() }},
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
                            if (window.innerWidth < 768 && this.totalSlides > 1) {
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
                    }"
                    @mouseenter="stopAutoSlide()"
                    @mouseleave="startAutoSlide()"
                    @touchstart="handleTouchStart($event)"
                    @touchend="handleTouchEnd($event)"
                    @resize.window="if (window.innerWidth >= 768) stopAutoSlide(); else if (!timer) startAutoSlide();">
                    
                    <div class="flex transition-transform duration-300 ease-out"
                        :style="`transform: translateX(-${currentIndex * 100}%)`">
                        @foreach ($candidatePairs as $candidate)
                            @php
                                $leader = $candidate->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter;
                                $viceLeader = $candidate->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter;
                                $leaderName = $leader?->name ?? 'Kandidat Ketua';
                                $viceLeaderName = $viceLeader?->name ?? 'Kandidat Wakil';
                                $number = $candidate->formattedNumber();
                            @endphp
                            <div class="w-full shrink-0 px-1">
                                <div class="bg-surface border-2 border-ink shadow-brutal-lg flex flex-col justify-between relative h-full">
                                    <div>
                                        <div class="bg-brand text-surface px-4 py-2.5 border-b-2 border-ink flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                                                <span class="font-display font-extrabold text-sm uppercase tracking-wider">
                                                    PASLON {{ $number }}
                                                </span>
                                            </div>
                                            <span class="bg-accent text-ink px-2 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                                KANDIDAT BEM
                                            </span>
                                        </div>

                                        <div class="relative bg-surface-muted border-b-2 border-ink p-4 overflow-hidden">
                                            <div class="absolute -top-3 -right-2 font-display font-black text-6xl text-ink/10 select-none pointer-events-none leading-none">
                                                {{ $number }}
                                            </div>

                                            @if ($candidate->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($candidate->photo))
                                                <div class="border-2 border-ink shadow-brutal-sm aspect-4/5 max-w-55 mx-auto bg-surface overflow-hidden relative z-10">
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($candidate->photo) }}"
                                                         alt="Foto Paslon {{ $number }}"
                                                         class="w-full h-full object-cover object-top" />
                                                </div>
                                            @else
                                                <div class="border-2 border-ink shadow-brutal-sm aspect-4/5 max-w-55 mx-auto bg-surface overflow-hidden relative z-10 flex flex-col items-center justify-center p-3 text-center">
                                                    <div class="w-10 h-10 bg-surface-muted border border-ink flex items-center justify-center mb-1">
                                                        <svg class="w-5 h-5 text-ink/40" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                        </svg>
                                                    </div>
                                                    <span class="font-display font-bold text-xs uppercase text-ink/40">FOTO BELUM DIUNGGAH</span>
                                                </div>
                                            @endif

                                            <div class="mt-3 pt-2 border-t border-ink/20 flex items-center justify-between text-xs font-sans font-bold uppercase tracking-wider text-brand">
                                                <span>PORTRAIT RESMI</span>
                                                <span class="text-accent font-display font-bold">&#9632; VERIFIED</span>
                                            </div>
                                        </div>

                                        <div class="p-4 space-y-2">
                                            <div class="bg-surface-muted border-2 border-ink p-2 shadow-brutal-sm">
                                                <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                                                    CALON KETUA BEM
                                                </div>
                                                <div class="font-display font-bold text-sm text-brand uppercase leading-tight mt-0.5 truncate" title="{{ $leaderName }}">
                                                    {{ $leaderName }}
                                                </div>
                                                <div class="text-xs font-sans text-ink/60 truncate mt-0.5" title="{{ $leader?->studyProgram?->name ?? '' }}">
                                                    {{ $leader?->studyProgram?->name ?? '-' }}
                                                </div>
                                            </div>

                                            <div class="bg-surface-muted border-2 border-ink p-2 shadow-brutal-sm">
                                                <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/70">
                                                    CALON WAKIL KETUA BEM
                                                </div>
                                                <div class="font-display font-bold text-sm text-brand uppercase leading-tight mt-0.5 truncate" title="{{ $viceLeaderName }}">
                                                    {{ $viceLeaderName }}
                                                </div>
                                                <div class="text-xs font-sans text-ink/60 truncate mt-0.5" title="{{ $viceLeader?->studyProgram?->name ?? '' }}">
                                                    {{ $viceLeader?->studyProgram?->name ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 pt-0">
                                        <a href="{{ route('public.candidates.show', $candidate) }}"
                                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand">
                                            <span>DETAIL & VISI-MISI PASLON {{ $number }}</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col items-center gap-2.5 mt-4">
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" @click="prev()" aria-label="Paslon sebelumnya"
                                    class="w-10 h-10 inline-flex items-center justify-center bg-surface border-2 border-ink shadow-brutal-sm text-ink font-display font-bold text-lg hover:bg-brand hover:text-surface hover:border-ink active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand cursor-pointer">
                                &larr;
                            </button>

                            <div class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-widest text-brand">
                                <span x-text="String(currentIndex + 1).padStart(2, '0')">01</span>
                                <span class="text-ink/40 mx-1">/</span>
                                <span>{{ str_pad((string) $candidatePairs->count(), 2, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <button type="button" @click="next()" aria-label="Paslon berikutnya"
                                    class="w-10 h-10 inline-flex items-center justify-center bg-surface border-2 border-ink shadow-brutal-sm text-ink font-display font-bold text-lg hover:bg-brand hover:text-surface hover:border-ink active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand cursor-pointer">
                                &rarr;
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5" role="tablist" aria-label="Indikator slide paslon">
                            @foreach ($candidatePairs as $i => $pair)
                                <button type="button"
                                        @click="goTo({{ $i }})"
                                        aria-label="Lihat Paslon {{ $pair->formattedNumber() }}"
                                        class="inline-block transition-all duration-300 cursor-pointer"
                                        :class="currentIndex === {{ $i }} ? 'w-5 h-2 bg-brand border border-ink' : 'w-2 h-2 bg-surface border border-ink/40'">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</section>
