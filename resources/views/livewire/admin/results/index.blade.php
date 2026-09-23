<div class="space-y-6 sm:space-y-8">
    @if (! $data)
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg sm:text-xl text-brand uppercase">
                    BELUM ADA DATA PEMILIHAN
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Data pemilihan raya belum dikonfigurasi atau belum tersedia di sistem.
                </p>
            </div>
        </div>
    @else
        <div class="bg-brand text-surface border-2 border-ink p-5 sm:p-8 shadow-brutal relative overflow-hidden">
            <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-accent/10 rounded-full pointer-events-none blur-2xl"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-accent text-brand border border-ink text-xs font-display font-black uppercase tracking-wider shadow-brutal-sm">
                        <span class="w-2 h-2 bg-brand inline-block"></span>
                        <span>PEMILIHAN RAYA MAHASISWA &middot; {{ $data['election']->year }}</span>
                    </div>

                    <h2 class="font-display font-black text-2xl sm:text-4xl text-surface uppercase tracking-tight leading-none">
                        HASIL PERHITUNGAN SUARA RESMI
                    </h2>

                    <p class="text-xs sm:text-sm font-sans text-surface/80 leading-relaxed">
                        {{ $data['election']->name }} &mdash; {{ $data['status_subtext'] }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-start sm:items-center lg:items-end xl:items-center gap-3 shrink-0">
                    @if ($elections->count() > 1)
                        <div class="w-full sm:w-auto">
                            <label for="election-selector" class="sr-only">Pilih Pemilihan</label>
                            <select id="election-selector"
                                    wire:change="selectElection($event.target.value)"
                                    class="w-full sm:w-auto bg-surface text-ink border-2 border-ink px-3 py-2 text-xs font-display font-bold uppercase tracking-wider shadow-brutal-sm focus:outline-none cursor-pointer">
                                @foreach ($elections as $elec)
                                    <option value="{{ $elec->id }}" @selected($elec->id === $data['election']->id)>
                                        {{ $elec->name }} ({{ $elec->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                        <div class="inline-flex items-center gap-2 px-3 py-2 border-2 border-ink text-xs font-display font-black uppercase tracking-wider shadow-brutal-sm {{ $data['is_finished'] ? 'bg-emerald-300 text-emerald-950' : ($data['is_voting'] ? 'bg-accent text-brand' : 'bg-surface-muted text-ink') }}">
                            @if ($data['is_finished'])
                                <svg class="w-4 h-4 text-emerald-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif ($data['is_voting'])
                                <span class="w-2 h-2 bg-red-600 rounded-full animate-ping shrink-0"></span>
                            @else
                                <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                            <span>{{ $data['status_label'] }}</span>
                        </div>

                        <div class="relative group/refresh">
                            <button wire:click="refreshResults"
                                    type="button"
                                    class="w-10 h-10 bg-surface hover:bg-accent text-ink border-2 border-ink shadow-brutal-sm flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-accent"
                                    aria-label="Perbarui data perhitungan suara"
                                    title="Perbarui data perhitungan">
                                <svg class="w-4 h-4 text-ink transition-transform active:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            <span role="tooltip" class="pointer-events-none absolute bottom-full right-0 mb-1.5 hidden group-hover/refresh:block px-2 py-0.5 text-[10px] font-sans font-bold uppercase tracking-wider text-surface bg-ink whitespace-nowrap shadow-sm z-30">
                                Perbarui &middot; {{ $data['calculated_at'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x-2 divide-ink">
                <div class="p-4 sm:p-5 flex flex-col justify-between">
                    <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60">
                        TOTAL SUARA MASUK
                    </span>
                    <div class="font-display font-black text-2xl sm:text-4xl text-brand mt-1 truncate">
                        {{ $data['total_ballots_formatted'] }}
                    </div>
                    <span class="text-[11px] font-sans text-ink/70 mt-1 block">
                        Lembar surat suara sah
                    </span>
                </div>

                <div class="p-4 sm:p-5 flex flex-col justify-between">
                    <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60">
                        TINGKAT PARTISIPASI
                    </span>
                    <div class="font-display font-black text-2xl sm:text-4xl text-brand mt-1 truncate">
                        {{ $data['participation_rate_formatted'] }}
                    </div>
                    <span class="text-[11px] font-sans text-ink/70 mt-1 block">
                        {{ $data['total_participations_formatted'] }} dari {{ $data['total_eligible_formatted'] }} DPT
                    </span>
                </div>

                <div class="p-4 sm:p-5 flex flex-col justify-between">
                    <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60">
                        TOTAL PEMILIH (DPT)
                    </span>
                    <div class="font-display font-black text-2xl sm:text-4xl text-ink mt-1 truncate">
                        {{ $data['total_eligible_formatted'] }}
                    </div>
                    <span class="text-[11px] font-sans text-ink/70 mt-1 block">
                        {{ $data['total_registered_formatted'] }} akun terdaftar
                    </span>
                </div>

                <div class="p-4 sm:p-5 flex flex-col justify-between bg-surface-muted/50">
                    <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60">
                        PERIODE PEMILIHAN
                    </span>
                    <div class="font-mono font-bold text-xs sm:text-sm text-ink mt-1 leading-snug">
                        {{ $data['election']->voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} &mdash; {{ $data['election']->voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                    </div>
                    <span class="text-[11px] font-sans font-bold text-brand mt-1 block">
                        Sinkronisasi: {{ $data['calculated_at'] }}
                    </span>
                </div>
            </div>
        </div>

        @if ($data['is_upcoming'])
            <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
                <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                    <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div class="max-w-xl mx-auto space-y-2">
                    <h3 class="font-display font-black text-xl sm:text-2xl text-brand uppercase">
                        HASIL BELUM TERSEDIA
                    </h3>
                    <p class="text-xs sm:text-sm font-sans text-ink/80 leading-relaxed">
                        Hasil perolehan suara belum dapat ditampilkan karena pemungutan suara belum dimulai. Bilik suara digital akan dibuka sesuai jadwal resmi PEMIRA KBM PNB.
                    </p>
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-2 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs font-mono font-bold text-ink">
                    <span>Jadwal Voting: {{ $data['voting_start_formatted'] }} s/d {{ $data['voting_end_formatted'] }}</span>
                </div>
            </div>

            @if ($data['has_candidates'])
                <div class="space-y-4">
                    <div class="border-b-2 border-ink pb-2">
                        <h3 class="font-display font-black text-lg text-brand uppercase tracking-tight">
                            DAFTAR PASANGAN CALON TERDAFTAR
                        </h3>
                        <p class="text-xs font-sans text-ink/70">
                            Kandidat resmi yang akan bertanding dalam pemilihan ini.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach ($data['candidate_results'] as $item)
                            <div class="bg-surface border-2 border-ink p-4 sm:p-5 shadow-brutal space-y-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-2 border-b-2 border-ink pb-2.5">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-brand text-accent text-xs font-display font-black uppercase border border-ink shadow-2xs">
                                            PASLON #{{ $item['formatted_number'] }}
                                        </span>
                                        <span class="text-[11px] font-sans font-bold text-ink/60 uppercase">
                                            Terdaftar Resmi
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-surface-muted border-2 border-ink shrink-0 overflow-hidden shadow-brutal-sm">
                                            @if ($item['photo'])
                                                <img src="{{ asset('storage/' . $item['photo']) }}"
                                                     alt="Foto Paslon {{ $item['formatted_number'] }}"
                                                     class="w-full h-full object-cover" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center font-display font-black text-lg text-ink/40">
                                                    {{ $item['formatted_number'] }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-1.5 min-w-0 flex-1">
                                            <div>
                                                <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/50 block">Calon Ketua</span>
                                                <div class="font-display font-black text-xs sm:text-sm text-ink truncate" title="{{ $item['leader_name'] }}">
                                                    {{ $item['leader_name'] }}
                                                </div>
                                                <span class="text-[10px] font-sans text-ink/60 truncate block">{{ $item['leader_study_program'] }}</span>
                                            </div>

                                            <div>
                                                <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/50 block">Calon Wakil</span>
                                                <div class="font-display font-black text-xs sm:text-sm text-ink truncate" title="{{ $item['vice_leader_name'] }}">
                                                    {{ $item['vice_leader_name'] }}
                                                </div>
                                                <span class="text-[10px] font-sans text-ink/60 truncate block">{{ $item['vice_leader_study_program'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($item['vision'])
                                        <div class="p-2.5 bg-surface-muted border border-ink/20 text-xs font-sans text-ink/80 italic">
                                            &ldquo;{{ \Illuminate\Support\Str::limit($item['vision'], 120) }}&rdquo;
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-ink/10 text-center">
                                    <span class="text-xs font-display font-bold uppercase text-ink/50 tracking-wider">
                                        Perolehan suara dibuka saat voting
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b-2 border-ink pb-2">
                    <div>
                        <h3 class="font-display font-black text-xl sm:text-2xl text-brand uppercase tracking-tight">
                            PEROLEHAN SUARA PASANGAN CALON
                        </h3>
                        <p class="text-xs sm:text-sm font-sans text-ink/70">
                            @if ($data['is_finished'])
                                Hasil resmi penetapan suara pemilihan raya.
                            @else
                                Distribusi suara sementara yang telah tercatat dan terverifikasi.
                            @endif
                        </p>
                    </div>

                    @if ($data['is_finished'] && $data['has_ballots'])
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-950 border border-ink text-xs font-display font-bold uppercase">
                            <span class="w-2 h-2 bg-emerald-700 inline-block"></span>
                            <span>DATA TERKUNCI & FINAL</span>
                        </div>
                    @elseif ($data['is_voting'])
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-accent text-brand border border-ink text-xs font-display font-bold uppercase">
                            <span class="w-2 h-2 bg-red-600 rounded-full animate-ping"></span>
                            <span>LIVE COUNT AKTIF</span>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 {{ $data['candidate_results']->count() >= 3 ? 'lg:grid-cols-3' : '' }} gap-4 sm:gap-6">
                    @foreach ($data['candidate_results'] as $item)
                        <div class="bg-surface border-2 border-ink shadow-brutal flex flex-col justify-between relative overflow-hidden transition-all {{ $item['is_highest'] ? 'ring-3 ring-brand' : '' }}">
                            @if ($item['is_highest'])
                                <div class="bg-brand text-accent px-4 py-1.5 text-xs font-display font-black uppercase tracking-wider text-center border-b-2 border-ink flex items-center justify-center gap-1.5 shadow-xs">
                                    <svg class="w-4 h-4 fill-accent" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span>PEROLEHAN SUARA TERTINGGI</span>
                                </div>
                            @endif

                            <div class="p-5 sm:p-6 space-y-4 sm:space-y-5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="inline-flex items-center px-3 py-1 bg-surface-muted text-ink border-2 border-ink text-xs font-display font-black uppercase tracking-wider shadow-brutal-sm">
                                        PASLON #{{ $item['formatted_number'] }}
                                    </span>

                                    <div class="text-right">
                                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">SUARA MASUK</span>
                                        <span class="font-mono font-bold text-sm text-ink">{{ $item['votes_formatted'] }} suara</span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-surface-muted border-2 border-ink shrink-0 overflow-hidden shadow-brutal-sm relative">
                                        @if ($item['photo'])
                                            <img src="{{ asset('storage/' . $item['photo']) }}"
                                                 alt="Foto Paslon {{ $item['formatted_number'] }}"
                                                 class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-display font-black text-2xl text-ink/40 bg-surface-muted">
                                                {{ $item['formatted_number'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-2 min-w-0 flex-1">
                                        <div>
                                            <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/50 block">Calon Ketua</span>
                                            <div class="font-display font-black text-sm sm:text-base text-ink uppercase truncate" title="{{ $item['leader_name'] }}">
                                                {{ $item['leader_name'] }}
                                            </div>
                                            <span class="text-xs font-sans text-ink/70 block truncate">{{ $item['leader_study_program'] }}</span>
                                        </div>

                                        <div>
                                            <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/50 block">Calon Wakil</span>
                                            <div class="font-display font-black text-sm sm:text-base text-ink uppercase truncate" title="{{ $item['vice_leader_name'] }}">
                                                {{ $item['vice_leader_name'] }}
                                            </div>
                                            <span class="text-xs font-sans text-ink/70 block truncate">{{ $item['vice_leader_study_program'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-3 border-t-2 border-ink space-y-2">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60">
                                            PERSENTASE
                                        </span>
                                        <div class="font-display font-black text-4xl sm:text-5xl text-brand leading-none">
                                            {{ $item['percentage_formatted'] }}
                                        </div>
                                    </div>

                                    <div class="w-full h-4 bg-surface-muted border-2 border-ink overflow-hidden p-0.5">
                                        <div class="h-full {{ $item['colors']['progress'] }} transition-all duration-500"
                                             style="width: {{ min(100, max(0, $item['percentage'])) }}%"></div>
                                    </div>

                                    <div class="flex items-center justify-between text-[11px] font-sans text-ink/70 pt-0.5">
                                        <span>Proporsi DPT: <strong>{{ $item['percentage_of_dpt_formatted'] }}</strong></span>
                                        <span>Total: <strong>{{ $item['votes_formatted'] }} suara</strong></span>
                                    </div>
                                </div>
                            </div>

                            @if ($item['vision'])
                                <div class="px-5 py-3 sm:px-6 bg-surface-muted border-t-2 border-ink text-xs font-sans text-ink/80 leading-relaxed">
                                    <span class="font-display font-bold text-ink uppercase text-[10px] block mb-0.5">Visi:</span>
                                    <span class="line-clamp-2">&ldquo;{{ $item['vision'] }}&rdquo;</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($data['has_ballots'] && $data['candidate_results']->count() > 1)
                    <div class="bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <span class="text-xs font-display font-black uppercase tracking-wider text-brand">
                                PROPORSI PEROLEHAN SUARA KANDIDAT (100% SUARA MASUK)
                            </span>
                            <span class="text-xs font-mono font-bold text-ink/70">
                                Total: {{ $data['total_ballots_formatted'] }} Surat Suara
                            </span>
                        </div>

                        <div class="w-full h-8 sm:h-9 bg-surface-muted border-2 border-ink shadow-brutal-sm overflow-hidden flex">
                            @foreach ($data['candidate_results'] as $item)
                                @if ($item['percentage'] > 0)
                                    <div class="h-full {{ $item['colors']['progress'] }} flex items-center justify-center text-xs font-display font-black truncate px-1 transition-all"
                                         style="width: {{ $item['percentage'] }}%"
                                         title="Paslon #{{ $item['formatted_number'] }}: {{ $item['percentage_formatted'] }} ({{ $item['votes_formatted'] }} suara)">
                                        <span class="truncate px-1 {{ $item['colors']['text'] }}">#{{ $item['formatted_number'] }} ({{ $item['percentage_formatted'] }})</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 text-xs font-sans">
                            @foreach ($data['candidate_results'] as $item)
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 {{ $item['colors']['progress'] }} border border-ink inline-block shrink-0"></span>
                                    <span class="font-bold text-ink">Paslon #{{ $item['formatted_number'] }}:</span>
                                    <span class="text-ink/80">{{ $item['votes_formatted'] }} suara ({{ $item['percentage_formatted'] }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-surface border-2 border-ink p-5 sm:p-6 shadow-brutal space-y-5">
                    <div class="border-b-2 border-ink pb-2.5">
                        <h4 class="font-display font-black text-lg text-brand uppercase tracking-tight">
                            PARTISIPASI PEMILIH
                        </h4>
                        <p class="text-xs font-sans text-ink/70">
                            Perbandingan penggunaan hak pilih mahasiswa terhadap daftar pemilih tetap.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs font-display font-bold uppercase mb-1.5">
                                <span>Partisipasi Voting Mahasiswa</span>
                                <span class="font-black text-brand">{{ $data['participation_rate_formatted'] }}</span>
                            </div>
                            <div class="w-full h-5 bg-surface-muted border-2 border-ink p-0.5 shadow-2xs">
                                <div class="h-full bg-brand transition-all" style="width: {{ min(100, max(0, $data['participation_rate'])) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] font-sans text-ink/70 mt-1">
                                <span>{{ $data['total_participations_formatted'] }} mahasiswa telah memilih</span>
                                <span>{{ $data['unvoted_formatted'] }} belum menggunakan hak suara</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-xs font-display font-bold uppercase mb-1.5">
                                <span>Aktivasi Akun Pemilih</span>
                                <span class="font-black text-brand">
                                    {{ $data['total_eligible'] > 0 ? number_format(($data['total_registered'] / $data['total_eligible']) * 100, 2, ',', '.') . '%' : '0%' }}
                                </span>
                            </div>
                            <div class="w-full h-5 bg-surface-muted border-2 border-ink p-0.5 shadow-2xs">
                                <div class="h-full bg-accent transition-all" style="width: {{ $data['total_eligible'] > 0 ? min(100, max(0, ($data['total_registered'] / $data['total_eligible']) * 100)) : 0 }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] font-sans text-ink/70 mt-1">
                                <span>{{ $data['total_registered_formatted'] }} akun terverifikasi</span>
                                <span>{{ $data['total_eligible_formatted'] }} total hak suara DPT</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-2xs">
                            <span class="text-[10px] font-display font-bold uppercase text-ink/60 block">SUARA MASUK</span>
                            <span class="font-mono font-black text-lg text-brand">{{ $data['total_ballots_formatted'] }}</span>
                        </div>
                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-2xs">
                            <span class="text-[10px] font-display font-bold uppercase text-ink/60">BELUM MEMILIH</span>
                            <span class="font-mono font-black text-lg text-ink/80">{{ $data['unvoted_formatted'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-surface border-2 border-ink p-5 sm:p-6 shadow-brutal space-y-4">
                    <div class="border-b-2 border-ink pb-2.5">
                        <h4 class="font-display font-black text-lg text-brand uppercase tracking-tight">
                            PARTISIPASI PER PROGRAM STUDI / JURUSAN
                        </h4>
                        <p class="text-xs font-sans text-ink/70">
                            Tingkat partisipasi penggunaan hak suara di masing-masing jurusan.
                        </p>
                    </div>

                    @if ($data['department_turnout']->isNotEmpty())
                        <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                            @foreach ($data['department_turnout'] as $dept)
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs font-sans">
                                        <span class="font-bold text-ink truncate max-w-xs">{{ $dept['name'] }} ({{ $dept['code'] }})</span>
                                        <span class="font-mono font-bold text-brand shrink-0">{{ $dept['rate_formatted'] }}</span>
                                    </div>
                                    <div class="w-full h-3 bg-surface-muted border border-ink overflow-hidden">
                                        <div class="h-full bg-brand transition-all" style="width: {{ min(100, max(0, $dept['rate'])) }}%"></div>
                                    </div>
                                    <div class="text-[10px] font-mono text-ink/60 text-right">
                                        {{ $dept['voted'] }} / {{ $dept['eligible'] }} mahasiswa
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 bg-surface-muted border-2 border-ink text-center text-xs font-sans text-ink/60">
                            Data rincian jurusan belum tersedia.
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-surface border-2 border-ink shadow-brutal space-y-3">
                <div class="p-4 sm:p-5 border-b-2 border-ink bg-surface-muted">
                    <h4 class="font-display font-black text-base sm:text-lg text-brand uppercase tracking-tight">
                        DETAIL PEROLEHAN SUARA & BERITA ACARA REKAPITULASI
                    </h4>
                    <p class="text-xs font-sans text-ink/70 mt-0.5">
                        Rincian tabulasi matematis perolehan suara sah berdasarkan basis data bilik suara digital.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-ink bg-surface-muted text-center text-xs font-display font-black text-brand uppercase tracking-wider">
                                <th class="px-3 py-3 w-12 sm:w-16">NO</th>
                                <th class="px-4 py-3 w-28">NO URUT</th>
                                <th class="px-4 py-3 text-left">PASANGAN CALON (KETUA & WAKIL)</th>
                                <th class="px-4 py-3">PEROLEHAN SUARA</th>
                                <th class="px-4 py-3">PERSENTASE SUARA MASUK</th>
                                <th class="px-4 py-3">PERSENTASE DARI DPT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y border-b-2 border-ink">
                            @foreach ($data['candidate_results'] as $idx => $item)
                                <tr class="hover:bg-surface-muted/50 transition-colors {{ $item['is_highest'] ? 'bg-amber-50/50' : '' }}">
                                    <td class="px-3 py-3 text-center text-xs font-mono font-bold text-ink/70">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 bg-brand text-accent text-xs font-display font-black border border-ink">
                                            #{{ $item['formatted_number'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-display font-black text-xs sm:text-sm text-ink uppercase">
                                            {{ $item['leader_name'] }} &amp; {{ $item['vice_leader_name'] }}
                                        </div>
                                        <div class="text-[11px] font-sans text-ink/70">
                                            {{ $item['leader_study_program'] }} &middot; {{ $item['vice_leader_study_program'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-black text-sm text-brand">
                                        {{ $item['votes_formatted'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-display font-black text-sm text-brand">
                                        {{ $item['percentage_formatted'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold text-xs text-ink/80">
                                        {{ $item['percentage_of_dpt_formatted'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-surface-muted font-display font-black text-xs text-ink border-b-2 border-ink">
                                <td colspan="3" class="px-4 py-3 text-right uppercase tracking-wider">
                                    TOTAL SUARA SAH MASUK:
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-black text-sm text-brand">
                                    {{ $data['total_ballots_formatted'] }}
                                </td>
                                <td class="px-4 py-3 text-center font-black text-sm text-brand">
                                    {{ $data['has_ballots'] ? '100,00%' : '0,00%' }}
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-xs text-ink">
                                    {{ $data['ballot_turnout_rate_formatted'] }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-4 sm:p-5 bg-surface-muted/30 border-t border-ink/10 text-xs font-sans text-ink/70 space-y-1">
                    <p>
                        <strong>Penyelenggara:</strong> Komisi Pemilihan Raya Mahasiswa (KPRM) Politeknik Negeri Bali.
                    </p>
                    <p>
                        <strong>Integritas Suara:</strong> Surat suara tersimpan secara anonim tanpa identitas pemilih (Asas LUBERJURDIL).
                    </p>
                    <p>
                        <strong>Status Data:</strong> {{ $data['status_label'] }} &middot; Terakhir diperbarui pada {{ $data['calculated_at'] }}.
                    </p>
                </div>
            </div>
        @endif
    @endif
</div>
