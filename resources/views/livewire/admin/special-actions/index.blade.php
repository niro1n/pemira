<div class="space-y-6 sm:space-y-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>KONTROL SISTEM UTAMA — KHUSUS SUPER ADMIN</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                TINDAKAN KHUSUS
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                Kelola pemecahan kendala pendaftaran mahasiswa (OTP), aktivasi akun pemilih manual, serta kontrol status pemeliharaan sistem.
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-3 sm:p-4 bg-accent/20 border-2 border-ink shadow-brutal flex items-center justify-between gap-3 text-xs sm:text-sm font-sans font-bold text-ink min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 bg-brand shrink-0 inline-block"></span>
                <span class="wrap-break-word">{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="p-1 hover:bg-ink/10 text-ink shrink-0 cursor-pointer" aria-label="Tutup pesan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 sm:p-4 bg-rose-100 border-2 border-ink shadow-brutal flex items-center justify-between gap-3 text-xs sm:text-sm font-sans font-bold text-rose-950 min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 bg-rose-600 shrink-0 inline-block"></span>
                <span class="wrap-break-word">{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="p-1 hover:bg-ink/10 text-rose-950 shrink-0 cursor-pointer" aria-label="Tutup pesan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-ink pb-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-accent inline-block border border-ink"></span>
                <h3 class="font-display font-black text-base sm:text-lg uppercase tracking-wider text-ink">
                    1. Bypass & Generator OTP Pendaftaran Manual
                </h3>
            </div>
            <span class="text-xs font-sans text-ink/70">Solusi ketika email kampus/student tidak menerima OTP otomatis</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-5 bg-surface border-2 border-ink shadow-brutal p-5 sm:p-6 space-y-5">
                <div class="border-b-2 border-ink pb-3">
                    <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60 block">BANTUAN HELPDESK WA</span>
                    <h4 class="font-display font-black text-base uppercase text-brand">
                        Buat Kode OTP Baru
                    </h4>
                    <p class="text-xs font-sans text-ink/70 mt-1">
                        Generate 6 digit kode OTP valid untuk email yang sedang stuck di halaman verifikasi.
                    </p>
                </div>

                <form wire:submit.prevent="generateManualOtp" class="space-y-4">
                    <div>
                        <label for="otpEmail" class="block text-xs font-display font-bold uppercase text-ink mb-1">
                            Alamat Email Mahasiswa <span class="text-rose-600">*</span>
                        </label>
                        <input type="email"
                               id="otpEmail"
                               wire:model="otpEmail"
                               placeholder="contoh: irma@student.pnb.ac.id"
                               class="w-full px-3 py-2 border-2 border-ink bg-surface font-sans text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand shadow-brutal-sm">
                        @error('otpEmail')
                            <p class="text-xs font-sans text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="manualOtpCode" class="block text-xs font-display font-bold uppercase text-ink mb-1">
                            Kode OTP Kustom (Opsional, 6 Digit)
                        </label>
                        <input type="text"
                               id="manualOtpCode"
                               wire:model="manualOtpCode"
                               maxlength="6"
                               placeholder="Biarkan kosong untuk kode acak"
                               class="w-full px-3 py-2 border-2 border-ink bg-surface font-mono text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand shadow-brutal-sm tracking-widest">
                        <span class="text-[11px] font-sans text-ink/60 mt-0.5 block">Jika dikosongkan, sistem akan mengacak 6 digit angka secara aman.</span>
                        @error('manualOtpCode')
                            <p class="text-xs font-sans text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-3 bg-amber-50 border border-ink text-xs font-sans text-ink/80 space-y-1">
                        <span class="font-bold text-amber-950 block">Alur Penggunaan:</span>
                        <ol class="list-decimal list-inside space-y-0.5 text-[11px]">
                            <li>Minta mahasiswa tetap membuka layar input OTP di HP-nya.</li>
                            <li>Klik tombol di bawah untuk membuat kode valid (aktif 60 menit).</li>
                            <li>Kirimkan 6 digit angka kepada mahasiswa via WhatsApp.</li>
                        </ol>
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-2.5 bg-brand hover:bg-brand-dark text-accent border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer text-center">
                        <span wire:loading.remove wire:target="generateManualOtp">
                            GENERATE KODE OTP SEKARANG
                        </span>
                        <span wire:loading wire:target="generateManualOtp">
                            MEMPROSES GENERASI OTP...
                        </span>
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 bg-surface border-2 border-ink shadow-brutal p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b-2 border-ink pb-3">
                    <div>
                        <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60 block">LOG AKTIVITAS REGISTRASI</span>
                        <h4 class="font-display font-black text-base uppercase text-brand">
                            Daftar Permintaan OTP Terkini
                        </h4>
                    </div>
                    <span class="text-xs font-sans text-ink/60">10 Data Terakhir</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-100 font-sans text-xs">
                        <thead>
                            <tr class="border-b-2 border-ink bg-surface-muted">
                                <th class="p-2.5 font-display font-black uppercase text-ink">EMAIL MAHASISWA</th>
                                <th class="p-2.5 font-display font-black uppercase text-ink">STATUS</th>
                                <th class="p-2.5 font-display font-black uppercase text-ink">WAKTU</th>
                                <th class="p-2.5 font-display font-black uppercase text-ink text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink/20">
                            @forelse ($recentOtps as $otp)
                                <tr class="hover:bg-ink/5">
                                    <td class="p-2.5 font-bold text-ink">
                                        {{ $otp->email }}
                                    </td>
                                    <td class="p-2.5 whitespace-nowrap">
                                        @if ($otp->isUsed())
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-900 border border-ink font-display font-bold text-[10px] uppercase">
                                                Sudah Digunakan
                                            </span>
                                        @elseif ($otp->isExpired())
                                            <span class="inline-flex items-center px-2 py-0.5 bg-rose-100 text-rose-900 border border-ink font-display font-bold text-[10px] uppercase">
                                                Kedaluwarsa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-amber-900 border border-ink font-display font-bold text-[10px] uppercase">
                                                Menunggu Verifikasi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-2.5 whitespace-nowrap text-ink/60 text-[11px]">
                                        {{ $otp->updated_at->timezone('Asia/Makassar')->translatedFormat('H:i') }} WITA
                                    </td>
                                    <td class="p-2.5 whitespace-nowrap text-right">
                                        <button wire:click="quickGenerateOtp('{{ $otp->email }}')"
                                                wire:loading.attr="disabled"
                                                type="button"
                                                class="px-2.5 py-1 bg-surface hover:bg-accent border border-ink shadow-brutal-sm font-display font-bold text-[11px] uppercase transition-colors cursor-pointer"
                                                title="Buatkan OTP baru untuk email ini">
                                            Buat OTP Baru
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-ink/60 italic">
                                        Belum ada riwayat permintaan OTP yang tercatat di sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-ink pb-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-brand inline-block border border-ink"></span>
                <h3 class="font-display font-black text-base sm:text-lg uppercase tracking-wider text-ink">
                    2. Aktivasi Akun Pemilih Manual (Direct Account Creation)
                </h3>
            </div>
            <span class="text-xs font-sans text-ink/70">Bypass total form pendaftaran untuk mahasiswa di DPT</span>
        </div>

        <div class="bg-surface border-2 border-ink shadow-brutal p-5 sm:p-6 space-y-4">
            <div class="max-w-2xl">
                <label for="voterSearch" class="block text-xs font-display font-bold uppercase text-ink mb-1">
                    Cari Mahasiswa di DPT (Ketik NIM atau Nama)
                </label>
                <div class="relative">
                    <input type="text"
                           id="voterSearch"
                           wire:model.live.debounce.300ms="voterSearch"
                           placeholder="Ketik minimal 2 karakter NIM atau nama mahasiswa..."
                           class="w-full px-3.5 py-2.5 border-2 border-ink bg-surface font-sans text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand shadow-brutal-sm">
                </div>
                @error('voterSearch')
                    <p class="text-xs font-sans text-rose-600 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if ($searchedVoters->isNotEmpty())
                <div class="border-2 border-ink bg-surface-muted overflow-hidden">
                    <div class="px-4 py-2 border-b-2 border-ink bg-ink/5 text-xs font-display font-bold uppercase text-ink">
                        Hasil Pencarian Mahasiswa DPT ({{ $searchedVoters->count() }})
                    </div>
                    <div class="divide-y-2 divide-ink">
                        @foreach ($searchedVoters as $v)
                            <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface hover:bg-accent/10 transition-colors">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono font-bold text-xs bg-ink text-surface px-2 py-0.5">{{ $v->nim }}</span>
                                        <h5 class="font-display font-black text-sm uppercase text-ink">{{ $v->name }}</h5>
                                        @if ($v->voterAccount !== null)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-ink text-[10px] font-bold uppercase">
                                                Sudah Punya Akun ({{ $v->voterAccount->user?->email }})
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-ink text-[10px] font-bold uppercase">
                                                Belum Punya Akun
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs font-sans text-ink/70">
                                        <span>Jurusan: <strong>{{ $v->studyProgram?->name ?? 'Belum terpetakan' }}</strong></span>
                                        <span class="mx-1.5">•</span>
                                        <span>Tgl Lahir: <strong>{{ $v->date_of_birth?->format('d/m/Y') ?? '-' }}</strong></span>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    @if ($v->voterAccount === null)
                                        <button wire:click="selectVoterForActivation({{ $v->id }})"
                                                type="button"
                                                class="w-full sm:w-auto px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                                            Aktivasi Akun Sekarang
                                        </button>
                                    @else
                                        <button disabled
                                                type="button"
                                                class="w-full sm:w-auto px-3 py-1.5 bg-surface-muted text-ink/40 border border-ink/40 font-display font-bold text-xs uppercase cursor-not-allowed">
                                            Akun Sudah Aktif
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif (strlen(trim($voterSearch)) >= 2)
                <div class="p-4 bg-amber-50 border-2 border-ink text-xs font-sans text-amber-950">
                    Tidak ditemukan mahasiswa di DPT dengan kata kunci <strong>"{{ $voterSearch }}"</strong>. Pastikan NIM atau nama sudah terdaftar dalam DPT.
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-4 pt-2">
        <div class="flex items-center gap-2 border-b-2 border-ink pb-2">
            <span class="w-3 h-3 bg-brand inline-block"></span>
            <h3 class="font-display font-black text-base sm:text-lg uppercase tracking-wider text-ink">
                3. Status Sistem & Maintenance Mode
            </h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="lg:col-span-2 bg-surface border-2 border-ink shadow-brutal p-5 sm:p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-ink pb-4">
                        <div>
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">FITUR PEMELIHARAAN</span>
                            <h4 class="font-display font-black text-lg sm:text-xl text-brand uppercase">
                                Maintenance Mode
                            </h4>
                        </div>

                        <div>
                            @if ($isMaintenanceMode)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-950 border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider">
                                    <span class="w-2 h-2 bg-amber-500 inline-block border border-ink"></span>
                                    AKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-950 border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider">
                                    <span class="w-2 h-2 bg-emerald-600 inline-block border border-ink"></span>
                                    NONAKTIF
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm font-sans text-ink/70 leading-relaxed">
                        Aktifkan untuk sementara membatasi akses website selama proses pemeliharaan.
                    </p>

                    <div class="p-4 border-2 border-ink {{ $isMaintenanceMode ? 'bg-amber-50' : 'bg-surface-muted' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-none border-2 border-ink flex items-center justify-center shrink-0 mt-0.5 {{ $isMaintenanceMode ? 'bg-amber-400 text-ink' : 'bg-surface text-ink' }}">
                                @if ($isMaintenanceMode)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="space-y-0.5">
                                <h5 class="font-display font-black text-sm uppercase text-ink">
                                    {{ $isMaintenanceMode ? 'Website Maintenance' : 'Website Normal' }}
                                </h5>
                                <p class="text-xs font-sans text-ink/70">
                                    {{ $isMaintenanceMode
                                        ? 'Website sedang dalam mode pemeliharaan. VOTER dan ADMIN diarahkan ke halaman pemeliharaan.'
                                        : 'Website dapat diakses seperti biasa oleh seluruh pengguna sesuai permission.' }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-wrap sm:flex-nowrap items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                            <button wire:click="requestToggle({{ $isMaintenanceMode ? 'false' : 'true' }})"
                                    wire:loading.attr="disabled"
                                    type="button"
                                    class="relative inline-flex items-center h-8 w-16 border-2 border-ink shadow-brutal-sm cursor-pointer transition-colors focus:outline-none {{ $isMaintenanceMode ? 'bg-brand' : 'bg-surface' }}"
                                    role="switch"
                                    aria-checked="{{ $isMaintenanceMode ? 'true' : 'false' }}"
                                    aria-label="Toggle Maintenance Mode">
                                <span class="sr-only">Toggle Maintenance Mode</span>
                                <span class="inline-block w-6 h-6 border-2 border-ink transform transition-transform {{ $isMaintenanceMode ? 'translate-x-8 bg-accent' : 'translate-x-0.5 bg-ink/20' }}"></span>
                            </button>

                            <button wire:click="requestToggle({{ $isMaintenanceMode ? 'false' : 'true' }})"
                                    wire:loading.attr="disabled"
                                    type="button"
                                    class="px-3.5 py-1.5 border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all cursor-pointer {{ $isMaintenanceMode ? 'bg-surface hover:bg-rose-50 text-rose-800' : 'bg-brand text-accent hover:bg-brand-dark' }}">
                                <span wire:loading.remove wire:target="requestToggle">
                                    {{ $isMaintenanceMode ? 'NONAKTIFKAN' : 'AKTIFKAN' }}
                                </span>
                                <span wire:loading wire:target="requestToggle">
                                    MEMPROSES...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 border-2 border-ink {{ $isRealtimeResultsEnabled ? 'bg-emerald-50' : 'bg-surface-muted' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-none border-2 border-ink flex items-center justify-center shrink-0 mt-0.5 {{ $isRealtimeResultsEnabled ? 'bg-emerald-400 text-ink' : 'bg-surface text-ink' }}">
                                <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="space-y-0.5">
                                <h5 class="font-display font-black text-sm uppercase text-ink">
                                    Quick Count / Hasil Real-time Pemilih
                                </h5>
                                <p class="text-xs font-sans text-ink/70">
                                    {{ $isRealtimeResultsEnabled
                                        ? 'Perolehan suara diperbarui secara live dan dapat dilihat oleh pemilih di portal dashboard mahasiswa.'
                                        : 'Tampilan hasil perhitungan sementara disembunyikan dari dashboard pemilih.' }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-3 w-full sm:w-auto justify-end">
                            <button wire:click="toggleRealtimeResults({{ $isRealtimeResultsEnabled ? 'false' : 'true' }})"
                                    wire:loading.attr="disabled"
                                    type="button"
                                    class="px-3.5 py-1.5 border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all cursor-pointer {{ $isRealtimeResultsEnabled ? 'bg-surface hover:bg-rose-50 text-rose-800' : 'bg-brand text-accent hover:bg-brand-dark' }}">
                                <span wire:loading.remove wire:target="toggleRealtimeResults">
                                    {{ $isRealtimeResultsEnabled ? 'NONAKTIFKAN' : 'AKTIFKAN' }}
                                </span>
                                <span wire:loading wire:target="toggleRealtimeResults">
                                    MEMPROSES...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-ink/15 flex items-center justify-between text-xs font-sans text-ink/60">
                    <span>Proteksi Super Admin:</span>
                    <span class="font-bold text-ink">Super Admin tetap memiliki akses dashboard</span>
                </div>
            </div>

            <div class="bg-surface border-2 border-ink shadow-brutal p-5 sm:p-6 space-y-4">
                <div class="border-b-2 border-ink pb-3">
                    <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">INFORMASI TEKNIS</span>
                    <h4 class="font-display font-black text-sm uppercase text-brand">
                        Lingkungan Operasional
                    </h4>
                </div>

                <div class="space-y-3 font-sans text-xs">
                    <div class="flex items-center justify-between p-2 bg-surface-muted border border-ink">
                        <span class="text-ink/70">Waktu Server:</span>
                        <span class="font-bold text-ink font-mono">{{ now()->timezone('Asia/Makassar')->translatedFormat('H:i') }} WITA</span>
                    </div>

                    <div class="flex items-center justify-between p-2 bg-surface-muted border border-ink">
                        <span class="text-ink/70">Lingkungan:</span>
                        <span class="font-bold text-ink uppercase">{{ app()->environment() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2 bg-surface-muted border border-ink">
                        <span class="text-ink/70">Akses Super Admin:</span>
                        <span class="font-bold text-emerald-800">Tidak Terpengaruh</span>
                    </div>

                    <div class="p-3 bg-surface-muted border border-ink space-y-1">
                        <span class="text-[10px] font-display font-bold uppercase text-ink/60 block">Perubahan Terakhir:</span>
                        @if ($lastLog)
                            <div class="font-bold text-ink text-xs">
                                {{ $lastLog->action === 'maintenance_mode_enabled' ? 'Mode Diaktifkan' : 'Mode Dinonaktifkan' }}
                            </div>
                            <div class="text-[11px] text-ink/60">
                                Oleh: {{ $lastLog->user?->email ?? 'Sistem' }}
                            </div>
                            <div class="text-[11px] text-ink/50">
                                {{ $lastLog->created_at->translatedFormat('d M Y, H:i') }} WITA
                            </div>
                        @else
                            <span class="text-[11px] text-ink/60 block">Belum ada riwayat tercatat</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-brand inline-block"></span>
                <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-wider text-ink">
                    Riwayat Pemeliharaan Sistem
                </h3>
            </div>
            <span class="text-xs font-sans text-ink/60">5 Log Terakhir</span>
        </div>

        <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden">
            @if ($recentLogs->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-125 font-sans">
                        <thead>
                            <tr class="border-b-2 border-ink bg-surface-muted">
                                <th class="p-3 text-xs font-display font-black uppercase text-ink">STATUS</th>
                                <th class="p-3 text-xs font-display font-black uppercase text-ink">AKTOR</th>
                                <th class="p-3 text-xs font-display font-black uppercase text-ink">DESKRIPSI</th>
                                <th class="p-3 text-xs font-display font-black uppercase text-ink text-right">WAKTU</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-ink">
                            @foreach ($recentLogs as $log)
                                <tr class="hover:bg-ink/5 text-xs">
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($log->action === 'maintenance_mode_enabled')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-amber-100 text-amber-950 border border-ink font-display font-bold text-[11px] uppercase">
                                                <span class="w-1.5 h-1.5 bg-amber-600 inline-block"></span>
                                                AKTIF
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-ink font-display font-bold text-[11px] uppercase">
                                                <span class="w-1.5 h-1.5 bg-emerald-600 inline-block"></span>
                                                NONAKTIF
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 whitespace-nowrap font-bold text-ink">
                                        {{ $log->user?->email ?? 'Sistem' }}
                                    </td>
                                    <td class="p-3 text-ink/80">
                                        {{ $log->description }}
                                    </td>
                                    <td class="p-3 whitespace-nowrap text-right text-ink/60">
                                        {{ $log->created_at->translatedFormat('d M Y, H:i') }} WITA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-xs font-sans text-ink/60">
                    Belum ada riwayat perubahan status pemeliharaan dalam audit log.
                </div>
            @endif
        </div>
    </div>

    @if ($showOtpSuccessModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-ink/75 backdrop-blur-xs"
             role="dialog"
             aria-modal="true">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0"
                 x-data="{ copied: false, waCopied: false }">
                <div class="px-5 py-4 border-b-2 border-ink flex items-center justify-between bg-accent/20 shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-brand block">KODE OTP BERHASIL DIBUAT</span>
                        <h3 class="font-display font-black text-lg text-ink uppercase">
                            KODE VERIFIKASI MAHASISWA
                        </h3>
                    </div>
                    <button wire:click="closeOtpSuccessModal"
                            type="button"
                            class="w-9 h-9 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 sm:p-6 space-y-4 font-sans text-xs">
                    <p class="text-ink/80 leading-relaxed">
                        Kode OTP telah aktif di database sistem untuk email: <strong class="text-ink">{{ $generatedOtpEmail }}</strong>. Kode ini berlaku selama 60 menit.
                    </p>

                    <div class="p-4 bg-accent/30 border-2 border-ink text-center space-y-1">
                        <span class="text-[10px] font-display font-bold uppercase tracking-widest text-ink/60 block">KODE OTP RESMI</span>
                        <div class="text-3xl sm:text-4xl font-mono font-black text-brand tracking-[0.25em]">
                            {{ $generatedOtpResult }}
                        </div>
                        <span class="text-[11px] text-ink/60 block">Berlaku sampai: {{ $generatedOtpExpiresAt }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-2">
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $generatedOtpResult }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="flex-1 px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-black text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                            <span x-show="!copied">SALIN ANGKA KODE ({{ $generatedOtpResult }})</span>
                            <span x-show="copied" class="text-emerald-700">✓ KODE DISALIN!</span>
                        </button>

                        @php
                            $waText = "Halo kak, berikut kode verifikasi OTP akun PEMIRA 2026 kamu:\n\n*{$generatedOtpResult}*\n\nSilakan masukkan kode ini di layar verifikasi. Kode berlaku selama 60 menit.";
                        @endphp
                        <button type="button"
                                @click="navigator.clipboard.writeText(`{{ $waText }}`); waCopied = true; setTimeout(() => waCopied = false, 2500)"
                                class="flex-1 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider text-center transition-all cursor-pointer">
                            <span x-show="!waCopied">SALIN FORMAT WA</span>
                            <span x-show="waCopied" class="text-accent">✓ FORMAT WA DISALIN!</span>
                        </button>
                    </div>
                </div>

                <div class="px-5 py-3 border-t-2 border-ink bg-surface-muted text-right">
                    <button wire:click="closeOtpSuccessModal"
                            type="button"
                            class="px-5 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase cursor-pointer">
                        SELESAI / TUTUP
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showActivationModal && $selectedVoter)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-ink/75 backdrop-blur-xs"
             role="dialog"
             aria-modal="true">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0">
                <div class="px-5 py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">SUPER ADMIN BYPASS</span>
                        <h3 class="font-display font-black text-lg text-brand uppercase truncate min-w-0">
                            Aktivasi Akun Pemilih Manual
                        </h3>
                    </div>
                    <button wire:click="cancelActivation"
                            type="button"
                            class="w-9 h-9 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="activateVoterAccount" class="p-5 sm:p-6 space-y-4 font-sans text-xs">
                    <div class="bg-surface-muted border-2 border-ink p-3 space-y-1">
                        <div class="text-[10px] font-display font-bold uppercase text-ink/60">Data Mahasiswa Terpilih di DPT:</div>
                        <div class="font-mono font-bold text-brand text-xs">NIM: {{ $selectedVoter->nim }}</div>
                        <div class="font-display font-black text-sm uppercase text-ink">{{ $selectedVoter->name }}</div>
                        <div class="text-ink/70">Jurusan: {{ $selectedVoter->studyProgram?->name ?? 'Belum terpetakan' }}</div>
                    </div>

                    <div>
                        <label for="activationEmail" class="block font-display font-bold uppercase text-ink mb-1">
                            Email Login Mahasiswa <span class="text-rose-600">*</span>
                        </label>
                        <input type="email"
                               id="activationEmail"
                               wire:model="activationEmail"
                               placeholder="contoh: {{ strtolower(explode(' ', $selectedVoter->name)[0] ?? 'mahasiswa') }}@student.pnb.ac.id"
                               class="w-full px-3 py-2 border-2 border-ink bg-surface font-sans text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand shadow-brutal-sm">
                        @error('activationEmail')
                            <p class="text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="activationPassword" class="block font-display font-bold uppercase text-ink mb-1">
                            Password Sementara <span class="text-rose-600">*</span>
                        </label>
                        <input type="text"
                               id="activationPassword"
                               wire:model="activationPassword"
                               class="w-full px-3 py-2 border-2 border-ink bg-surface font-mono text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand shadow-brutal-sm">
                        <span class="text-[11px] text-ink/60 mt-0.5 block">Default otomatis: Pemira{{ $selectedVoter->nim }}! (Dapat Anda ubah sesuai kebutuhan).</span>
                        @error('activationPassword')
                            <p class="text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="activationNote" class="block font-display font-bold uppercase text-ink mb-1">
                            Alasan / Catatan Verifikasi Super Admin <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="activationNote"
                                  wire:model="activationNote"
                                  rows="2"
                                  placeholder="contoh: Validasi identitas via KTM WhatsApp (kendala email kampus tidak menerima OTP)"
                                  class="w-full px-3 py-2 border-2 border-ink bg-surface font-sans text-xs focus:outline-none focus:ring-2 focus:ring-brand shadow-brutal-sm"></textarea>
                        <span class="text-[11px] text-ink/60 mt-0.5 block">Catatan ini akan dicatat permanen dalam Audit Log untuk akuntabilitas.</span>
                        @error('activationNote')
                            <p class="text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="px-0 pt-3 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
                        <button wire:click="cancelActivation"
                                type="button"
                                class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase cursor-pointer text-center">
                            BATAL
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer text-center">
                            <span wire:loading.remove wire:target="activateVoterAccount">
                                AKTIFKAN AKUN SEKARANG
                            </span>
                            <span wire:loading wire:target="activateVoterAccount">
                                MENDAFTARKAN AKUN...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showActivationSuccessModal && $createdAccountData)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-ink/75 backdrop-blur-xs"
             role="dialog"
             aria-modal="true">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0"
                 x-data="{ copiedWa: false }">
                <div class="px-5 py-4 border-b-2 border-ink flex items-center justify-between bg-emerald-100 shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-emerald-950 block">STATUS: AKTIF & TERVERIFIKASI</span>
                        <h3 class="font-display font-black text-lg text-emerald-950 uppercase">
                            AKUN BERHASIL DIAKTIVASI!
                        </h3>
                    </div>
                    <button wire:click="closeActivationSuccessModal"
                            type="button"
                            class="w-9 h-9 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 sm:p-6 space-y-4 font-sans text-xs">
                    <p class="text-ink leading-relaxed">
                        Akun pemilih atas nama <strong>{{ $createdAccountData['name'] }}</strong> telah resmi terdaftar dan terverifikasi di sistem PEMIRA.
                    </p>

                    <div class="p-4 bg-surface-muted border-2 border-ink space-y-2">
                        <div class="flex justify-between border-b border-ink/20 pb-1">
                            <span class="text-ink/60">NIM:</span>
                            <span class="font-mono font-bold text-ink">{{ $createdAccountData['nim'] }}</span>
                        </div>
                        <div class="flex justify-between border-b border-ink/20 pb-1">
                            <span class="text-ink/60">Nama:</span>
                            <span class="font-display font-black uppercase text-ink">{{ $createdAccountData['name'] }}</span>
                        </div>
                        <div class="flex justify-between border-b border-ink/20 pb-1">
                            <span class="text-ink/60">Jurusan:</span>
                            <span class="font-bold text-ink">{{ $createdAccountData['prodi'] }}</span>
                        </div>
                        <div class="flex justify-between border-b border-ink/20 pb-1">
                            <span class="text-ink/60">Email Login:</span>
                            <span class="font-bold text-brand">{{ $createdAccountData['email'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink/60">Password:</span>
                            <span class="font-mono font-black text-rose-700 bg-surface px-1.5 py-0.5 border border-ink">{{ $createdAccountData['password'] }}</span>
                        </div>
                    </div>

                    @php
                        $waTemplate = "Halo {$createdAccountData['name']} (NIM: {$createdAccountData['nim']}),\n\nAkun PEMIRA 2026 kamu telah berhasil diaktivasi secara manual oleh Panitia/Super Admin.\n\nSilakan langsung login ke website PEMIRA:\nURL: ".url('/login')."\nEmail: {$createdAccountData['email']}\nPassword: {$createdAccountData['password']}\n\nSetelah berhasil login, silakan gunakan hak suara kamu pada bilik suara. Terima kasih!";
                    @endphp

                    <button type="button"
                            @click="navigator.clipboard.writeText(`{{ $waTemplate }}`); copiedWa = true; setTimeout(() => copiedWa = false, 2500)"
                            class="w-full px-4 py-3 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider text-center transition-all cursor-pointer">
                        <span x-show="!copiedWa">SALIN PESAN LOGIN UNTUK WHATSAPP MAHASISWA</span>
                        <span x-show="copiedWa" class="text-accent">✓ PESAN WHATSAPP BERHASIL DISALIN!</span>
                    </button>
                </div>

                <div class="px-5 py-3 border-t-2 border-ink bg-surface-muted text-right">
                    <button wire:click="closeActivationSuccessModal"
                            type="button"
                            class="px-5 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase cursor-pointer">
                        SELESAI / TUTUP
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="confirm-modal-title">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">KONFIRMASI SISTEM</span>
                        <h3 id="confirm-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            {{ $targetState ? 'AKTIFKAN MAINTENANCE MODE?' : 'NONAKTIFKAN MAINTENANCE MODE?' }}
                        </h3>
                    </div>
                    <button wire:click="cancelToggle"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-3 font-sans overflow-y-auto flex-1 min-w-0">
                    @if ($targetState)
                        <p class="text-xs sm:text-sm text-ink leading-relaxed">
                            Website akan masuk ke mode maintenance. VOTER dan ADMIN tidak dapat mengakses website sampai mode maintenance dimatikan.
                        </p>

                        <div class="p-3 bg-amber-50 border border-ink text-xs text-amber-950 space-y-1">
                            <span class="font-bold block">Pemberitahuan Akses:</span>
                            <p class="text-[11px] text-ink/80">Anda selaku Super Admin tetap dapat mengakses dashboard secara penuh untuk mematikan mode maintenance kapan saja.</p>
                        </div>
                    @else
                        <p class="text-xs sm:text-sm text-ink leading-relaxed">
                            Website akan kembali dapat diakses oleh pengguna sesuai jadwal dan permission yang berlaku.
                        </p>

                        <div class="p-3 bg-emerald-50 border border-ink text-xs text-emerald-950 space-y-1">
                            <span class="font-bold block">Pemberitahuan Akses:</span>
                            <p class="text-[11px] text-ink/80">Seluruh pemilih dan administrator KPR akan dapat kembali berinteraksi dengan sistem secara normal.</p>
                        </div>
                    @endif
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                    <button wire:click="cancelToggle"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer text-center">
                        BATAL
                    </button>

                    <button wire:click="confirmToggle"
                            type="button"
                            class="w-full sm:w-auto px-5 py-2.5 {{ $targetState ? 'bg-brand hover:bg-brand-dark text-accent' : 'bg-emerald-700 hover:bg-emerald-800 text-surface' }} border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer text-center">
                        {{ $targetState ? 'AKTIFKAN MAINTENANCE' : 'NONAKTIFKAN MAINTENANCE' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
