@php
    $election = $this->electionData;
    $participation = $this->participationData;
    $visualization = $this->visualizationData;
    $programs = $this->programParticipation;
    $activities = $this->recentActivities;
    $systemInfo = $this->systemInfo;
    $currentUser = auth()->user();
@endphp

<div class="space-y-6 sm:space-y-8">
    @if ($election)
        <section id="election-header" class="relative w-full bg-surface border-2 border-ink shadow-brutal p-5 sm:p-7 lg:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="font-display font-black text-3xl sm:text-5xl text-brand uppercase tracking-tight leading-none">
                            {{ $election['name'] }}
                        </h2>
                        <span class="inline-block px-3 py-1 text-xs sm:text-sm font-display font-black uppercase border-2 border-ink shadow-brutal-sm {{ $election['is_live'] ? 'bg-accent text-ink' : ($election['phase'] === 'finished' ? 'bg-ink text-surface' : 'bg-surface-muted text-brand') }}">
                            [{{ $election['phase_label'] }}]
                        </span>
                    </div>

                    <div class="mt-3 text-xs sm:text-sm font-sans font-semibold text-ink/80 flex items-center gap-2">
                        <span class="w-2 h-2 bg-brand inline-block"></span>
                        <span>{{ $election['contextual_date_label'] }}</span>
                    </div>
                </div>

                <div class="shrink-0"
                     x-data="{
                        target: {{ $election['target_timestamp_ms'] ?? 0 }},
                        phase: '{{ $election['phase'] }}',
                        days: '00',
                        hours: '00',
                        minutes: '00',
                        seconds: '00',
                        isFinished: {{ $election['phase'] === 'finished' ? 'true' : 'false' }},
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
                    <div class="bg-surface-muted border-2 border-ink p-3.5 sm:p-4 shadow-brutal-sm">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 font-display font-black text-lg sm:text-2xl text-brand tracking-tight">
                            <template x-if="days !== '00'">
                                <span class="flex items-center gap-1">
                                    <span x-text="days">00</span>
                                    <span class="text-xs font-sans font-bold text-ink/60 mr-1.5">HARI</span>
                                </span>
                            </template>
                            <span class="bg-surface border-2 border-ink px-2 sm:px-2.5 py-0.5 sm:py-1" x-text="hours">00</span>
                            <span class="text-ink/60">:</span>
                            <span class="bg-surface border-2 border-ink px-2 sm:px-2.5 py-0.5 sm:py-1" x-text="minutes">00</span>
                            <span class="text-ink/60">:</span>
                            <span class="bg-surface border-2 border-ink px-2 sm:px-2.5 py-0.5 sm:py-1 text-accent" x-text="seconds">00</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section id="election-header" class="relative w-full bg-surface border-2 border-ink shadow-brutal p-5 sm:p-7 lg:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-display font-black text-2xl sm:text-4xl text-brand uppercase tracking-tight leading-none">
                        Belum Ada Pemilihan
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm font-sans font-semibold text-ink/70">
                        Belum ada jadwal pemilihan yang aktif atau terdaftar di sistem.
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.elections.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand text-surface border-2 border-ink shadow-brutal text-xs sm:text-sm font-display font-bold uppercase hover:bg-brand-dark hover:translate-x-0.5 hover:translate-y-0.5 active:translate-x-1 active:translate-y-1 transition-all">
                        <span>Kelola Pemilihan</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <section id="participation-metrics" class="space-y-3">
        <h3 class="font-display font-extrabold text-lg sm:text-xl text-ink uppercase tracking-tight flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-brand inline-block"></span>
            <span>PARTISIPASI & HAK SUARA</span>
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60">TOTAL DPT</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-brand mt-1">
                    {{ $participation['eligible_formatted'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60">AKUN TERDAFTAR</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-ink mt-1">
                    {{ $participation['registered_formatted'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60">SUARA MASUK</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-brand mt-1">
                    {{ $participation['voted_formatted'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <div class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60">BELUM MEMILIH</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-ink mt-1">
                    {{ $participation['not_voted_formatted'] }}
                </div>
            </div>

            <div class="bg-brand text-surface border-2 border-ink p-4 shadow-brutal col-span-2 sm:col-span-1">
                <div class="text-xs font-display font-bold uppercase tracking-wider text-accent">TINGKAT PARTISIPASI</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-accent mt-1">
                    {{ $participation['rate_formatted'] }}
                </div>
            </div>
        </div>
    </section>

    <section id="participation-progress" class="bg-surface border-2 border-ink p-5 sm:p-6 shadow-brutal space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b-2 border-ink">
            <div>
                <h3 class="font-display font-extrabold text-base sm:text-lg text-brand uppercase">
                    PROGRES SUARA MASUK
                </h3>
                <div class="text-xs font-sans font-medium text-ink/70 mt-0.5">
                    Total {{ $visualization['ratio_text'] }} Mahasiswa
                </div>
            </div>
            <div class="px-3 py-1 bg-accent text-ink border-2 border-ink shadow-brutal-sm font-display font-black text-base sm:text-lg">
                {{ $visualization['rate_formatted'] }}
            </div>
        </div>

        <div class="space-y-2">
            <div class="w-full bg-surface-muted border-2 border-ink h-7 p-0.5 shadow-brutal-sm relative overflow-hidden">
                <div class="h-full bg-accent border-r-2 border-ink transition-all duration-500 ease-out"
                     style="width: {{ $visualization['progress_percent'] }}%">
                </div>
            </div>
            <div class="flex items-center justify-between text-xs font-sans font-semibold text-ink">
                <span>0</span>
                <span class="font-bold text-brand">{{ $visualization['not_voted_label'] }}</span>
                <span>{{ $participation['eligible_formatted'] }} DPT</span>
            </div>
        </div>

        <div class="pt-3 border-t-2 border-ink/15 flex flex-wrap items-center justify-between gap-2 text-xs font-sans text-ink/70">
            <span>Aktivasi Akun: <strong class="text-ink">{{ $participation['registered_formatted'] }} ({{ $participation['registration_rate_formatted'] }})</strong></span>
            <span>Pilihan suara tidak terhubung ke identitas pemilih.</span>
        </div>
    </section>

    <section id="department-participation" class="bg-surface border-2 border-ink p-5 sm:p-6 shadow-brutal space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3 pb-3 border-b-2 border-ink">
            <div>
                <h3 class="font-display font-extrabold text-base sm:text-lg text-brand uppercase">
                    PARTISIPASI JURUSAN
                </h3>
                <div class="text-xs font-sans font-medium text-ink/70 mt-0.5">
                    Komparasi perolehan suara per jurusan di Politeknik Negeri Bali
                </div>
            </div>
            <span class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60">{{ count($programs) }} Jurusan PNB</span>
        </div>

        <div class="space-y-3.5">
            @forelse ($programs as $prog)
                <div class="space-y-1.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2 text-xs font-sans">
                        <div class="font-display font-bold text-ink truncate pr-2 min-w-0">
                            <span class="px-1.5 py-0.5 bg-surface-muted border border-ink text-xs font-display font-black text-brand mr-1.5 shrink-0">{{ $prog['code'] }}</span>
                            <span class="truncate">{{ $prog['name'] }}</span>
                        </div>
                        <div class="shrink-0 flex items-center gap-2 self-start sm:self-auto">
                            <span class="text-ink/70 font-semibold">{{ number_format($prog['voted'], 0, ',', '.') }} / {{ number_format($prog['eligible'], 0, ',', '.') }} Suara</span>
                            <span class="px-1.5 py-0.5 bg-brand text-surface text-xs font-display font-black border border-ink">{{ $prog['rate_formatted'] }}</span>
                        </div>
                    </div>

                    <div class="w-full bg-surface-muted border border-ink h-3 overflow-hidden">
                        <div class="bg-accent h-full border-r border-ink transition-all duration-300"
                             style="width: {{ min(100, max(0, $prog['rate'])) }}%">
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-4 text-center text-xs font-sans font-medium text-ink/60">
                    Belum ada data jurusan yang terdaftar.
                </div>
            @endforelse
        </div>
    </section>

    <div class="grid grid-cols-1 {{ ($currentUser?->isSuperAdmin() && $systemInfo) ? 'lg:grid-cols-12' : '' }} gap-6">
        <section id="recent-activity" class="{{ ($currentUser?->isSuperAdmin() && $systemInfo) ? 'lg:col-span-7' : 'w-full' }} bg-surface border-2 border-ink p-5 sm:p-6 shadow-brutal space-y-3">
            <div class="flex items-center justify-between pb-3 border-b-2 border-ink">
                <h3 class="font-display font-extrabold text-base sm:text-lg text-ink uppercase">
                    LOG SUARA MASUK TERKINI
                </h3>
                <span class="text-xs font-display font-bold uppercase tracking-wider px-2 py-0.5 bg-surface-muted border border-ink text-ink/70">
                    ANONIM
                </span>
            </div>

            <div class="divide-y-2 divide-ink/10">
                @forelse ($activities as $act)
                    <div class="py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <span class="font-display font-bold text-xs text-brand shrink-0">
                                {{ $act['time'] }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="font-display font-bold text-xs text-ink uppercase truncate">
                                    {{ $act['title'] }}
                                </div>
                                <p class="text-xs font-sans text-ink/70 truncate">
                                    {{ $act['description'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0 self-start sm:self-auto">
                            @if (!empty($act['department']))
                                <span class="text-xs font-display font-black uppercase px-1.5 py-0.5 bg-brand text-surface border border-ink">
                                    {{ $act['department'] }}
                                </span>
                            @endif
                            <span class="text-xs font-display font-bold uppercase tracking-wider px-1.5 py-0.5 border border-ink bg-surface-muted text-ink/70">
                                {{ $act['type'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs font-sans font-medium text-ink/60">
                        Belum ada aktivitas suara yang tercatat.
                    </div>
                @endforelse
            </div>
        </section>

        @if ($currentUser?->isSuperAdmin() && $systemInfo)
            <section id="super-admin-system" class="lg:col-span-5 bg-brand text-surface border-2 border-ink p-5 sm:p-6 shadow-brutal space-y-3">
                <div class="flex items-center justify-between pb-3 border-b-2 border-ink/40">
                    <h3 class="font-display font-extrabold text-base text-accent uppercase">
                        OTORITAS SISTEM
                    </h3>
                    <span class="px-1.5 py-0.5 bg-accent text-ink text-xs font-display font-black uppercase">SUPER ADMIN</span>
                </div>

                <div class="space-y-2.5 text-xs font-sans">
                    <div class="flex items-center justify-between p-2.5 bg-surface/10 border border-surface/20">
                        <span class="text-surface/90">Permohonan Jadwal</span>
                        <span class="font-display font-bold text-accent">{{ $systemInfo['pending_schedule_requests'] }} Menunggu</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-surface/10 border border-surface/20">
                        <span class="text-surface/90">Akun Pengelola</span>
                        <span class="font-display font-bold text-surface">{{ $systemInfo['total_admins'] }} Terdaftar</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-surface/10 border border-surface/20">
                        <span class="text-surface/90">Integritas Audit Trail</span>
                        <span class="font-display font-bold text-accent">{{ $systemInfo['engine_status'] }}</span>
                    </div>
                </div>
            </section>
        @endif
    </div>
</div>
