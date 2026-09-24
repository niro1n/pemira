<div class="max-w-3xl mx-auto space-y-6 sm:space-y-8">
    <div class="bg-surface border-4 border-ink p-6 sm:p-8 shadow-brutal-lg text-center space-y-4">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-500 text-surface border-3 border-ink mx-auto flex items-center justify-center shadow-brutal">
            <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div>
            <span class="inline-flex items-center px-3 py-1 bg-green-100 border border-ink text-xs font-mono font-bold uppercase text-green-950 mb-2">
                Hak Suara Telah Digunakan
            </span>
            <h1 class="text-2xl sm:text-3xl font-display font-black text-ink uppercase tracking-tight">
                Suara Anda Berhasil Dicatat!
            </h1>
            <p class="text-xs sm:text-sm text-ink/75 font-medium max-w-lg mx-auto mt-2 leading-relaxed">
                Terima kasih atas partisipasi aktif Anda dalam menentukan arah kepemimpinan mahasiswa Politeknik Negeri Bali.
            </p>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink p-6 sm:p-7 shadow-brutal">
        <div class="flex items-center justify-between pb-4 border-b-2 border-ink mb-5">
            <div>
                <span class="text-[10px] font-mono font-bold text-ink/60 uppercase block">Dokumen Resmi KPR</span>
                <h2 class="text-base sm:text-lg font-display font-black text-brand uppercase tracking-wide">
                    Tanda Bukti Partisipasi Pemilih
                </h2>
            </div>
            <div class="px-2.5 py-1 bg-brand text-surface text-[10px] font-mono font-bold uppercase border border-ink">
                SAH &amp; RESMI
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-surface-muted border border-ink">
                <dt class="font-mono text-ink/60 uppercase text-[10px]">Nama Pemilih</dt>
                <dd class="font-bold text-ink text-sm mt-0.5">{{ $eligibleVoter?->name ?? $user->email }}</dd>
            </div>

            <div class="p-3 bg-surface-muted border border-ink">
                <dt class="font-mono text-ink/60 uppercase text-[10px]">Nomor Induk Mahasiswa (NIM)</dt>
                <dd class="font-mono font-bold text-ink text-sm mt-0.5">{{ $eligibleVoter?->nim ?? '-' }}</dd>
            </div>

            <div class="p-3 bg-surface-muted border border-ink">
                <dt class="font-mono text-ink/60 uppercase text-[10px]">Agenda Pemilihan</dt>
                <dd class="font-bold text-ink mt-0.5">{{ $election?->name ?? 'PEMIRA PNB' }} (Periode {{ $election?->year ?? date('Y') }})</dd>
            </div>

            <div class="p-3 bg-surface-muted border border-ink">
                <dt class="font-mono text-ink/60 uppercase text-[10px]">Waktu Pemberian Suara</dt>
                <dd class="font-mono font-bold text-ink mt-0.5">
                    {{ $participation?->voted_at?->translatedFormat('l, d F Y - H:i:s') ?? '-' }} WITA
                </dd>
            </div>
        </dl>

        <div class="mt-5 p-4 bg-surface-muted border-2 border-dashed border-ink/40 flex items-start gap-3">
            <svg class="w-5 h-5 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-xs text-ink/80 leading-relaxed">
                <strong class="text-ink font-bold">Jaminan Kerahasiaan Suara:</strong>
                Sesuai prinsip Anonymous Ballot, pilihan pasangan calon Anda tersimpan secara anonim dan terpisah dari identitas pemilih. Tanda bukti ini hanya membuktikan bahwa Anda telah menggunakan hak pilih, tanpa mengungkap kandidat yang Anda pilih.
            </div>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink p-6 sm:p-7 shadow-brutal">
        <div class="pb-4 border-b-2 border-ink mb-5">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                <h3 class="font-display font-bold text-base text-ink uppercase tracking-wide">
                    Evaluasi Pengalaman Pemilihan (Opsional)
                </h3>
            </div>
            <p class="text-xs text-ink/70 mt-1">
                Bantu KPR Politeknik Negeri Bali meningkatkan kualitas sistem e-voting pada pemilihan berikutnya.
            </p>
        </div>

        @if (session('feedback_success'))
            <div class="p-4 bg-green-100 border-2 border-ink text-green-950 text-xs font-semibold flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-green-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('feedback_success') }}</span>
            </div>
        @endif

        @if ($feedbackSubmitted)
            <div class="p-4 bg-surface-muted border-2 border-ink text-xs text-ink/80 text-center">
                <p class="font-bold text-ink text-sm">Ulasan Anda Telah Diterima</p>
                <div class="flex justify-center items-center gap-1 my-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $rating ? 'text-accent fill-accent' : 'text-ink/30' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    @endfor
                </div>
                @if ($comment)
                    <p class="italic text-ink/70 max-w-md mx-auto">&ldquo;{{ $comment }}&rdquo;</p>
                @endif
            </div>
        @else
            <form wire:submit="submitFeedback" class="space-y-4">
                <div class="p-3 bg-surface-muted border-2 border-ink text-xs text-ink/80 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="leading-relaxed">
                        <span class="font-bold text-ink uppercase tracking-wide text-[11px] block">Transparansi Identitas Masukan:</span>
                        <span>Berbeda dengan pilihan pada bilik suara yang bersifat 100% anonim, formulir ulasan ini terhubung dengan akun Anda untuk keperluan evaluasi dan peningkatan kualitas sistem oleh panitia.</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold uppercase text-ink mb-2">
                        Tingkat Kepuasan Sistem (1 - 5 Bintang)
                    </label>
                    <div class="flex items-center gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="setRating({{ $i }})"
                                class="p-2 border-2 border-ink transition-all {{ $i <= $rating ? 'bg-accent text-ink shadow-brutal-sm' : 'bg-surface-muted text-ink/40 hover:bg-surface' }}"
                                title="Beri rating {{ $i }}"
                            >
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            </button>
                        @endfor
                        <span class="ml-2 font-mono text-xs font-bold text-ink">
                            {{ $rating }} / 5
                        </span>
                    </div>
                    @error('rating')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="feedback-comment" class="block text-xs font-mono font-bold uppercase text-ink mb-1">
                        Komentar / Saran Perbaikan (Maks. 500 Karakter)
                    </label>
                    <textarea
                        id="feedback-comment"
                        wire:model="comment"
                        rows="3"
                        maxlength="500"
                        placeholder="Tuliskan pengalaman Anda atau saran untuk pemilihan selanjutnya..."
                        class="w-full p-3 bg-surface border-2 border-ink text-xs text-ink focus:outline-none focus:ring-2 focus:ring-brand font-sans"
                    ></textarea>
                    @error('comment')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button
                        type="submit"
                        class="px-5 py-2.5 text-xs font-display font-bold uppercase tracking-wider text-surface bg-brand border-2 border-ink shadow-brutal-sm hover:bg-brand-dark active:translate-x-0.5 active:translate-y-0.5 transition-all cursor-pointer"
                    >
                        Kirim Tanggapan
                    </button>
                </div>
            </form>
        @endif
    </div>

    <div class="text-center pt-2">
        <a
            href="{{ route('voter.dashboard') }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-xs sm:text-sm font-display font-black tracking-wider uppercase text-ink bg-surface border-2 border-ink shadow-brutal hover:bg-surface-muted hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Dashboard Pemilih</span>
        </a>
    </div>
</div>
