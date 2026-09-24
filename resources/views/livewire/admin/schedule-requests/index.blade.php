<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-ink pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                @if ($isSuperAdmin)
                    <span class="px-2 py-0.5 bg-brand text-accent text-[10px] font-display font-black uppercase tracking-wider border border-ink">
                        OTORITAS PENINJAU
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-surface-muted text-ink text-[10px] font-display font-black uppercase tracking-wider border border-ink">
                        PANITIA / ADMIN KPR
                    </span>
                @endif
            </div>
            <h2 class="font-display font-extrabold text-xl sm:text-2xl text-ink uppercase tracking-tight">
                {{ $isSuperAdmin ? 'PERSETUJUAN PERUBAHAN JADWAL' : 'PENGAJUAN PERUBAHAN JADWAL' }}
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1">
                {{ $isSuperAdmin
                    ? 'Antrean review dan otorisasi permohonan perubahan jadwal pemungutan suara dari panitia PEMIRA.'
                    : 'Kelola dan pantau status permohonan perubahan jadwal voting yang Anda ajukan ke Super Admin.' }}
            </p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            @if ($isSuperAdmin)
                <a href="{{ route('admin.elections.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-surface text-ink border-2 border-ink shadow-brutal hover:bg-surface-muted font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer w-full sm:w-auto">
                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Kelola Jadwal Resmi</span>
                </a>
            @else
                <button type="button"
                        wire:click="openCreateModal"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand text-surface border-2 border-ink shadow-brutal hover:bg-accent hover:text-brand font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajukan Perubahan</span>
                </button>
            @endif
        </div>
    </div>

    @if ($isSuperAdmin)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-amber-100 border-2 border-ink p-4 shadow-brutal relative">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-xs font-display font-black uppercase tracking-wider text-amber-950 block">
                        MENUNGGU REVIEW
                    </span>
                    @if ($stats['pending'] > 0)
                        <span class="px-1.5 py-0.5 bg-amber-600 text-surface text-[9px] font-mono font-bold">
                            PERLU TINDAKAN
                        </span>
                    @endif
                </div>
                <div class="font-mono font-black text-xl sm:text-2xl text-amber-900 mt-1">
                    {{ $stats['pending'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">
                    TOTAL MASUK
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-ink mt-1">
                    {{ $stats['total'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-emerald-800 block">
                    DISETUJUI
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-emerald-600 mt-1">
                    {{ $stats['approved'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-red-800 block">
                    DITOLAK
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-red-600 mt-1">
                    {{ $stats['rejected'] }}
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">
                    PENGAJUAN SAYA
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-ink mt-1">
                    {{ $stats['total'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-amber-800 block">
                    MENUNGGU REVIEW
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-amber-600 mt-1">
                    {{ $stats['pending'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-emerald-800 block">
                    DISETUJUI
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-emerald-600 mt-1">
                    {{ $stats['approved'] }}
                </div>
            </div>

            <div class="bg-surface border-2 border-ink p-4 shadow-brutal">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-red-800 block">
                    DITOLAK
                </span>
                <div class="font-mono font-black text-xl sm:text-2xl text-red-600 mt-1">
                    {{ $stats['rejected'] }}
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="p-3.5 bg-emerald-100 border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold text-emerald-950 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" wire:click="$refresh" class="text-emerald-950 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3.5 bg-red-100 border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold text-red-950 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" wire:click="$refresh" class="text-red-950 hover:text-red-700 font-bold">&times;</button>
        </div>
    @endif

    <div class="bg-surface border-2 border-ink p-3 sm:p-4 shadow-brutal flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2.5 sm:gap-3 min-w-0">
        <div class="relative flex-1 min-w-0">
            <label for="search-input" class="sr-only">Cari pengajuan jadwal</label>
            <input id="search-input"
                   type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="{{ $isSuperAdmin ? 'Cari alasan, nama PEMIRA, atau email admin pengaju...' : 'Cari alasan pengajuan atau nama PEMIRA...' }}"
                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink placeholder-ink/50 focus:outline-none focus:bg-surface shadow-2xs min-h-10" />
            @if ($search)
                <button type="button"
                        wire:click="$set('search', '')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-ink/50 hover:text-ink font-bold text-xs"
                        aria-label="Hapus pencarian">
                    &times;
                </button>
            @endif
        </div>

        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0 overflow-x-auto">
            <div class="flex-1 sm:flex-initial min-w-28 sm:min-w-36">
                <label for="status-filter" class="sr-only">Filter Status</label>
                <select id="status-filter"
                        wire:model.live="statusFilter"
                        class="w-full bg-surface-muted border-2 border-ink px-2 sm:px-3 py-2 text-xs font-display font-bold uppercase tracking-wider shadow-2xs focus:outline-none cursor-pointer min-h-10">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>

            @if ($elections->count() > 1)
                <div class="flex-1 sm:flex-initial min-w-32 sm:min-w-40">
                    <label for="election-filter" class="sr-only">Filter Pemilihan</label>
                    <select id="election-filter"
                            wire:model.live="electionFilter"
                            class="w-full bg-surface-muted border-2 border-ink px-2 sm:px-3 py-2 text-xs font-display font-bold uppercase tracking-wider shadow-2xs focus:outline-none cursor-pointer min-h-10 truncate">
                        <option value="">Semua Pemilihan</option>
                        @foreach ($elections as $elec)
                            <option value="{{ $elec->id }}">{{ $elec->name }} ({{ $elec->year }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="shrink-0">
                <label for="per-page-select" class="sr-only">Jumlah per halaman</label>
                <select id="per-page-select"
                        wire:model.live="perPage"
                        class="bg-surface-muted border-2 border-ink px-2 sm:px-2.5 py-2 text-xs font-mono font-bold shadow-2xs focus:outline-none cursor-pointer min-h-10">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden min-w-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-220">
                <thead>
                    <tr class="border-b-2 border-ink bg-surface-muted text-xs font-display font-black text-brand uppercase tracking-wider">
                        <th class="p-3.5 text-center w-12">NO</th>
                        <th class="p-3.5 w-56">{{ $isSuperAdmin ? 'PEMIRA & ADMIN PENGAJU' : 'PEMIRA & WAKTU PENGAJUAN' }}</th>
                        <th class="p-3.5 w-64">JADWAL SAAT INI</th>
                        <th class="p-3.5 w-64">JADWAL DIAJUKAN</th>
                        <th class="p-3.5">ALASAN</th>
                        <th class="p-3.5 text-center w-40">STATUS</th>
                        <th class="p-3.5 text-center w-36">{{ $isSuperAdmin ? 'AKSI REVIEW' : 'AKSI' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10 text-xs font-sans">
                    @forelse ($items as $index => $item)
                        <tr class="hover:bg-surface-muted/50 transition-colors {{ $item->isPending() ? 'bg-amber-50/40' : '' }}">
                            <td class="p-3.5 text-center font-mono font-bold text-ink/70">
                                {{ $items->firstItem() + $index }}
                            </td>
                            <td class="p-3.5">
                                <div class="font-display font-black text-ink uppercase text-xs sm:text-sm">
                                    {{ $item->election?->name ?? 'PEMIRA' }}
                                </div>
                                @if ($isSuperAdmin)
                                    <div class="text-[11px] text-ink/70 mt-0.5">
                                        Pengaju: <span class="font-semibold text-brand">{{ $item->requester?->email }}</span>
                                    </div>
                                @endif
                                <div class="text-[10px] font-mono text-ink/50 mt-0.5">
                                    Diajukan: {{ $item->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                                </div>
                            </td>
                            <td class="p-3.5 bg-surface-muted/30">
                                <div class="space-y-2 font-mono text-[11px]">
                                    @if ($item->hasRegistrationChange() || $item->old_registration_start_at)
                                        <div class="space-y-0.5 pb-1.5 border-b border-ink/10">
                                            <span class="text-[10px] font-display font-black uppercase text-brand block">Pendaftaran</span>
                                            <div class="text-ink/70">
                                                {{ $item->old_registration_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} — {{ $item->old_registration_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="space-y-0.5">
                                        <span class="text-[10px] font-display font-bold uppercase text-ink/60 block">Voting</span>
                                        <div class="text-ink/80">
                                            {{ $item->old_voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} — {{ $item->old_voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 bg-accent/10">
                                <div class="space-y-2 font-mono text-[11px]">
                                    @if ($item->hasRegistrationChange() || $item->new_registration_start_at)
                                        <div class="space-y-0.5 pb-1.5 border-b border-brand/20">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-display font-black uppercase text-brand">Pendaftaran Baru</span>
                                                @if ($item->hasRegistrationChange())
                                                    <span class="px-1 py-0.2 bg-brand text-accent text-[8px] font-mono font-black">DIUBAH</span>
                                                @endif
                                            </div>
                                            <div class="font-bold text-ink">
                                                {{ $item->new_registration_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} — {{ $item->new_registration_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-display font-bold uppercase text-brand">Voting Baru</span>
                                            @if ($item->hasVotingChange())
                                                <span class="px-1 py-0.2 bg-brand text-accent text-[8px] font-mono font-black">DIUBAH</span>
                                            @endif
                                        </div>
                                        <div class="font-bold text-ink">
                                            {{ $item->new_voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} — {{ $item->new_voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <p class="text-ink/90 line-clamp-2 italic" title="{{ $item->reason }}">
                                    &ldquo;{{ $item->reason }}&rdquo;
                                </p>
                                @if ($item->review_note)
                                    <div class="mt-1.5 p-1.5 bg-surface-muted border border-ink/20 text-[11px] text-ink/80">
                                        <span class="font-bold uppercase text-[10px] text-red-700 block">Catatan Review:</span>
                                        <span class="line-clamp-2">{{ $item->review_note }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                @if ($item->isPending())
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-900 border border-ink text-xs font-display font-black uppercase tracking-wider">
                                        Menunggu Persetujuan
                                    </span>
                                @elseif ($item->isApproved())
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-ink text-xs font-display font-black uppercase tracking-wider">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-900 border border-ink text-xs font-display font-black uppercase tracking-wider">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            wire:click="openDetailModal({{ $item->id }})"
                                            class="w-8 h-8 bg-surface-muted border border-ink hover:bg-accent flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                                            title="Lihat Detail Pengajuan"
                                            aria-label="Lihat Detail Pengajuan">
                                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    @if ($item->isPending() && ! $isSuperAdmin && $item->requested_by === auth()->id())
                                        <button type="button"
                                                wire:click="openEditModal({{ $item->id }})"
                                                class="w-8 h-8 bg-surface-muted border border-ink hover:bg-accent flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                                                title="Edit Pengajuan"
                                                aria-label="Edit Pengajuan">
                                            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button type="button"
                                                wire:click="openDeleteModal({{ $item->id }})"
                                                class="w-8 h-8 bg-surface-muted border border-ink hover:bg-red-500 hover:text-surface flex items-center justify-center transition-colors cursor-pointer shadow-2xs text-red-700"
                                                title="Batalkan Pengajuan"
                                                aria-label="Batalkan Pengajuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($item->isPending() && $isSuperAdmin)
                                        <button type="button"
                                                wire:click="openApproveModal({{ $item->id }})"
                                                class="w-8 h-8 bg-emerald-600 text-surface border border-ink hover:bg-emerald-700 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                                                title="Setujui Perubahan Jadwal"
                                                aria-label="Setujui Perubahan Jadwal">
                                            <svg class="w-4 h-4 text-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>

                                        <button type="button"
                                                wire:click="openRejectModal({{ $item->id }})"
                                                class="w-8 h-8 bg-red-600 text-surface border border-ink hover:bg-red-700 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                                                title="Tolak Pengajuan"
                                                aria-label="Tolak Pengajuan">
                                            <svg class="w-4 h-4 text-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-ink/60">
                                <div class="max-w-md mx-auto space-y-3">
                                    <div class="w-12 h-12 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                                        <svg class="w-6 h-6 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="font-display font-bold text-sm text-ink uppercase">
                                        {{ $isSuperAdmin ? 'TIDAK ADA ANTREAN PENINJAUAN' : 'BELUM ADA PENGAJUAN JADWAL' }}
                                    </p>
                                    <p class="text-xs font-sans text-ink/60">
                                        {{ $isSuperAdmin
                                            ? 'Saat ini tidak ada permohonan perubahan jadwal voting dari Admin yang perlu ditinjau.'
                                            : 'Anda belum pernah mengajukan permohonan perubahan jadwal voting.' }}
                                    </p>
                                    @if (! $isSuperAdmin)
                                        <div class="pt-2">
                                            <button type="button"
                                                    wire:click="openCreateModal"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand text-surface border border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-accent hover:text-brand transition-colors shadow-2xs cursor-pointer">
                                                <span>+ Ajukan Perubahan Jadwal</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        @if ($items->hasPages())
            <div class="p-3 border-t-2 border-ink bg-surface-muted">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-xl max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="p-4 border-b-2 border-ink bg-brand text-surface flex items-center justify-between">
                    <div>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-tight">
                            AJUKAN PERUBAHAN JADWAL
                        </h3>
                        <p class="text-[11px] font-sans text-surface/80">
                            Ajukan perubahan jadwal pemungutan suara untuk ditinjau oleh Super Admin.
                        </p>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="text-surface hover:text-accent font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <form wire:submit="createRequest" class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label for="create-election-id" class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Pilih PEMIRA <span class="text-red-600">*</span>
                        </label>
                        <select id="create-election-id"
                                wire:model.live="election_id"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs font-sans text-ink focus:outline-none cursor-pointer">
                            <option value="">-- Pilih Pemilihan --</option>
                            @foreach ($elections as $elec)
                                <option value="{{ $elec->id }}">{{ $elec->name }} ({{ $elec->year }})</option>
                            @endforeach
                        </select>
                        @error('election_id')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($current_voting_start_at && $current_voting_end_at)
                        <div class="p-3 bg-surface-muted border-2 border-ink text-xs font-sans space-y-1 shadow-2xs">
                            <span class="text-[10px] font-display font-bold uppercase text-ink/60 block tracking-wider">
                                JADWAL VOTING SAAT INI (DATABASE)
                            </span>
                            <div class="font-mono text-ink/80 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <div><strong>Mulai:</strong> {{ $current_voting_start_at }}</div>
                                <div><strong>Selesai:</strong> {{ $current_voting_end_at }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="p-3 bg-accent/10 border-2 border-ink space-y-3 shadow-2xs">
                        <span class="text-xs font-display font-black uppercase text-brand block tracking-wider">
                            JADWAL VOTING BARU YANG DIAJUKAN
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="create-start-at" class="block text-[11px] font-sans font-bold text-ink mb-1">
                                    Waktu Mulai Baru <span class="text-red-600">*</span>
                                </label>
                                <input id="create-start-at"
                                       type="datetime-local"
                                       lang="id-ID"
                                       wire:model="new_voting_start_at"
                                       class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none" />
                                @error('new_voting_start_at')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="create-end-at" class="block text-[11px] font-sans font-bold text-ink mb-1">
                                    Waktu Selesai Baru <span class="text-red-600">*</span>
                                </label>
                                <input id="create-end-at"
                                       type="datetime-local"
                                       lang="id-ID"
                                       wire:model="new_voting_end_at"
                                       class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none" />
                                @error('new_voting_end_at')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="create-reason" class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Alasan Perubahan Jadwal <span class="text-red-600">*</span>
                        </label>
                        <textarea id="create-reason"
                                  rows="3"
                                  wire:model="reason"
                                  placeholder="Jelaskan dasar permohonan perubahan jadwal pemungutan suara (misal: kendala teknis jaringan, perpanjangan masa pemungutan)..."
                                  class="w-full bg-surface-muted border-2 border-ink p-2.5 text-xs font-sans text-ink focus:outline-none focus:bg-surface"></textarea>
                        @error('reason')
                            <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                        <button type="button"
                                wire:click="closeCreateModal"
                                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 border-2 border-ink bg-surface-muted font-display font-bold text-xs uppercase tracking-wider text-center cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 sm:py-2 border-2 border-ink bg-brand text-surface hover:bg-accent hover:text-brand font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-xl max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="p-4 border-b-2 border-ink bg-brand text-surface flex items-center justify-between">
                    <div>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-tight">
                            EDIT PENGAJUAN JADWAL
                        </h3>
                        <p class="text-[11px] font-sans text-surface/80">
                            Perbarui waktu atau alasan pengajuan sebelum ditinjau oleh Super Admin.
                        </p>
                    </div>
                    <button type="button" wire:click="closeEditModal" class="text-surface hover:text-accent font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <form wire:submit="updateRequest" class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
                    @if ($current_voting_start_at && $current_voting_end_at)
                        <div class="p-3 bg-surface-muted border-2 border-ink text-xs font-sans space-y-1 shadow-2xs">
                            <span class="text-[10px] font-display font-bold uppercase text-ink/60 block tracking-wider">
                                JADWAL SAAT INI (DATABASE)
                            </span>
                            <div class="font-mono text-ink/80 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <div><strong>Mulai:</strong> {{ $current_voting_start_at }}</div>
                                <div><strong>Selesai:</strong> {{ $current_voting_end_at }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="p-3 bg-accent/10 border-2 border-ink space-y-3 shadow-2xs">
                        <span class="text-xs font-display font-black uppercase text-brand block tracking-wider">
                            JADWAL VOTING BARU YANG DIAJUKAN
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="edit-start-at" class="block text-[11px] font-sans font-bold text-ink mb-1">
                                    Waktu Mulai Baru <span class="text-red-600">*</span>
                                </label>
                                <input id="edit-start-at"
                                       type="datetime-local"
                                       lang="id-ID"
                                       wire:model="new_voting_start_at"
                                       class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none" />
                                @error('new_voting_start_at')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="edit-end-at" class="block text-[11px] font-sans font-bold text-ink mb-1">
                                    Waktu Selesai Baru <span class="text-red-600">*</span>
                                </label>
                                <input id="edit-end-at"
                                       type="datetime-local"
                                       lang="id-ID"
                                       wire:model="new_voting_end_at"
                                       class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none" />
                                @error('new_voting_end_at')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="edit-reason" class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Alasan Perubahan Jadwal <span class="text-red-600">*</span>
                        </label>
                        <textarea id="edit-reason"
                                  rows="3"
                                  wire:model="reason"
                                  class="w-full bg-surface-muted border-2 border-ink p-2.5 text-xs font-sans text-ink focus:outline-none focus:bg-surface"></textarea>
                        @error('reason')
                            <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                        <button type="button"
                                wire:click="closeEditModal"
                                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 border-2 border-ink bg-surface-muted font-display font-bold text-xs uppercase tracking-wider text-center cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 sm:py-2 border-2 border-ink bg-brand text-surface hover:bg-accent hover:text-brand font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDetailModal && $selectedRequest)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-xl max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="p-4 border-b-2 border-ink bg-brand text-surface flex items-center justify-between">
                    <div>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-tight">
                            RINCIAN PENGAJUAN JADWAL
                        </h3>
                        <p class="text-[11px] font-sans text-surface/80">
                            {{ $selectedRequest->election?->name }} ({{ $selectedRequest->election?->year }})
                        </p>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="text-surface hover:text-accent font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 font-sans text-xs">
                    <div class="flex items-center justify-between border-b-2 border-ink/15 pb-3">
                        <div>
                            <span class="text-[10px] font-display font-bold uppercase text-ink/50 block">Status Pengajuan</span>
                            @if ($selectedRequest->isPending())
                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-900 border border-ink text-xs font-display font-black uppercase">
                                    Menunggu Persetujuan
                                </span>
                            @elseif ($selectedRequest->isApproved())
                                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-ink text-xs font-display font-black uppercase">
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-900 border border-ink text-xs font-display font-black uppercase">
                                    Ditolak
                                </span>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="text-[10px] font-display font-bold uppercase text-ink/50 block">Diajukan Pada</span>
                            <span class="font-mono font-bold text-ink">
                                {{ $selectedRequest->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                            </span>
                        </div>
                    </div>

                    @if ($selectedRequest->hasRegistrationChange() || $selectedRequest->new_registration_start_at)
                        <div class="space-y-1">
                            <span class="text-[10px] font-display font-black uppercase text-brand tracking-wider block">
                                PERUBAHAN JADWAL PENDAFTARAN
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3 bg-surface-muted border-2 border-ink space-y-1">
                                    <span class="text-[10px] font-display font-bold uppercase text-ink/60 block">
                                        Pendaftaran Semula
                                    </span>
                                    <div class="font-mono text-xs space-y-0.5">
                                        <div><span class="text-ink/60">Mulai:</span> {{ $selectedRequest->old_registration_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} WITA</div>
                                        <div><span class="text-ink/60">Selesai:</span> {{ $selectedRequest->old_registration_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} WITA</div>
                                    </div>
                                </div>
                                <div class="p-3 bg-accent/20 border-2 border-ink space-y-1">
                                    <span class="text-[10px] font-display font-bold uppercase text-brand block">
                                        Pendaftaran Baru Diajukan
                                    </span>
                                    <div class="font-mono text-xs space-y-0.5 font-bold">
                                        <div><span class="text-ink/60 font-normal">Mulai:</span> {{ $selectedRequest->new_registration_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} WITA</div>
                                        <div><span class="text-ink/60 font-normal">Selesai:</span> {{ $selectedRequest->new_registration_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }} WITA</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-1">
                        <span class="text-[10px] font-display font-black uppercase text-brand tracking-wider block">
                            PERUBAHAN JADWAL VOTING
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 bg-surface-muted border-2 border-ink space-y-1">
                                <span class="text-[10px] font-display font-bold uppercase text-ink/60 block">
                                    Voting Semula
                                </span>
                                <div class="font-mono text-xs space-y-0.5">
                                    <div><span class="text-ink/60">Mulai:</span> {{ $selectedRequest->old_voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
                                    <div><span class="text-ink/60">Selesai:</span> {{ $selectedRequest->old_voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
                                </div>
                            </div>

                            <div class="p-3 bg-accent/20 border-2 border-ink space-y-1">
                                <span class="text-[10px] font-display font-black uppercase text-brand block">
                                    Voting Baru Diajukan
                                </span>
                                <div class="font-mono text-xs space-y-0.5 font-bold">
                                    <div><span class="text-ink/60 font-normal">Mulai:</span> {{ $selectedRequest->new_voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
                                    <div><span class="text-ink/60 font-normal">Selesai:</span> {{ $selectedRequest->new_voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-display font-bold uppercase text-ink/50 block mb-1">
                            Alasan Perubahan
                        </span>
                        <div class="p-3 bg-surface-muted border border-ink/20 text-ink leading-relaxed">
                            {{ $selectedRequest->reason }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-ink/15 text-[11px]">
                        <div>
                            <span class="text-[10px] font-display font-bold uppercase text-ink/50 block">Pengaju</span>
                            <span class="font-bold text-ink">{{ $selectedRequest->requester?->email }}</span>
                        </div>

                        @if ($selectedRequest->reviewer)
                            <div>
                                <span class="text-[10px] font-display font-bold uppercase text-ink/50 block">Ditinjau Oleh</span>
                                <span class="font-bold text-ink">{{ $selectedRequest->reviewer->email }}</span>
                                <span class="text-ink/60 block text-[10px] font-mono">
                                    {{ $selectedRequest->reviewed_at?->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($selectedRequest->review_note)
                        <div class="p-3 bg-red-50 border-2 border-red-900/30 text-xs">
                            <span class="text-[10px] font-display font-black uppercase text-red-800 block mb-0.5">
                                Catatan Penolakan (Super Admin)
                            </span>
                            <p class="text-red-950">{{ $selectedRequest->review_note }}</p>
                        </div>
                    @endif
                </div>

                <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex items-center justify-end shrink-0">
                    <button type="button"
                            wire:click="closeDetailModal"
                            class="w-full sm:w-auto px-5 py-2.5 sm:py-2 border-2 border-ink bg-surface font-display font-bold text-xs uppercase tracking-wider text-center cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showApproveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-md max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0 p-4 sm:p-5 space-y-4">
                <div class="flex items-center gap-3 border-b-2 border-ink pb-3 text-emerald-950 shrink-0">
                    <div class="w-10 h-10 bg-emerald-100 border-2 border-ink flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-display font-black text-sm sm:text-base uppercase text-brand truncate">
                            SETUJUI PERUBAHAN JADWAL?
                        </h3>
                        <span class="text-xs font-sans text-ink/70 block truncate">Tindakan Otorisasi Resmi Super Admin</span>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1">
                    <p class="text-xs font-sans text-ink/80 leading-relaxed">
                        Dengan menyetujui pengajuan ini, jadwal pemungutan suara resmi pada tabel pemilihan akan langsung diperbarui ke waktu baru yang diajukan. Tindakan ini bersifat atomik dan mengikat.
                    </p>
                </div>

                <div class="pt-3 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeApproveModal"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 border-2 border-ink bg-surface-muted font-display font-bold text-xs uppercase text-center cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="approveRequest"
                            class="w-full sm:w-auto px-5 py-2.5 sm:py-2 border-2 border-ink bg-emerald-600 text-surface hover:bg-emerald-700 font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                        Ya, Setujui
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-md max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0 p-4 sm:p-5 space-y-4">
                <div class="flex items-center gap-3 border-b-2 border-ink pb-3 text-red-950 shrink-0">
                    <div class="w-10 h-10 bg-red-100 border-2 border-ink flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-display font-black text-sm sm:text-base uppercase text-brand truncate">
                            TOLAK PENGAJUAN JADWAL
                        </h3>
                        <span class="text-xs font-sans text-ink/70 block truncate">Wajib memberikan catatan penolakan</span>
                    </div>
                </div>

                <div class="space-y-2 overflow-y-auto flex-1">
                    <label for="reject-note" class="block text-xs font-display font-bold uppercase text-ink">
                        Alasan Penolakan <span class="text-red-600">*</span>
                    </label>
                    <textarea id="reject-note"
                              rows="3"
                              wire:model="review_note"
                              placeholder="Tuliskan pertimbangan atau instruksi penolakan untuk pengaju..."
                              class="w-full bg-surface-muted border-2 border-ink p-2.5 text-xs font-sans text-ink focus:outline-none focus:bg-surface"></textarea>
                    @error('review_note')
                        <p class="text-xs font-sans font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeRejectModal"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 border-2 border-ink bg-surface-muted font-display font-bold text-xs uppercase text-center cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="rejectRequest"
                            class="w-full sm:w-auto px-5 py-2.5 sm:py-2 border-2 border-ink bg-red-600 text-surface hover:bg-red-700 font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                        Tolak Pengajuan
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-md max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0 p-4 sm:p-5 space-y-4">
                <div class="flex items-center gap-3 border-b-2 border-ink pb-3 text-red-950 shrink-0">
                    <div class="w-10 h-10 bg-red-100 border-2 border-ink flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-display font-black text-sm sm:text-base uppercase text-brand truncate">
                            BATALKAN PENGAJUAN?
                        </h3>
                        <span class="text-xs font-sans text-ink/70 block truncate">Tindakan pembatalan permohonan</span>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1">
                    <p class="text-xs font-sans text-ink/80 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan pengajuan perubahan jadwal ini? Pengajuan yang dibatalkan akan dihapus dari antrean review.
                    </p>
                </div>

                <div class="pt-3 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeDeleteModal"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 border-2 border-ink bg-surface-muted font-display font-bold text-xs uppercase text-center cursor-pointer">
                        Kembali
                    </button>
                    <button type="button"
                            wire:click="deleteRequest"
                            class="w-full sm:w-auto px-5 py-2.5 sm:py-2 border-2 border-ink bg-red-600 text-surface hover:bg-red-700 font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                        Ya, Batalkan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
