<div class="max-w-4xl mx-auto space-y-6 sm:space-y-8">
    <div class="bg-surface border-2 border-ink p-6 sm:p-7 shadow-brutal flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-brand text-surface text-[10px] font-mono font-bold uppercase mb-2">
                <span>Daftar Pemilih Tetap (DPT)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-display font-black text-ink uppercase tracking-tight">
                Profil Identitas Pemilih
            </h1>
            <p class="text-xs sm:text-sm text-ink/75 font-medium mt-1">
                Data resmi mahasiswa yang terdaftar dalam sistem pemilihan raya kampus.
            </p>
        </div>

        <div class="shrink-0">
            @if ($eligibleVoter?->is_eligible)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-200 text-green-950 border-2 border-ink text-xs font-mono font-bold uppercase shadow-brutal-sm">
                    <svg class="w-4 h-4 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>ELIGIBLE / HAK PILIH AKTIF</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-200 text-red-950 border-2 border-ink text-xs font-mono font-bold uppercase shadow-brutal-sm">
                    <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>TIDAK ELIGIBLE</span>
                </span>
            @endif
        </div>
    </div>

    <div class="bg-surface border-2 border-ink p-6 sm:p-8 shadow-brutal space-y-6">
        <div class="flex items-center justify-between pb-4 border-b-2 border-ink">
            <h2 class="font-display font-extrabold text-base sm:text-lg text-ink uppercase tracking-wide flex items-center gap-2">
                <span class="w-3 h-3 bg-brand inline-block border border-ink"></span>
                Informasi Biodata Mahasiswa
            </h2>
            <span class="text-[10px] font-mono font-bold text-ink/60 uppercase">Data Terkunci (Read-Only)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="p-3.5 bg-surface-muted border border-ink">
                <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Nama Lengkap</span>
                <span class="text-sm sm:text-base font-bold text-ink mt-0.5 block">{{ $eligibleVoter?->name ?? '-' }}</span>
            </div>

            <div class="p-3.5 bg-surface-muted border border-ink">
                <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Nomor Induk Mahasiswa (NIM)</span>
                <span class="text-sm sm:text-base font-mono font-bold text-ink mt-0.5 block">{{ $eligibleVoter?->nim ?? '-' }}</span>
            </div>

            <div class="p-3.5 bg-surface-muted border border-ink">
                <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Program Studi / Jurusan</span>
                <span class="text-sm sm:text-base font-bold text-ink mt-0.5 block">{{ $studyProgram?->name ?? '-' }}</span>
            </div>

            <div class="p-3.5 bg-surface-muted border border-ink">
                <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Tanggal Lahir</span>
                <span class="text-sm sm:text-base font-medium text-ink mt-0.5 block">
                    {{ $eligibleVoter?->date_of_birth ? $eligibleVoter->date_of_birth->translatedFormat('d F Y') : '-' }}
                </span>
            </div>

            <div class="p-3.5 bg-surface-muted border border-ink">
                <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Alamat Email Akun</span>
                <span class="text-sm sm:text-base font-mono text-ink mt-0.5 block truncate">{{ $user->email }}</span>
            </div>

            <div class="p-3.5 bg-surface-muted border border-ink">
                <span class="text-[10px] font-mono font-bold uppercase text-ink/60 block">Status Verifikasi Akun</span>
                <span class="text-sm sm:text-base font-medium text-ink mt-0.5 block">
                    {{ $user->email_verified_at ? 'Terverifikasi pada ' . $user->email_verified_at->translatedFormat('d/m/Y H:i') : 'Belum Diverifikasi' }}
                </span>
            </div>
        </div>

        <div class="p-4 bg-surface-muted border-2 border-dashed border-ink/40 flex items-start gap-3">
            <svg class="w-5 h-5 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs text-ink/75 leading-relaxed">
                Data identitas di atas bersumber dari Surat Keputusan penetapan DPT oleh Komisi Pemilihan Raya.
                Demi integritas pemilu, data ini tidak dapat disunting secara mandiri.
                Jika terdapat kekeliruan data, silakan berkoordinasi langsung dengan panitia KPR dengan melampirkan KTM aktif.
            </p>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink p-6 sm:p-7 shadow-brutal space-y-4">
        <div class="flex items-center justify-between pb-3 border-b-2 border-ink">
            <h2 class="font-display font-extrabold text-base sm:text-lg text-ink uppercase tracking-wide flex items-center gap-2">
                <span class="w-3 h-3 bg-accent inline-block border border-ink"></span>
                Riwayat Partisipasi Pemilihan
            </h2>
            <span class="text-xs font-mono font-bold text-ink/70">{{ $participations->count() }} Kegiatan</span>
        </div>

        @if ($participations->isEmpty())
            <div class="p-6 bg-surface-muted border border-ink text-center text-xs text-ink/70">
                Belum ada riwayat partisipasi pemberian suara yang tercatat pada akun ini.
            </div>
        @else
            <div class="divide-y-2 divide-ink border-2 border-ink">
                @foreach ($participations as $p)
                    <div class="p-4 bg-surface flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase text-brand block">
                                Periode {{ $p->election?->year ?? '-' }}
                            </span>
                            <h3 class="font-display font-bold text-sm text-ink">
                                {{ $p->election?->name ?? 'Pemilihan Raya' }}
                            </h3>
                            <p class="text-xs text-ink/70 font-mono mt-0.5">
                                Dicatat: {{ $p->voted_at?->translatedFormat('d F Y, H:i:s') ?? '-' }} WITA
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-mono font-bold bg-green-200 text-green-950 border border-ink self-start sm:self-auto">
                            SUARA TERCATAT
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
