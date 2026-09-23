<div class="space-y-6 pb-28">
    <div class="bg-surface border-2 border-ink p-5 sm:p-6 shadow-brutal flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('voter.dashboard') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-display font-bold uppercase text-ink bg-surface-muted border border-ink hover:bg-surface hover:shadow-brutal-sm transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </a>
                <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-green-200 text-green-950 border border-ink">
                    SESI VOTING AKTIF
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-display font-black text-brand tracking-tight mt-2 uppercase">
                Bilik Suara Digital
            </h1>
            <p class="text-xs sm:text-sm text-ink/75 font-medium">
                {{ $election->name }} &bull; Periode {{ $election->year }}
            </p>
        </div>

        <div class="p-3 bg-accent/20 border-2 border-ink flex items-center gap-3 text-xs max-w-sm">
            <svg class="w-6 h-6 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <div>
                <p class="font-bold text-ink">Asas Kerahasiaan Terjamin</p>
                <p class="text-[11px] text-ink/80 leading-tight">Pilihan Anda dienkripsi tanpa menghubungkan nama/NIM dengan surat suara.</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-muted border-2 border-ink p-4 text-xs sm:text-sm text-ink/85 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-brand inline-block border border-ink"></span>
            <span>Silakan tentukan <strong>satu pasangan calon</strong> pilihan Anda dengan mengklik kartu atau tombol <strong>PILIH</strong>.</span>
        </div>
        <div class="font-mono text-xs font-bold text-ink/70 shrink-0 hidden sm:block">
            Total: {{ $candidates->count() }} Pasangan Calon
        </div>
    </div>

    <div
        role="radiogroup"
        aria-label="Daftar Pasangan Calon"
        class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8"
    >
        @foreach ($candidates as $candidate)
            @php
                $isSelected = $selectedCandidatePairId === $candidate->id;
                $leader = $candidate->leaderMember?->eligibleVoter;
                $viceLeader = $candidate->viceLeaderMember?->eligibleVoter;
            @endphp

            <div
                role="radio"
                aria-checked="{{ $isSelected ? 'true' : 'false' }}"
                tabindex="0"
                wire:key="candidate-card-{{ $candidate->id }}"
                wire:click="selectCandidate({{ $candidate->id }})"
                wire:keydown.enter="selectCandidate({{ $candidate->id }})"
                wire:keydown.space.prevent="selectCandidate({{ $candidate->id }})"
                class="cursor-pointer transition-all duration-150 flex flex-col justify-between border-2 border-ink bg-surface p-5 sm:p-6 relative focus:outline-none focus:ring-4 focus:ring-accent {{ $isSelected ? 'ring-4 ring-brand shadow-brutal-lg bg-accent/10 border-brand -translate-x-0.5 -translate-y-0.5' : 'shadow-brutal hover:shadow-brutal-lg hover:-translate-x-px hover:-translate-y-px' }}"
            >
                @if ($isSelected)
                    <div class="absolute -top-3.5 right-4 z-10 px-3 py-1 bg-brand text-surface text-xs font-display font-black tracking-wider uppercase border-2 border-ink shadow-brutal-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>PILIHAN ANDA</span>
                    </div>
                @endif

                <div>
                    <div class="flex items-center justify-between pb-4 border-b-2 border-ink mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-brand text-surface border-2 border-ink flex flex-col items-center justify-center font-display shadow-brutal-sm">
                                <span class="text-[10px] font-bold tracking-widest text-accent uppercase leading-none">NO</span>
                                <span class="text-2xl sm:text-3xl font-black leading-none mt-0.5">{{ $candidate->formattedNumber() }}</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-mono font-bold uppercase text-ink/60">Pasangan Calon</span>
                                <h2 class="text-lg sm:text-xl font-display font-extrabold text-ink leading-tight">
                                    {{ $leader?->name ?? 'Calon Ketua' }}
                                    <span class="text-sm font-semibold text-ink/70 block">&amp; {{ $viceLeader?->name ?? 'Calon Wakil' }}</span>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="aspect-16/10 sm:aspect-video w-full bg-surface-muted border-2 border-ink overflow-hidden mb-4 relative shadow-brutal-sm flex items-center justify-center">
                        @if ($candidate->photo)
                            <img
                                src="{{ asset('storage/' . $candidate->photo) }}"
                                alt="Foto Paslon {{ $candidate->formattedNumber() }}"
                                class="w-full h-full object-cover object-center"
                                loading="lazy"
                            >
                        @else
                            <div class="flex flex-col items-center justify-center text-ink/40 p-4 text-center">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-xs font-mono font-medium">Foto Paslon {{ $candidate->formattedNumber() }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 mb-5">
                        <div class="p-2.5 bg-surface-muted border border-ink text-xs">
                            <span class="text-[10px] font-mono font-bold uppercase text-brand block">Calon Ketua Umum</span>
                            <p class="font-bold text-ink text-sm">{{ $leader?->name ?? '-' }}</p>
                            <p class="text-[11px] text-ink/75 font-mono">NIM: {{ $leader?->nim ?? '-' }} &bull; {{ $leader?->studyProgram?->name ?? '-' }}</p>
                        </div>

                        <div class="p-2.5 bg-surface-muted border border-ink text-xs">
                            <span class="text-[10px] font-mono font-bold uppercase text-brand block">Calon Wakil Ketua</span>
                            <p class="font-bold text-ink text-sm">{{ $viceLeader?->name ?? '-' }}</p>
                            <p class="text-[11px] text-ink/75 font-mono">NIM: {{ $viceLeader?->nim ?? '-' }} &bull; {{ $viceLeader?->studyProgram?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t-2 border-ink space-y-2">
                    <button
                        type="button"
                        wire:click.stop="openDetailModal({{ $candidate->id }})"
                        class="w-full text-center px-3 py-2 text-xs font-display font-bold uppercase tracking-wide text-ink bg-surface border-2 border-ink hover:bg-surface-muted active:translate-x-0.5 active:translate-y-0.5 transition-all"
                    >
                        Lihat Visi &amp; Misi Paslon &rarr;
                    </button>

                    <button
                        type="button"
                        wire:click.stop="selectCandidate({{ $candidate->id }})"
                        class="w-full min-h-12 px-4 py-3 text-sm font-display font-extrabold uppercase tracking-wider border-2 border-ink shadow-brutal-sm flex items-center justify-center gap-2 transition-all {{ $isSelected ? 'bg-brand text-surface hover:bg-brand-dark' : 'bg-surface-muted text-ink hover:bg-accent hover:text-ink' }}"
                    >
                        @if ($isSelected)
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>PASLON INI TELAH DIPILIH</span>
                        @else
                            <span class="w-4 h-4 rounded-full border-2 border-ink inline-block"></span>
                            <span>PILIH PASLON {{ $candidate->formattedNumber() }}</span>
                        @endif
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="fixed bottom-0 inset-x-0 z-30 bg-surface border-t-4 border-ink shadow-[0_-4px_12px_rgba(0,0,0,0.15)] py-3 sm:py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="w-10 h-10 {{ $selectedCandidate ? 'bg-brand text-surface' : 'bg-surface-muted text-ink/40' }} border-2 border-ink flex items-center justify-center font-display font-black text-sm shrink-0">
                    {{ $selectedCandidate ? $selectedCandidate->formattedNumber() : '?' }}
                </div>
                <div class="truncate">
                    <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Status Pilihan Anda</span>
                    @if ($selectedCandidate)
                        <p class="text-xs sm:text-sm font-bold text-ink truncate">
                            Paslon {{ $selectedCandidate->formattedNumber() }}: {{ $selectedCandidate->leaderMember?->eligibleVoter?->name ?? 'Ketua' }} &amp; {{ $selectedCandidate->viceLeaderMember?->eligibleVoter?->name ?? 'Wakil' }}
                        </p>
                    @else
                        <p class="text-xs sm:text-sm font-medium text-ink/70">
                            Silakan klik kartu salah satu paslon di atas.
                        </p>
                    @endif
                </div>
            </div>

            <div class="w-full sm:w-auto shrink-0">
                <button
                    type="button"
                    wire:click="openConfirmModal"
                    @disabled(! $selectedCandidatePairId)
                    class="w-full sm:w-auto min-h-12 px-6 sm:px-8 py-3 text-sm sm:text-base font-display font-black tracking-wider uppercase border-2 border-ink transition-all flex items-center justify-center gap-2 {{ $selectedCandidatePairId ? 'bg-accent text-ink shadow-brutal hover:bg-accent-light hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm active:translate-x-1 active:translate-y-1 active:shadow-none cursor-pointer' : 'bg-surface-muted text-ink/40 border-ink/40 cursor-not-allowed' }}"
                >
                    <span>Lanjutkan ke Konfirmasi</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @if ($showDetailModal && $detailCandidate)
        <div
            class="fixed inset-0 z-50 overflow-y-auto bg-ink/75 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="detail-modal-title"
        >
            <div
                class="bg-surface border-4 border-ink shadow-brutal-lg max-w-2xl w-full p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto"
                @click.outside="$wire.closeDetailModal()"
            >
                <div class="flex items-start justify-between pb-4 border-b-2 border-ink">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-brand text-surface border-2 border-ink flex items-center justify-center font-display font-black text-xl">
                            {{ $detailCandidate->formattedNumber() }}
                        </div>
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase text-ink/60">Detail Pasangan Calon</span>
                            <h3 id="detail-modal-title" class="text-xl font-display font-extrabold text-ink leading-tight">
                                {{ $detailCandidate->leaderMember?->eligibleVoter?->name }} &amp; {{ $detailCandidate->viceLeaderMember?->eligibleVoter?->name }}
                            </h3>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeDetailModal"
                        class="p-1.5 border-2 border-ink hover:bg-surface-muted text-ink focus:outline-none"
                        aria-label="Tutup modal"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-2">
                    <h4 class="font-display font-bold text-sm text-brand uppercase tracking-wide flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                        Visi
                    </h4>
                    <div class="p-4 bg-surface-muted border-2 border-ink text-sm text-ink leading-relaxed">
                        {{ $detailCandidate->vision ?: 'Visi belum dicantumkan oleh pasangan calon.' }}
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="font-display font-bold text-sm text-brand uppercase tracking-wide flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                        Misi
                    </h4>
                    @if ($detailCandidate->candidateMissions && $detailCandidate->candidateMissions->isNotEmpty())
                        <ol class="space-y-2 list-decimal list-inside text-sm text-ink p-4 bg-surface-muted border-2 border-ink">
                            @foreach ($detailCandidate->candidateMissions as $m)
                                <li class="leading-relaxed">{{ $m->content }}</li>
                            @endforeach
                        </ol>
                    @elseif ($detailCandidate->mission)
                        <div class="p-4 bg-surface-muted border-2 border-ink text-sm text-ink leading-relaxed whitespace-pre-line">
                            {{ $detailCandidate->mission }}
                        </div>
                    @else
                        <div class="p-4 bg-surface-muted border-2 border-ink text-sm text-ink/70">
                            Misi belum dicantumkan oleh pasangan calon.
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t-2 border-ink flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button
                        type="button"
                        wire:click="closeDetailModal"
                        class="w-full sm:w-auto px-5 py-2.5 text-xs font-display font-bold uppercase tracking-wider text-ink bg-surface border-2 border-ink hover:bg-surface-muted"
                    >
                        Tutup
                    </button>
                    <button
                        type="button"
                        wire:click="selectCandidate({{ $detailCandidate->id }}); closeDetailModal();"
                        class="w-full sm:w-auto px-6 py-2.5 text-xs font-display font-black uppercase tracking-wider text-surface bg-brand border-2 border-ink shadow-brutal-sm hover:bg-brand-dark"
                    >
                        Pilih Paslon {{ $detailCandidate->formattedNumber() }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showConfirmModal && $selectedCandidate)
        <div
            class="fixed inset-0 z-50 overflow-y-auto bg-ink/80 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-modal-title"
        >
            <div
                class="bg-surface border-4 border-ink shadow-brutal-lg max-w-lg w-full p-6 sm:p-8 space-y-6"
                @click.outside="$wire.closeConfirmModal()"
            >
                <div class="text-center pb-4 border-b-2 border-ink">
                    <span class="inline-flex items-center px-3 py-1 bg-accent/20 border border-ink text-[11px] font-mono font-bold uppercase text-brand mb-2">
                        Konfirmasi Akhir
                    </span>
                    <h3 id="confirm-modal-title" class="text-2xl font-display font-black text-ink uppercase tracking-tight">
                        Tinjau Pilihan Suara Anda
                    </h3>
                </div>

                <div class="p-4 bg-surface-muted border-2 border-ink space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-brand text-surface border-2 border-ink flex flex-col items-center justify-center font-display shrink-0">
                            <span class="text-[9px] font-bold text-accent uppercase leading-none">NO</span>
                            <span class="text-2xl font-black leading-none mt-0.5">{{ $selectedCandidate->formattedNumber() }}</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono font-bold text-ink/60 uppercase block">Pasangan Calon Terpilih</span>
                            <p class="font-display font-extrabold text-ink text-base truncate">
                                {{ $selectedCandidate->leaderMember?->eligibleVoter?->name }}
                            </p>
                            <p class="font-display font-semibold text-ink/80 text-xs truncate">
                                &amp; {{ $selectedCandidate->viceLeaderMember?->eligibleVoter?->name }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-red-100 border-2 border-ink text-red-950 space-y-2">
                    <div class="flex items-center gap-2 font-display font-black text-xs uppercase tracking-wider text-red-900">
                        <svg class="w-5 h-5 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Pernyataan Final &amp; Mengikat</span>
                    </div>
                    <p class="text-xs leading-relaxed font-medium">
                        Pilihan ini bersifat <strong>FINAL</strong> dan <strong>TIDAK DAPAT DIUBAH ATAU DIBATALKAN</strong> setelah suara dikirim.
                        Suara Anda akan langsung dimasukkan ke dalam kotak suara digital secara anonim.
                    </p>
                </div>

                <div class="pt-4 border-t-2 border-ink space-y-3">
                    <button
                        type="button"
                        wire:click="submitVote"
                        wire:loading.attr="disabled"
                        wire:target="submitVote"
                        class="w-full min-h-13 px-6 py-3.5 text-base font-display font-black uppercase tracking-wider text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="submitVote" class="flex items-center gap-2">
                            <span>KONFIRMASI &amp; KIRIM SUARA</span>
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="submitVote" class="flex items-center gap-2">
                            <svg class="animate-spin w-5 h-5 text-surface" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>MEMPROSES SUARA...</span>
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="closeConfirmModal"
                        wire:loading.attr="disabled"
                        wire:target="submitVote"
                        class="w-full text-center px-4 py-2.5 text-xs font-display font-bold uppercase tracking-wider text-ink bg-surface border-2 border-ink hover:bg-surface-muted active:translate-x-0.5 active:translate-y-0.5 transition-all"
                    >
                        Kembali &amp; Ubah Pilihan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
