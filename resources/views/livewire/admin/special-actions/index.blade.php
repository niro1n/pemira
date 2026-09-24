<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>KONTROL SISTEM UTAMA</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                TINDAKAN KHUSUS
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                Kelola kontrol sistem yang memengaruhi akses dan operasional website PEMIRA.
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
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-brand inline-block"></span>
            <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-wider text-ink">
                Status Sistem
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
