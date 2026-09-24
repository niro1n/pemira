<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>KEAMANAN & AKUNTABILITAS</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                AUDIT & LOG AKTIVITAS
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                @if ($isSuperAdmin)
                    Pantau seluruh riwayat aktivitas sistem, log autentikasi, dan tindakan administratif secara menyeluruh.
                @else
                    Pantau riwayat aktivitas operasional data pemilih, paslon, dan pemilihan sesuai kewenangan.
                @endif
            </p>
        </div>

        <div class="shrink-0 flex items-center gap-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase tracking-wider text-ink">
                <span class="w-2 h-2 {{ $isSuperAdmin ? 'bg-accent' : 'bg-brand' }} inline-block border border-ink"></span>
                <span>SCOPE: {{ $isSuperAdmin ? 'SUPER ADMIN (FULL)' : 'ADMIN KPR (OPERASIONAL)' }}</span>
            </div>
        </div>
    </div>

    <div class="grid {{ $isSuperAdmin ? 'grid-cols-2 lg:grid-cols-5' : 'grid-cols-2 sm:grid-cols-4' }} gap-2.5 sm:gap-3 min-w-0">
        <div class="{{ $isSuperAdmin ? 'col-span-2 lg:col-span-1' : '' }} bg-surface border-2 border-ink p-3 sm:p-3.5 shadow-brutal min-w-0 {{ $isSuperAdmin ? 'flex items-center justify-between lg:block' : '' }}">
            <div class="min-w-0">
                <span class="text-xs font-display font-black uppercase tracking-wider text-ink/70 block">TOTAL LOG</span>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block">
                    {{ $isSuperAdmin ? 'Semua Aktivitas' : 'Aktivitas Terkait' }}
                </span>
            </div>
            <div class="font-display font-black text-2xl sm:text-3xl text-brand {{ $isSuperAdmin ? 'lg:mt-1' : 'mt-0.5 sm:mt-1' }} shrink-0">
                {{ number_format($stats['total'], 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3.5 shadow-brutal min-w-0 flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-emerald-800 block leading-tight">HARI INI</span>
            <div class="font-display font-black text-lg sm:text-2xl text-emerald-700 mt-1">
                {{ number_format($stats['today'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block">Aktivitas Baru</span>
        </div>

        @if ($isSuperAdmin)
            <div class="bg-surface border-2 border-ink p-2.5 sm:p-3.5 shadow-brutal min-w-0 flex flex-col justify-between">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-brand block leading-tight">AUTENTIKASI</span>
                <div class="font-display font-black text-lg sm:text-2xl text-brand mt-1">
                    {{ number_format($stats['auth'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block">Login & Keluar</span>
            </div>
        @endif

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3.5 shadow-brutal min-w-0 flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-amber-800 block leading-tight">DATA PEMILIH</span>
            <div class="font-display font-black text-lg sm:text-2xl text-amber-700 mt-1">
                {{ number_format($stats['voters'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block">Master & Akun</span>
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3.5 shadow-brutal min-w-0 flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-ink/70 block leading-tight">PASLON & PEMIRA</span>
            <div class="font-display font-black text-lg sm:text-2xl text-ink/80 mt-1">
                {{ number_format($stats['elections'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block">Kandidat & Event</span>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink p-3 sm:p-4 shadow-brutal space-y-3">
        <div class="flex items-center justify-between gap-2 sm:gap-3 min-w-0">
            <div class="relative flex-1 min-w-0">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari aksi, deskripsi, IP address, atau email..."
                       aria-label="Cari aktivitas audit"
                       class="w-full bg-surface-muted border-2 border-ink pl-9 pr-3 py-2 text-xs sm:text-sm font-sans font-medium text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="openFilterModal"
                        type="button"
                        aria-label="Buka filter audit log"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10 {{ $activeFilters > 0 ? 'bg-accent text-ink' : 'bg-surface-muted hover:bg-accent text-ink' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>{{ $activeFilters > 0 ? 'FILTER · ' . $activeFilters : 'FILTER' }}</span>
                </button>

                <select wire:model.live="perPage"
                        aria-label="Jumlah per halaman"
                        class="bg-surface-muted border-2 border-ink px-2.5 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer min-h-10">
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                    <option value="100">100 / hal</option>
                </select>
            </div>
        </div>

        @if ($activeFilters > 0)
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-ink/10">
                <span class="text-xs font-display font-bold uppercase text-ink/60 tracking-wider">Filter Aktif:</span>

                @if ($isSuperAdmin && $actorFilter !== 'all')
                    @php
                        $actorLabel = $actorFilter === 'system' ? 'Sistem' : ($actorList->firstWhere('id', (int) $actorFilter)?->email ?? 'Aktor #' . $actorFilter);
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Aktor: {{ $actorLabel }}</span>
                        <button wire:click="clearFilter('actor')" type="button" aria-label="Hapus filter aktor" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($isSuperAdmin && $roleFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Role: {{ strtoupper(str_replace('_', ' ', $roleFilter)) }}</span>
                        <button wire:click="clearFilter('role')" type="button" aria-label="Hapus filter role" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($moduleFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Modul: {{ $moduleFilter === 'general' ? 'Umum / Sistem' : class_basename($moduleFilter) }}</span>
                        <button wire:click="clearFilter('module')" type="button" aria-label="Hapus filter modul" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($actionFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Aksi: {{ strtoupper(str_replace('_', ' ', $actionFilter)) }}</span>
                        <button wire:click="clearFilter('action')" type="button" aria-label="Hapus filter aksi" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($startDate !== '')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Dari: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</span>
                        <button wire:click="clearFilter('start_date')" type="button" aria-label="Hapus filter tanggal mulai" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($endDate !== '')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Sampai: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                        <button wire:click="clearFilter('end_date')" type="button" aria-label="Hapus filter tanggal akhir" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                <button wire:click="resetFilters"
                        type="button"
                        class="text-xs font-display font-bold text-red-700 hover:text-red-900 hover:underline uppercase cursor-pointer ml-1">
                    Reset Semua
                </button>
            </div>
        @endif
    </div>

    @if ($stats['total'] === 0)
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg sm:text-xl text-brand uppercase">
                    BELUM ADA AKTIVITAS
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    @if ($isSuperAdmin)
                        Aktivitas sistem dan administratif akan dicatat secara otomatis di sini.
                    @else
                        Belum ada aktivitas operasional yang tercatat dalam wewenang Anda.
                    @endif
                </p>
            </div>
        </div>
    @elseif ($logs->isEmpty())
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg text-brand uppercase">
                    LOG TIDAK DITEMUKAN
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Tidak ada aktivitas yang sesuai dengan kata kunci atau filter yang dipilih dalam scope Anda.
                </p>
            </div>
            <div>
                <button wire:click="resetFilters"
                        type="button"
                        class="px-4 py-2 bg-surface border-2 border-ink text-xs font-display font-bold text-ink shadow-brutal-sm hover:bg-accent cursor-pointer">
                    RESET FILTER
                </button>
            </div>
        </div>
    @else
        <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden">
            <div class="overflow-x-auto max-h-[calc(100vh-280px)] min-h-105">
                <table class="w-full text-left border-separate border-spacing-0 min-w-220">
                    <thead class="sticky top-0 z-20">
                        <tr class="bg-surface-muted">
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-3 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center w-12 sm:w-16">NO</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">WAKTU (WITA)</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">AKTOR</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">AKSI</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">MODUL</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">DESKRIPSI</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">IP ADDRESS</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center w-16">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            @php
                                $actionName = strtolower($log->action);
                                $actionBadgeClass = match (true) {
                                    str_contains($actionName, 'create') || str_contains($actionName, 'import') || str_contains($actionName, 'store') => 'bg-emerald-200 text-emerald-950 border border-ink',
                                    str_contains($actionName, 'update') || str_contains($actionName, 'edit') || str_contains($actionName, 'toggle') => 'bg-amber-200 text-amber-950 border border-ink',
                                    str_contains($actionName, 'delete') || str_contains($actionName, 'destroy') => 'bg-red-100 text-red-900 border border-ink',
                                    str_contains($actionName, 'login') || str_contains($actionName, 'logout') => 'bg-brand text-accent border border-ink',
                                    default => 'bg-surface-muted text-ink border border-ink',
                                };
                            @endphp
                            <tr class="hover:bg-surface-muted/50 transition-colors">
                                <td class="px-3 py-3 text-xs sm:text-sm font-mono font-bold text-ink/70 text-center whitespace-nowrap border-b border-ink/10">
                                    {{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <div class="text-xs font-mono font-bold text-ink">
                                        {{ $log->created_at->timezone('Asia/Makassar')->format('d M Y, H:i:s') }}
                                    </div>
                                    <div class="text-[10px] font-sans text-ink/50 mt-0.5">
                                        {{ $log->created_at->timezone('Asia/Makassar')->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center border-b border-ink/10">
                                    @if ($log->user)
                                        <div class="inline-flex flex-col items-center max-w-50">
                                            <span class="text-xs font-sans font-bold text-ink truncate w-full" title="{{ $log->user->email }}">
                                                {{ $log->user->email }}
                                            </span>
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 bg-surface-muted border border-ink/40 text-[9px] font-display font-bold uppercase tracking-wider text-ink/70">
                                                {{ $log->user->role === 'super_admin' ? 'SUPER ADMIN' : ($log->user->role === 'admin' ? 'ADMIN' : 'PEMILIH') }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-ink text-surface border border-ink text-[10px] font-display font-bold uppercase">
                                            SISTEM
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-display font-bold uppercase {{ $actionBadgeClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    @if ($log->entity_type)
                                        <div class="text-xs font-sans font-bold text-brand">
                                            {{ class_basename($log->entity_type) }}
                                        </div>
                                        @if ($log->entity_id)
                                            <div class="text-[10px] font-mono text-ink/50">
                                                #{{ $log->entity_id }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs font-sans text-ink/50 italic">Umum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-left border-b border-ink/10 max-w-xs">
                                    <span class="text-xs font-sans text-ink line-clamp-2" title="{{ $log->description }}">
                                        {{ $log->description ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <span class="text-xs font-mono font-bold text-ink/80 bg-surface-muted px-2 py-0.5 border border-ink/20">
                                        {{ $log->ip_address ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <x-action-button variant="detail" label="Lihat detail log" wire:click="openDetailModal({{ $log->id }})" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif

    @if ($showFilterModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="filter-modal-title">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">PILIHAN FILTER</span>
                        <h3 id="filter-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            FILTER AUDIT LOG
                        </h3>
                    </div>
                    <button wire:click="closeFilterModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-4 sm:space-y-5 overflow-y-auto flex-1 min-w-0">
                    @if ($isSuperAdmin)
                        <div class="space-y-1.5">
                            <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Aktor / Pengguna
                            </label>
                            <select wire:model.live="actorFilter"
                                    class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                                <option value="all">Semua Aktor</option>
                                <option value="system">Sistem (Otomatis)</option>
                                @foreach ($actorList as $actor)
                                    <option value="{{ $actor->id }}">{{ $actor->email }} ({{ strtoupper($actor->role) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Role / Peran
                            </label>
                            <select wire:model.live="roleFilter"
                                    class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                                <option value="all">Semua Role</option>
                                <option value="super_admin">Super Admin</option>
                                <option value="admin">Admin KPR</option>
                                <option value="voter">Pemilih / Mahasiswa</option>
                                <option value="system">Sistem Otomatis</option>
                            </select>
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Tipe Aksi
                        </label>
                        <select wire:model.live="actionFilter"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                            <option value="all">Semua Aksi</option>
                            @foreach ($actionList as $act)
                                <option value="{{ $act }}">{{ strtoupper(str_replace('_', ' ', $act)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Modul / Entitas
                        </label>
                        <select wire:model.live="moduleFilter"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                            <option value="all">Semua Modul</option>
                            @if ($isSuperAdmin)
                                <option value="general">Umum / Sistem</option>
                            @endif
                            @foreach ($moduleList as $mod)
                                <option value="{{ $mod }}">{{ class_basename($mod) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Rentang Tanggal
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-sans font-bold text-ink/60 uppercase block mb-1">Dari Tanggal</span>
                                <input type="date"
                                       wire:model.live="startDate"
                                       aria-label="Dari tanggal"
                                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            </div>
                            <div>
                                <span class="text-[10px] font-sans font-bold text-ink/60 uppercase block mb-1">Sampai Tanggal</span>
                                <input type="date"
                                       wire:model.live="endDate"
                                       aria-label="Sampai tanggal"
                                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                    <button wire:click="resetFilters"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-red-50 text-red-700 border-2 border-ink font-display font-black text-xs uppercase tracking-wider text-center transition-colors cursor-pointer">
                        RESET
                    </button>

                    <button wire:click="closeFilterModal"
                            type="button"
                            class="w-full sm:w-auto px-5 py-2.5 bg-brand text-surface hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider text-center transition-all cursor-pointer">
                        TERAPKAN FILTER
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showDetailModal && $detail)
        @php
            $detailActionLower = strtolower($detail->action);
            $detailActionBadgeClass = match (true) {
                str_contains($detailActionLower, 'create') || str_contains($detailActionLower, 'import') || str_contains($detailActionLower, 'store') => 'bg-emerald-200 text-emerald-950 border border-ink',
                str_contains($detailActionLower, 'update') || str_contains($detailActionLower, 'edit') || str_contains($detailActionLower, 'toggle') => 'bg-amber-200 text-amber-950 border border-ink',
                str_contains($detailActionLower, 'delete') || str_contains($detailActionLower, 'destroy') => 'bg-red-100 text-red-900 border border-ink',
                str_contains($detailActionLower, 'login') || str_contains($detailActionLower, 'logout') => 'bg-brand text-accent border border-ink',
                default => 'bg-surface-muted text-ink border border-ink',
            };
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="detail-modal-title">
            <div class="w-full sm:max-w-2xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">LOG AKTIVITAS #{{ $detail->id }}</span>
                        <h3 id="detail-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            DETAIL AUDIT LOG
                        </h3>
                    </div>
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup detail modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2 p-3 bg-surface-muted border-2 border-ink shadow-brutal-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-display font-bold uppercase text-ink/60">TIPE AKSI:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-display font-black uppercase {{ $detailActionBadgeClass }}">
                                {{ strtoupper(str_replace('_', ' ', $detail->action)) }}
                            </span>
                        </div>
                        <div class="text-xs font-mono font-bold text-ink/70">
                            ID: #{{ $detail->id }}
                        </div>
                    </div>

                    <div class="bg-surface border-2 border-ink p-3.5 shadow-brutal-sm space-y-1">
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">DESKRIPSI AKTIVITAS</span>
                        <p class="font-sans font-bold text-sm text-ink leading-relaxed">
                            {{ $detail->description ?: 'Tidak ada deskripsi yang tercatat.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">WAKTU (WITA)</span>
                            <div class="font-mono font-bold text-xs sm:text-sm text-ink">
                                {{ $detail->created_at->timezone('Asia/Makassar')->format('d F Y, H:i:s') }} WITA
                            </div>
                            <div class="text-[11px] font-sans text-ink/60">
                                {{ $detail->created_at->timezone('Asia/Makassar')->diffForHumans() }}
                            </div>
                        </div>

                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">AKTOR / PENGGUNA</span>
                            @if ($detail->user)
                                <div class="font-sans font-bold text-xs sm:text-sm text-brand truncate" title="{{ $detail->user->email }}">
                                    {{ $detail->user->email }}
                                </div>
                                <div class="text-[11px] font-sans text-ink/60">
                                    Role: {{ strtoupper($detail->user->role) }} &middot; User ID #{{ $detail->user_id }}
                                </div>
                            @else
                                <div class="font-display font-bold text-xs sm:text-sm text-ink">
                                    Sistem Otomatis
                                </div>
                                <div class="text-[11px] font-sans text-ink/60">
                                    Aksi background / scheduled task
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">MODUL & TARGET</span>
                            <div class="font-sans font-bold text-xs sm:text-sm text-ink">
                                {{ $detail->entity_type ? class_basename($detail->entity_type) : 'Umum / Sistem' }}
                            </div>
                            <div class="text-[11px] font-mono text-ink/60">
                                Target ID: {{ $detail->entity_id ?: '—' }}
                            </div>
                        </div>

                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">IP ADDRESS</span>
                            <div class="font-mono font-bold text-xs sm:text-sm text-brand">
                                {{ $detail->ip_address ?: '—' }}
                            </div>
                            <div class="text-[11px] font-sans text-ink/60">
                                Alamat jaringan pencatat
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">USER AGENT / PERANGKAT</span>
                        <div class="text-xs font-mono text-ink/80 wrap-break-word leading-relaxed">
                            {{ $detail->user_agent ?: '—' }}
                        </div>
                    </div>

                    <div class="bg-surface border-2 border-ink p-3.5 shadow-brutal-sm space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60">DATA METADATA</span>
                            @if (! empty($detail->metadata))
                                <span class="text-[10px] font-mono bg-accent/30 border border-ink/40 px-2 py-0.5 font-bold text-brand">
                                    {{ count($detail->metadata) }} field
                                </span>
                            @endif
                        </div>

                        @if (! empty($detail->metadata) && is_array($detail->metadata))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                @foreach ($detail->metadata as $key => $val)
                                    <div class="bg-surface-muted p-2.5 border border-ink/20 text-xs font-mono">
                                        <span class="text-ink/60 block uppercase font-bold text-[10px] truncate mb-0.5">
                                            {{ str_replace('_', ' ', $key) }}
                                        </span>
                                        <span class="font-bold text-ink wrap-break-word">
                                            @if (is_bool($val))
                                                {{ $val ? 'TRUE' : 'FALSE' }}
                                            @elseif (is_array($val))
                                                {{ json_encode($val) }}
                                            @elseif (is_null($val))
                                                NULL
                                            @else
                                                {{ $val }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <details class="text-xs font-mono pt-1">
                                <summary class="cursor-pointer text-brand font-bold uppercase tracking-wider py-1 hover:underline">
                                    Format JSON Mentah
                                </summary>
                                <pre class="mt-2 p-3 bg-ink text-accent text-xs font-mono overflow-x-auto border-2 border-ink shadow-brutal-sm">{{ json_encode($detail->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @else
                            <div class="text-xs font-sans text-ink/50 italic py-1">
                                Tidak ada metadata tambahan yang tercatat pada log ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-end bg-surface-muted shrink-0">
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-surface hover:bg-accent border-2 border-ink font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                        TUTUP
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
