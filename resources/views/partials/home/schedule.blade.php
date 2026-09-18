@props([
    'election' => null,
])

@php
    $phase = $election?->currentPhase();
    $targetTimestampMs = $election?->targetTimestampMs() ?? 0;
    $isFinished = $phase === \App\Enums\ElectionPhase::FINISHED;

    $phaseTitle = match ($phase) {
        \App\Enums\ElectionPhase::UPCOMING => 'TAHAP PERSIAPAN',
        \App\Enums\ElectionPhase::REGISTRATION => 'PENDAFTARAN PEMILIH',
        \App\Enums\ElectionPhase::VOTING => 'PEMUNGUTAN SUARA',
        \App\Enums\ElectionPhase::FINISHED => 'PEMILIHAN SELESAI',
        default => 'PEMILIHAN BELUM DIBUKA',
    };

    $statusBadge = match ($phase) {
        \App\Enums\ElectionPhase::UPCOMING => 'PEMILIHAN AKAN DATANG',
        \App\Enums\ElectionPhase::REGISTRATION => 'PENDAFTARAN SEDANG BERLANGSUNG',
        \App\Enums\ElectionPhase::VOTING => 'VOTING SEDANG BERLANGSUNG',
        \App\Enums\ElectionPhase::FINISHED => 'PEMILIHAN TELAH SELESAI',
        default => 'BELUM TERSEDIA',
    };

    $countdownLabel = match ($phase) {
        \App\Enums\ElectionPhase::UPCOMING => 'PENDAFTARAN DIMULAI DALAM',
        \App\Enums\ElectionPhase::REGISTRATION => 'PEMUNGUTAN SUARA DIMULAI DALAM',
        \App\Enums\ElectionPhase::VOTING => 'PEMUNGUTAN SUARA BERAKHIR DALAM',
        \App\Enums\ElectionPhase::FINISHED => 'PEMILIHAN TELAH SELESAI',
        default => 'WAKTU PEMILIHAN',
    };

    $activeDateRange = match ($phase) {
        \App\Enums\ElectionPhase::UPCOMING => 'Dimulai '.$election?->registration_start_at?->translatedFormat('d F Y · H:i').' WITA',
        \App\Enums\ElectionPhase::REGISTRATION => $election?->registration_start_at?->translatedFormat('d F Y').' — '.$election?->registration_end_at?->translatedFormat('d F Y'),
        \App\Enums\ElectionPhase::VOTING => $election?->voting_start_at?->translatedFormat('d F Y').' — '.$election?->voting_end_at?->translatedFormat('d F Y'),
        \App\Enums\ElectionPhase::FINISHED => 'Selesai '.$election?->voting_end_at?->translatedFormat('d F Y · H:i').' WITA',
        default => '-',
    };

    $regStartFormatted = $election?->registration_start_at?->translatedFormat('d F Y');
    $regEndFormatted = $election?->registration_end_at?->translatedFormat('d F Y');
    $votingStartFormatted = $election?->voting_start_at?->translatedFormat('d F Y');
    $votingEndFormatted = $election?->voting_end_at?->translatedFormat('d F Y');

    $step1Status = match ($phase) {
        \App\Enums\ElectionPhase::UPCOMING => 'MENDATANG',
        \App\Enums\ElectionPhase::REGISTRATION => 'TAHAP AKTIF',
        \App\Enums\ElectionPhase::VOTING, \App\Enums\ElectionPhase::FINISHED => 'SELESAI',
        default => '-',
    };

    $step2Status = match ($phase) {
        \App\Enums\ElectionPhase::UPCOMING, \App\Enums\ElectionPhase::REGISTRATION => 'MENDATANG',
        \App\Enums\ElectionPhase::VOTING => 'TAHAP AKTIF',
        \App\Enums\ElectionPhase::FINISHED => 'SELESAI',
        default => '-',
    };

    $step3Status = match ($phase) {
        \App\Enums\ElectionPhase::FINISHED => 'TAHAP AKTIF',
        default => 'MENDATANG',
    };
@endphp

