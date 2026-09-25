<div class="space-y-6 sm:space-y-8">
    <div class="bg-surface border-2 border-ink p-5 sm:p-7 shadow-brutal flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-accent/20 border border-ink text-xs font-mono font-bold uppercase mb-2">
                <span>Portal Resmi Mahasiswa</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-display font-extrabold text-ink tracking-tight">
                Halo, {{ $eligibleVoter?->name ?? $user->email }}!
            </h1>
            <p class="text-sm text-ink/75 font-medium mt-1">
                Gunakan hak suara Anda secara bijak, mandiri, jujur, dan rahasia untuk masa depan Politeknik Negeri Bali.
            </p>
        </div>

        <div class="shrink-0 flex items-center gap-3 p-3 bg-surface-muted border-2 border-ink text-sm">
            <div class="w-10 h-10 bg-brand text-surface flex items-center justify-center font-display font-bold text-lg border border-ink">
                {{ strtoupper(substr($eligibleVoter?->name ?? 'V', 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-ink leading-snug">{{ $eligibleVoter?->name ?? 'Pemilih' }}</p>
                <p class="text-xs font-mono text-ink/70">NIM: {{ $eligibleVoter?->nim ?? '-' }}</p>
                <div class="mt-1">
                    @if (! $hasVoterAccount)
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold font-mono bg-amber-200 text-amber-950 border border-ink">
                            DATA BELUM TERHUBUNG
                        </span>
                    @elseif ($isEligible)
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold font-mono bg-green-200 text-green-900 border border-ink">
                            DPT ELIGIBLE
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold font-mono bg-red-200 text-red-900 border border-ink">
                            TIDAK ELIGIBLE
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (! $election)
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal">
            <div class="w-16 h-16 mx-auto mb-4 bg-surface-muted border-2 border-ink flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-xl font-display font-bold text-ink">Tidak Ada Pemilihan Aktif</h2>
            <p class="text-sm text-ink/70 max-w-md mx-auto mt-2">
                Saat ini belum ada jadwal pemilihan raya (PEMIRA) yang sedang dibuka oleh Komisi Pemilihan Raya. Silakan kembali lagi nanti saat agenda pemilihan diumumkan.
            </p>
        </div>
    @elseif (! $hasVoterAccount)
        @php
            $humasWa = env('HUMAS_WHATSAPP', config('pemira.contacts.humas.whatsapp_number', '6281337534761'));
            $ketuaWa = env('KETUA_PANITIA_WHATSAPP', config('pemira.contacts.ketua_panitia.whatsapp_number', '628970898383'));
        @endphp
        <div class="bg-amber-50 border-2 border-ink p-6 sm:p-8 shadow-brutal">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-amber-400 text-ink border-2 border-ink flex items-center justify-center shrink-0 shadow-brutal-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-mono font-bold bg-amber-200 text-amber-950 border border-ink mb-1.5 uppercase">
                        Data Belum Terhubung
                    </span>
                    <h2 class="text-lg font-display font-bold text-ink">Akun Belum Terhubung ke DPT</h2>
                    <p class="text-sm text-ink/80 mt-1">
                        Akun Anda berhasil masuk, namun belum tertaut dengan data Daftar Pemilih Tetap (DPT) mahasiswa untuk agenda <strong>{{ $election->name }}</strong>.
                    </p>
                    <p class="text-xs font-mono text-ink/70 mt-3">
                        Silakan hubungi panitia KPR atau operator administrasi pemilu untuk verifikasi dan penautan NIM akun Anda:
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="https://wa.me/{{ $humasWa }}?text={{ rawurlencode('Halo kak Sintya (Humas PEMIRA), akun saya belum tertaut ke DPT. Mohon bantuannya untuk verifikasi data pemilih.') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                            <span>Hubungi Humas (Sintya)</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="https://wa.me/{{ $ketuaWa }}?text={{ rawurlencode('Halo kak Diana (Ketua Panitia PEMIRA), akun saya belum tertaut ke DPT. Mohon bantuannya untuk verifikasi data pemilih.') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-ink/10 text-ink border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                            <span>Ketua Panitia (Diana)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif (! $isEligible)
        @php
            $humasWa = env('HUMAS_WHATSAPP', config('pemira.contacts.humas.whatsapp_number', '6281337534761'));
            $ketuaWa = env('KETUA_PANITIA_WHATSAPP', config('pemira.contacts.ketua_panitia.whatsapp_number', '628970898383'));
        @endphp
        <div class="bg-amber-50 border-2 border-ink p-6 sm:p-8 shadow-brutal">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-red-500 text-surface border-2 border-ink flex items-center justify-center shrink-0 shadow-brutal-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-mono font-bold bg-red-200 text-red-950 border border-ink mb-1.5 uppercase">
                        Status Hak Pilih
                    </span>
                    <h2 class="text-lg font-display font-bold text-ink">Status DPT: Tidak Memenuhi Syarat (Non-Eligible)</h2>
                    <p class="text-sm text-ink/80 mt-1">
                        Berdasarkan penetapan Daftar Pemilih Tetap (DPT) KPR, akun Anda saat ini tidak tercatat sebagai pemilih yang berhak memberikan suara pada <strong>{{ $election->name }}</strong>.
                    </p>
                    <p class="text-xs font-mono text-ink/60 mt-3">
                        Jika Anda merasa ini adalah kekeliruan data, segera hubungi sekretariat panitia KPR dengan membawa KTM / bukti mahasiswa aktif:
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="https://wa.me/{{ $humasWa }}?text={{ rawurlencode('Halo kak Sintya (Humas PEMIRA), status DPT saya non-eligible. Saya ingin konfirmasi kelayakan pemilih.') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                            <span>Hubungi Humas (Sintya)</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="https://wa.me/{{ $ketuaWa }}?text={{ rawurlencode('Halo kak Diana (Ketua Panitia PEMIRA), status DPT saya non-eligible. Saya ingin konfirmasi kelayakan pemilih.') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-ink/10 text-ink border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                            <span>Ketua Panitia (Diana)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-surface border-2 border-ink p-6 sm:p-8 shadow-brutal flex flex-col justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 text-xs font-mono font-bold bg-brand text-surface border border-ink">
                            {{ $election->statusLabel() }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-mono font-bold bg-surface-muted border border-ink">
                            PERIODE {{ $election->year }}
                        </span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-display font-extrabold text-brand tracking-tight">
                        {{ $election->name }}
                    </h2>

                    <p class="text-sm font-medium text-ink/80 mt-2">
                        {{ $election->contextualDateLabel() }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                        <div class="p-3 bg-surface-muted border border-ink">
                            <span class="text-[10px] font-mono font-bold uppercase text-ink/70 block">Masa Pemungutan Suara</span>
                            <span class="text-xs font-bold text-ink">
                                {{ $election->voting_start_at?->translatedFormat('d M Y, H:i') }} - {{ $election->voting_end_at?->translatedFormat('d M Y, H:i') }} WITA
                            </span>
                        </div>
                        <div class="p-3 bg-surface-muted border border-ink">
                            <span class="text-[10px] font-mono font-bold uppercase text-ink/70 block">Kandidat Terdaftar</span>
                            <span class="text-xs font-bold text-ink">
                                {{ $activeCandidatesCount }} Pasangan Calon Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t-2 border-ink">
                    @if ($currentPhase === \App\Enums\ElectionPhase::VOTING)
                        @if ($hasVoted)
                            <div class="bg-green-50 border-2 border-ink p-4 sm:p-5 shadow-brutal-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-500 text-surface border-2 border-ink flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-display font-bold text-green-950 text-base">Hak Suara Anda Telah Digunakan</p>
                                        <p class="text-xs text-green-900/80">
                                            Dicatat pada {{ $votingParticipation?->voted_at?->translatedFormat('d F Y, H:i') ?? '-' }} WITA
                                        </p>
                                    </div>
                                </div>
                                <a
                                    href="{{ route('voter.success') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-xs font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2"
                                >
                                    Bukti Partisipasi &rarr;
                                </a>
                            </div>
                        @else
                            @if ($activeCandidatesCount === 0)
                                <div class="p-4 bg-amber-100 border-2 border-ink text-sm font-medium text-amber-900">
                                    Tidak ada pasangan calon aktif yang siap dipilih saat ini. Silakan pantau pengumuman resmi panitia.
                                </div>
                            @else
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-accent/15 border-2 border-ink p-4 sm:p-5">
                                    <div>
                                        <p class="font-display font-black text-ink text-lg uppercase tracking-tight">
                                            Bilik Suara Sedang Dibuka!
                                        </p>
                                        <p class="text-xs text-ink/80 mt-0.5">
                                            Anda memiliki 1 hak suara sah untuk menentukan pemimpin mahasiswa.
                                        </p>
                                    </div>
                                    <a
                                        href="{{ route('voter.voting') }}"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm sm:text-base font-display font-black uppercase tracking-wider text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm active:translate-x-1 active:translate-y-1 active:shadow-none transition-all text-center min-h-12 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2"
                                    >
                                        <span>Masuk Bilik Suara</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        @endif
                    @elseif ($currentPhase === \App\Enums\ElectionPhase::REGISTRATION || $currentPhase === \App\Enums\ElectionPhase::UPCOMING)
                        <div class="p-4 bg-surface-muted border-2 border-ink">
                            <p class="font-display font-bold text-ink text-sm uppercase">Bilik Suara Belum Dibuka</p>
                            <p class="text-xs text-ink/70 mt-1">
                                Pemungutan suara baru dapat dilakukan mulai <strong>{{ $election->voting_start_at?->translatedFormat('d F Y, H:i') }} WITA</strong>.
                                Pastikan Anda telah memeriksa visi dan misi masing-masing pasangan calon.
                            </p>
                        </div>
                    @elseif ($currentPhase === \App\Enums\ElectionPhase::FINISHED)
                        <div class="p-4 bg-surface-muted border-2 border-ink flex items-center justify-between gap-4">
                            <div>
                                <p class="font-display font-bold text-ink text-sm uppercase">Pemilihan Telah Selesai</p>
                                <p class="text-xs text-ink/70 mt-0.5">
                                    @if ($hasVoted)
                                        Terima kasih atas partisipasi Anda dalam menyukseskan pesta demokrasi kampus.
                                    @else
                                        Masa pemungutan suara telah resmi ditutup pada {{ $election->voting_end_at?->translatedFormat('d F Y, H:i') }} WITA.
                                    @endif
                                </p>
                            </div>
                            @if ($hasVoted)
                                <a
                                    href="{{ route('voter.success') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2"
                                >
                                    Bukti Partisipasi
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-6 shadow-brutal flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b-2 border-ink mb-4">
                        <h3 class="font-display font-bold text-base text-ink uppercase tracking-wide">Data Pemilih (DPT)</h3>
                        <span class="text-[10px] font-mono font-bold bg-green-200 text-green-950 px-2 py-0.5 border border-ink">RESMI</span>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="font-mono text-ink/60 uppercase text-[10px]">Nama Lengkap</dt>
                            <dd class="font-bold text-ink text-sm">{{ $eligibleVoter?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-mono text-ink/60 uppercase text-[10px]">NIM</dt>
                            <dd class="font-mono font-bold text-ink">{{ $eligibleVoter?->nim ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-mono text-ink/60 uppercase text-[10px]">Jurusan</dt>
                            <dd class="font-bold text-ink">{{ $studyProgram?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-mono text-ink/60 uppercase text-[10px]">Tanggal Lahir</dt>
                            <dd class="font-medium text-ink">{{ $eligibleVoter?->date_of_birth?->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-mono text-ink/60 uppercase text-[10px]">Email Akun</dt>
                            <dd class="font-mono text-ink truncate">{{ $user->email }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="pt-4 border-t border-ink/20 mt-4">
                    <a
                        href="{{ route('voter.profile') }}"
                        class="text-xs font-display font-bold text-brand hover:underline inline-flex items-center gap-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-1"
                    >
                        <span>Lihat Profil Lengkap</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-surface border-2 border-ink p-6 sm:p-7 shadow-brutal">
        <h3 class="font-display font-bold text-lg text-ink uppercase tracking-wide mb-5 flex items-center gap-2">
            <span class="w-3 h-3 bg-accent inline-block border border-ink"></span>
            Panduan Memberikan Hak Suara
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <div class="p-4 bg-surface-muted border-2 border-ink relative">
                <span class="absolute -top-3 left-4 px-2 py-0.5 bg-brand text-surface text-xs font-mono font-bold border border-ink">
                    LANGKAH 01
                </span>
                <h4 class="font-display font-bold text-base text-ink mt-2">Pilih Pasangan Calon</h4>
                <p class="text-xs text-ink/75 mt-1 leading-relaxed">
                    Masuk ke bilik suara, pelajari visi, misi, dan profil masing-masing kandidat, lalu klik kartu pasangan calon pilihan Anda.
                </p>
            </div>

            <div class="p-4 bg-surface-muted border-2 border-ink relative">
                <span class="absolute -top-3 left-4 px-2 py-0.5 bg-brand text-surface text-xs font-mono font-bold border border-ink">
                    LANGKAH 02
                </span>
                <h4 class="font-display font-bold text-base text-ink mt-2">Konfirmasi Pilihan</h4>
                <p class="text-xs text-ink/75 mt-1 leading-relaxed">
                    Periksa kembali ringkasan paslon terpilih pada jendela konfirmasi. Pastikan pilihan Anda sudah mantap sebelum mengirim.
                </p>
            </div>

            <div class="p-4 bg-surface-muted border-2 border-ink relative">
                <span class="absolute -top-3 left-4 px-2 py-0.5 bg-brand text-surface text-xs font-mono font-bold border border-ink">
                    LANGKAH 03
                </span>
                <h4 class="font-display font-bold text-base text-ink mt-2">Pencatatan Anonim</h4>
                <p class="text-xs text-ink/75 mt-1 leading-relaxed">
                    Suara Anda langsung tersimpan secara anonim dan terpisah dari identitas akun. Dapatkan bukti tanda telah memilih.
                </p>
            </div>
        </div>
    </div>
</div>