<section id="jadwal"
    class="relative w-full bg-surface-muted border-b-2 border-ink overflow-hidden py-10 sm:py-16 md:py-20 lg:py-24">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div
            class="hidden md:block absolute -top-10 -right-4 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            02
        </div>

        <svg class="absolute top-10 left-8 w-40 h-40 text-ink/10 hidden sm:block pointer-events-none"
            fill="currentColor">
            <pattern id="jadwal-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#jadwal-dots)" />
        </svg>

        <div class="hidden lg:block absolute top-0 right-1/4 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 left-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden sm:flex absolute top-12 right-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div
            class="hidden sm:flex absolute bottom-12 left-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <div
                class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-3.5 py-1 sm:py-1.5 bg-surface border-2 border-ink shadow-brutal-sm text-xs sm:text-sm font-sans font-bold tracking-wider uppercase text-brand mb-4 sm:mb-6">
                <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block border border-ink"></span>
                <span>JADWAL {{ $election ? strtoupper($election->name) : 'PEMILIHAN' }}</span>
            </div>

            <h2
                class="font-display font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight uppercase text-ink mb-3 sm:mb-4 leading-none">
                <span class="block">TAHU KAPAN</span>
                <span class="block text-brand">SAATNYA MEMILIH.</span>
            </h2>

            <p class="text-sm sm:text-base lg:text-lg text-ink font-sans font-medium leading-snug sm:leading-relaxed">
                Ikuti setiap tahapan PEMIRA dan pastikan kamu tidak melewatkan waktu pemungutan suara.
            </p>
        </div>

        @if ($election)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 mt-8 sm:mt-12 mb-10 sm:mb-16">
                <div
                    class="lg:col-span-7 bg-brand text-surface border-2 border-ink shadow-brutal-lg p-5 sm:p-7 lg:p-8 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between gap-3 mb-6 sm:mb-8">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-accent inline-block"></span>
                            <span class="font-display font-bold text-xs sm:text-sm uppercase tracking-wider text-surface">
                                STATUS SAAT INI
                            </span>
                        </div>
                        <span
                            class="bg-accent text-ink px-2.5 sm:px-3 py-1 text-xs font-display font-bold uppercase border border-ink shadow-brutal-sm">
                            {{ $statusBadge }}
                        </span>
                    </div>

                    <div>
                        <h3
                            class="font-display font-black text-2xl sm:text-4xl lg:text-5xl tracking-tight uppercase leading-none mb-3 sm:mb-4 text-surface">
                            {{ $phaseTitle }}
                        </h3>
                        <div
                            class="inline-flex items-center gap-2 bg-brand-dark px-3 py-1.5 border border-surface/20 text-xs sm:text-sm font-display font-bold text-accent tracking-wide uppercase">
                            <span class="w-1.5 h-1.5 bg-accent inline-block"></span>
                            <span>{{ $activeDateRange }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="lg:col-span-5 bg-surface border-2 border-ink shadow-brutal-lg p-5 sm:p-7 lg:p-8 flex flex-col justify-between">
                    <div class="flex items-center gap-2 mb-4 sm:mb-6">
                        <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                        <span class="font-display font-bold text-xs sm:text-sm uppercase tracking-wider text-brand">
                            {{ $countdownLabel }}
                        </span>
                    </div>

                    <div
                        x-data="{
                            target: {{ $targetTimestampMs }},
                            isFinished: {{ $isFinished ? 'true' : 'false' }},
                            days: '00',
                            hours: '00',
                            minutes: '00',
                            seconds: '00',
                            init() {
                                this.update();
                                if (!this.isFinished && this.target > 0) {
                                    setInterval(() => this.update(), 1000);
                                }
                            },
                            update() {
                                if (this.isFinished || !this.target) {
                                    this.days = '00';
                                    this.hours = '00';
                                    this.minutes = '00';
                                    this.seconds = '00';
                                    return;
                                }
                                let diff = this.target - Date.now();
                                if (diff <= 0) {
                                    this.days = '00';
                                    this.hours = '00';
                                    this.minutes = '00';
                                    this.seconds = '00';
                                    return;
                                }
                                let d = Math.floor(diff / (1000 * 60 * 60 * 24));
                                let h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                let s = Math.floor((diff % (1000 * 60)) / 1000);
                                this.days = String(d).padStart(2, '0');
                                this.hours = String(h).padStart(2, '0');
                                this.minutes = String(m).padStart(2, '0');
                                this.seconds = String(s).padStart(2, '0');
                            }
                        }">
                        <div class="grid grid-cols-4 gap-2 sm:gap-3 text-center">
                            <div class="bg-surface-muted border-2 border-ink p-2 sm:p-3 shadow-brutal-sm">
                                <div class="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-brand leading-none" x-text="days">
                                    00
                                </div>
                                <div
                                    class="text-[9px] sm:text-[11px] font-sans font-bold uppercase tracking-wider text-ink mt-1">
                                    HARI
                                </div>
                            </div>
                            <div class="bg-surface-muted border-2 border-ink p-2 sm:p-3 shadow-brutal-sm">
                                <div class="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-brand leading-none" x-text="hours">
                                    00
                                </div>
                                <div
                                    class="text-[9px] sm:text-[11px] font-sans font-bold uppercase tracking-wider text-ink mt-1">
                                    JAM
                                </div>
                            </div>
                            <div class="bg-surface-muted border-2 border-ink p-2 sm:p-3 shadow-brutal-sm">
                                <div class="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-brand leading-none" x-text="minutes">
                                    00
                                </div>
                                <div
                                    class="text-[9px] sm:text-[11px] font-sans font-bold uppercase tracking-wider text-ink mt-1">
                                    MENIT
                                </div>
                            </div>
                            <div class="bg-surface-muted border-2 border-ink p-2 sm:p-3 shadow-brutal-sm">
                                <div class="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-brand leading-none text-accent" x-text="seconds">
                                    00
                                </div>
                                <div
                                    class="text-[9px] sm:text-[11px] font-sans font-bold uppercase tracking-wider text-ink mt-1">
                                    DETIK
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6 lg:gap-8">
                <div
                    class="bg-surface border-2 border-ink p-5 sm:p-6 lg:p-7 shadow-brutal flex flex-col justify-between relative">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <span class="font-display font-black text-3xl sm:text-4xl lg:text-5xl text-ink/30 leading-none">
                            01
                        </span>
                        <span
                            class="px-2 py-0.5 text-[10px] sm:text-xs font-display font-bold uppercase border border-ink {{ $step1Status === 'TAHAP AKTIF' ? 'bg-accent text-ink shadow-brutal-sm' : 'bg-surface-muted text-ink' }}">
                            {{ $step1Status }}
                        </span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-lg sm:text-xl text-brand uppercase mb-2">
                            PENDAFTARAN
                        </h4>
                        <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                            {{ $regStartFormatted }} — {{ $regEndFormatted }}
                        </p>
                    </div>
                </div>

                <div class="bg-surface border-2 border-ink p-5 sm:p-6 lg:p-7 shadow-brutal flex flex-col justify-between relative {{ $step2Status === 'TAHAP AKTIF' ? 'shadow-brutal-lg' : '' }}">
                    @if ($step2Status === 'TAHAP AKTIF')
                        <div
                            class="absolute -top-3 left-6 bg-accent text-ink px-2.5 py-0.5 text-[10px] sm:text-xs font-display font-bold uppercase border border-ink shadow-brutal-sm">
                            TAHAP AKTIF
                        </div>
                    @endif
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <span class="font-display font-black text-3xl sm:text-4xl lg:text-5xl {{ $step2Status === 'TAHAP AKTIF' ? 'text-brand' : 'text-ink/30' }} leading-none">
                            02
                        </span>
                        <span
                            class="px-2 py-0.5 text-[10px] sm:text-xs font-display font-bold uppercase border border-ink {{ $step2Status === 'TAHAP AKTIF' ? 'bg-accent text-ink shadow-brutal-sm' : 'bg-surface-muted text-ink' }}">
                            {{ $step2Status }}
                        </span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-lg sm:text-xl text-brand uppercase mb-2">
                            PEMUNGUTAN SUARA
                        </h4>
                        <p class="text-xs sm:text-sm font-sans {{ $step2Status === 'TAHAP AKTIF' ? 'font-bold text-brand' : 'font-medium text-ink/80' }} leading-relaxed">
                            {{ $votingStartFormatted }} — {{ $votingEndFormatted }}
                        </p>
                    </div>
                </div>

                <div
                    class="bg-surface border-2 border-ink p-5 sm:p-6 lg:p-7 shadow-brutal flex flex-col justify-between relative">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <span class="font-display font-black text-3xl sm:text-4xl lg:text-5xl text-ink/30 leading-none">
                            03
                        </span>
                        <span
                            class="px-2 py-0.5 text-[10px] sm:text-xs font-display font-bold uppercase border border-ink {{ $step3Status === 'TAHAP AKTIF' ? 'bg-accent text-ink shadow-brutal-sm' : 'bg-surface-muted text-ink/60' }}">
                            {{ $step3Status }}
                        </span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-lg sm:text-xl text-brand uppercase mb-2">
                            HASIL
                        </h4>
                        <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                            SETELAH PEMUNGUTAN SUARA BERAKHIR
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-8 sm:mt-12 bg-surface border-2 border-ink shadow-brutal-lg p-6 sm:p-10 text-center">
                <div class="max-w-md mx-auto space-y-3">
                    <div class="w-12 h-12 bg-surface-muted border-2 border-ink shadow-brutal-sm mx-auto flex items-center justify-center font-display font-black text-xl text-brand">
                        !
                    </div>
                    <h3 class="font-display font-black text-xl sm:text-2xl uppercase text-brand">
                        Jadwal pemilihan belum tersedia.
                    </h3>
                    <p class="text-xs sm:text-sm font-sans font-medium text-ink/70">
                        Informasi mengenai tahapan pendaftaran dan pemungutan suara akan segera diumumkan oleh panitia pemilihan.
                    </p>
                </div>
            </div>
        @endif
    </div>
</section>
