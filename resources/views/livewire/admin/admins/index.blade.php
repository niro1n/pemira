<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>HAK AKSES SUPER ADMIN</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                MANAJEMEN ADMIN
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                Pengelolaan akun administrator dan hak akses operasional sistem PEMIRA.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <button wire:click="openInvitationsModal"
                    type="button"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-surface hover:bg-surface-muted border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider text-ink transition-all cursor-pointer min-h-10.5">
                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span>RIWAYAT UNDANGAN</span>
                @if ($statistics['pending_invitations_count'] > 0)
                    <span class="inline-flex items-center justify-center px-1.5 py-0.2 bg-amber-400 text-ink text-[10px] font-black border border-ink">
                        {{ $statistics['pending_invitations_count'] }}
                    </span>
                @endif
            </button>

            <button wire:click="openInviteModal"
                    type="button"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>UNDANG ADMIN</span>
            </button>
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

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal flex flex-col justify-between">
            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">TOTAL AKUN</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="font-display font-black text-2xl sm:text-3xl text-brand">{{ $statistics['total'] }}</span>
                <span class="text-xs font-sans font-bold text-ink/60 uppercase">ADMIN</span>
            </div>
        </div>

        <div class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal flex flex-col justify-between">
            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">ADMIN KPR</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="font-display font-black text-2xl sm:text-3xl text-ink">{{ $statistics['admin_count'] }}</span>
                <span class="text-xs font-sans font-bold text-ink/60 uppercase">OPERASIONAL</span>
            </div>
        </div>

        <div class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal flex flex-col justify-between">
            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">SUPER ADMIN</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="font-display font-black text-2xl sm:text-3xl text-brand">{{ $statistics['super_admin_count'] }}</span>
                <span class="text-xs font-sans font-bold text-accent-dark uppercase">KONTROL PENUH</span>
            </div>
        </div>

        <div class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal flex flex-col justify-between">
            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">STATUS AKTIF</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="font-display font-black text-2xl sm:text-3xl text-emerald-800">{{ $statistics['active_count'] }}</span>
                <span class="text-xs font-sans font-bold text-ink/60 uppercase">DARI {{ $statistics['total'] }}</span>
            </div>
        </div>

        <button type="button"
                wire:click="openInvitationsModal"
                class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal flex flex-col justify-between col-span-2 sm:col-span-1 text-left hover:bg-accent/10 transition-colors cursor-pointer group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">UNDANGAN PENDING</span>
                <span class="text-[10px] font-display font-black uppercase text-brand group-hover:underline flex items-center gap-1">
                    <span>LIHAT</span>
                    <span aria-hidden="true">&rarr;</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between mt-2">
                <span class="font-display font-black text-2xl sm:text-3xl text-amber-800">{{ $statistics['pending_invitations_count'] }}</span>
                <span class="text-xs font-sans font-bold text-amber-800/80 uppercase">MENUNGGU</span>
            </div>
        </button>
    </div>

    <div class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 min-w-0">
        <div class="relative flex-1 min-w-0 sm:min-w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink/40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari admin (email atau nama)..."
                   class="w-full pl-9 pr-8 py-2 bg-surface-muted border-2 border-ink text-xs sm:text-sm font-sans font-bold text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface" />
            @if ($search !== '')
                <button wire:click="$set('search', '')"
                        type="button"
                        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-ink/50 hover:text-ink cursor-pointer"
                        aria-label="Bersihkan pencarian">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button wire:click="openFilterModal"
                    type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-surface-muted hover:bg-accent border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase tracking-wider text-ink transition-colors cursor-pointer min-h-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>FILTER</span>
                @if ($activeFilterCount > 0)
                    <span class="inline-flex items-center justify-center px-1.5 py-0.2 bg-brand text-surface text-[10px] font-black border border-ink">
                        {{ $activeFilterCount }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    @if ($activeFilterCount > 0)
        <div class="flex flex-wrap items-center gap-2 p-2.5 bg-surface border-2 border-ink shadow-brutal-sm text-xs font-sans">
            <span class="font-display font-bold uppercase text-[11px] text-ink/60">Filter Aktif:</span>

            @if ($roleFilter !== 'all')
                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-surface-muted border border-ink text-xs font-bold text-ink">
                    <span>Role: {{ $roleFilter === 'super_admin' ? 'Super Admin' : 'Admin KPR' }}</span>
                    <button wire:click="clearFilter('role')" type="button" class="hover:text-red-700 cursor-pointer" aria-label="Hapus filter role">&times;</button>
                </span>
            @endif

            @if ($statusFilter !== 'all')
                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-surface-muted border border-ink text-xs font-bold text-ink">
                    <span>Status: {{ $statusFilter === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
                    <button wire:click="clearFilter('status')" type="button" class="hover:text-red-700 cursor-pointer" aria-label="Hapus filter status">&times;</button>
                </span>
            @endif

            @if ($startDate !== '' || $endDate !== '')
                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-surface-muted border border-ink text-xs font-bold text-ink">
                    <span>Tanggal: {{ $startDate ?: '...' }} s/d {{ $endDate ?: '...' }}</span>
                    <button wire:click="clearFilter('date')" type="button" class="hover:text-red-700 cursor-pointer" aria-label="Hapus filter tanggal">&times;</button>
                </span>
            @endif

            <button wire:click="resetFilters"
                    type="button"
                    class="ml-auto text-[11px] font-display font-bold uppercase text-red-700 hover:text-red-900 underline cursor-pointer">
                RESET FILTER
            </button>
        </div>
    @endif

    <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden min-w-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-135">
                <thead>
                    <tr class="border-b-2 border-ink bg-surface-muted">
                        <th class="p-3 sm:p-4 text-xs font-display font-black uppercase tracking-wider text-ink">
                            ADMIN
                        </th>
                        <th class="p-3 sm:p-4 text-xs font-display font-black uppercase tracking-wider text-ink">
                            ROLE
                        </th>
                        <th class="p-3 sm:p-4 text-xs font-display font-black uppercase tracking-wider text-ink">
                            STATUS
                        </th>
                        <th class="p-3 sm:p-4 text-xs font-display font-black uppercase tracking-wider text-ink cursor-pointer select-none hover:bg-ink/5"
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center gap-1">
                                <span>DIBUAT PADA</span>
                                @if ($sortField === 'created_at')
                                    <span class="font-black">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-3 sm:p-4 text-xs font-display font-black uppercase tracking-wider text-ink text-right">
                            AKSI
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink">
                    @forelse ($admins as $admin)
                        <tr class="hover:bg-ink/5 transition-colors font-sans {{ $admin->id === auth()->id() ? 'bg-accent/5' : '' }}">
                            <td class="p-3 sm:p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 border-2 border-ink bg-surface flex items-center justify-center font-display font-black text-xs uppercase shadow-brutal-sm shrink-0 {{ $admin->isSuperAdmin() ? 'bg-accent text-ink' : 'bg-brand text-surface' }}">
                                        {{ substr($admin->email, 0, 2) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-display font-bold text-xs sm:text-sm text-ink truncate block">
                                                {{ $admin->getAdminDisplayName() }}
                                            </span>
                                            @if ($admin->id === auth()->id())
                                                <span class="text-[10px] px-1.5 py-0.2 bg-accent/30 border border-ink font-display font-bold uppercase tracking-wider text-ink">
                                                    Anda
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-ink/60 font-medium truncate block mt-0.5">
                                            {{ $admin->email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-3 sm:p-4 whitespace-nowrap">
                                @if ($admin->isSuperAdmin())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-accent border border-ink text-[11px] font-display font-black uppercase tracking-wider text-ink shadow-brutal-xs">
                                        <span class="w-1.5 h-1.5 bg-brand inline-block"></span>
                                        SUPER ADMIN
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-[11px] font-display font-bold uppercase tracking-wider text-ink">
                                        <span class="w-1.5 h-1.5 bg-ink/50 inline-block"></span>
                                        ADMIN KPR
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 sm:p-4 whitespace-nowrap">
                                @if ($admin->email_verified_at !== null)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-200 text-emerald-950 border border-ink text-xs font-display font-bold uppercase">
                                        <span class="w-1.5 h-1.5 bg-emerald-700 inline-block"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-rose-200 text-rose-950 border border-ink text-xs font-display font-bold uppercase">
                                        <span class="w-1.5 h-1.5 bg-rose-700 inline-block"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 sm:p-4 whitespace-nowrap text-xs font-bold text-ink/80">
                                <div>{{ $admin->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[11px] text-ink/50 font-normal">{{ $admin->created_at->translatedFormat('H:i') }} WITA</div>
                            </td>

                            <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1 sm:gap-1.5 justify-end">
                                    <button wire:click="openDetailModal({{ $admin->id }})"
                                            type="button"
                                            class="w-8 h-8 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center transition-colors shadow-brutal-sm cursor-pointer"
                                            title="Lihat Detail Admin"
                                            aria-label="Lihat detail admin {{ $admin->email }}">
                                        <svg class="w-3.5 h-3.5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <button wire:click="openEditModal({{ $admin->id }})"
                                            type="button"
                                            class="w-8 h-8 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center transition-colors shadow-brutal-sm cursor-pointer"
                                            title="Ubah Data Admin"
                                            aria-label="Ubah data admin {{ $admin->email }}">
                                        <svg class="w-3.5 h-3.5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>

                                    @if ($admin->id !== auth()->id())
                                        <button wire:click="openToggleStatusModal({{ $admin->id }})"
                                                type="button"
                                                class="w-8 h-8 border-2 border-ink {{ $admin->email_verified_at !== null ? 'bg-amber-100 hover:bg-amber-200 text-amber-950' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-950' }} flex items-center justify-center transition-colors shadow-brutal-sm cursor-pointer"
                                                title="{{ $admin->email_verified_at !== null ? 'Nonaktifkan Admin' : 'Aktifkan Admin' }}"
                                                aria-label="{{ $admin->email_verified_at !== null ? 'Nonaktifkan admin' : 'Aktifkan admin' }} {{ $admin->email }}">
                                            @if ($admin->email_verified_at !== null)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>

                                        @if (! $admin->isSuperAdmin())
                                            <button wire:click="openDeleteModal({{ $admin->id }})"
                                                    type="button"
                                                    class="w-8 h-8 border-2 border-ink bg-surface hover:bg-rose-100 text-rose-800 flex items-center justify-center transition-colors shadow-brutal-sm cursor-pointer"
                                                    title="Hapus Admin"
                                                    aria-label="Hapus admin {{ $admin->email }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 bg-surface-muted border-2 border-ink flex items-center justify-center mx-auto shadow-brutal-sm">
                                        <svg class="w-6 h-6 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <div class="font-display font-black text-sm uppercase text-ink">
                                        Tidak Ada Data Admin
                                    </div>
                                    <p class="text-xs font-sans text-ink/60">
                                        @if ($search !== '' || $activeFilterCount > 0)
                                            Tidak ditemukan akun admin yang cocok dengan kriteria pencarian atau filter aktif.
                                        @else
                                            Belum ada akun admin tambahan yang terdaftar dalam sistem.
                                        @endif
                                    </p>
                                    @if ($search !== '' || $activeFilterCount > 0)
                                        <button wire:click="resetFilters"
                                                type="button"
                                                class="px-4 py-2 bg-surface border-2 border-ink text-xs font-display font-bold uppercase shadow-brutal-sm hover:bg-accent cursor-pointer">
                                            RESET PENCARIAN & FILTER
                                        </button>
                                    @else
                                        <button wire:click="openCreateModal"
                                                type="button"
                                                class="px-4 py-2 bg-brand text-accent border-2 border-ink text-xs font-display font-black uppercase shadow-brutal-sm hover:bg-brand-dark cursor-pointer">
                                            + TAMBAH ADMIN BARU
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($admins->hasPages())
            <div class="p-3 sm:p-4 border-t-2 border-ink bg-surface-muted">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

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
                            FILTER DATA ADMIN
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

                <div class="p-4 sm:p-5 space-y-4 sm:space-y-5 overflow-y-auto flex-1 min-w-0 font-sans">
                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Role Administrator
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-surface-muted border-2 border-ink">
                            <button type="button"
                                    wire:click="$set('roleFilter', 'all')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $roleFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Semua
                            </button>
                            <button type="button"
                                    wire:click="$set('roleFilter', 'admin')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $roleFilter === 'admin' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Admin KPR
                            </button>
                            <button type="button"
                                    wire:click="$set('roleFilter', 'super_admin')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $roleFilter === 'super_admin' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Super Admin
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Status Akun
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-surface-muted border-2 border-ink">
                            <button type="button"
                                    wire:click="$set('statusFilter', 'all')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $statusFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Semua
                            </button>
                            <button type="button"
                                    wire:click="$set('statusFilter', 'active')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Aktif
                            </button>
                            <button type="button"
                                    wire:click="$set('statusFilter', 'inactive')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $statusFilter === 'inactive' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Nonaktif
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Rentang Tanggal Dibuat
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

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <button wire:click="resetFilters"
                            type="button"
                            class="px-4 py-2 bg-surface hover:bg-red-50 text-red-700 border-2 border-ink font-display font-black text-xs uppercase tracking-wider transition-colors cursor-pointer">
                        RESET
                    </button>

                    <button wire:click="closeFilterModal"
                            type="button"
                            class="px-5 py-2.5 bg-brand text-surface hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        TERAPKAN FILTER
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="create-modal-title">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">FORMULIR</span>
                        <h3 id="create-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            TAMBAH AKUN ADMIN
                        </h3>
                    </div>
                    <button wire:click="closeCreateModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="createAdmin" class="flex flex-col flex-1 min-h-0 overflow-hidden font-sans">
                    <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div class="space-y-1.5">
                            <label for="create-email" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Alamat Email <span class="text-red-600">*</span>
                            </label>
                            <input type="email"
                                   id="create-email"
                                   wire:model="email"
                                   placeholder="contoh: kpr.admin@pnb.ac.id"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            @error('email')
                                <span class="text-xs font-bold text-red-600 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="create-password" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Kata Sandi <span class="text-red-600">*</span>
                            </label>
                            <input type="password"
                                   id="create-password"
                                   wire:model="password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            @error('password')
                                <span class="text-xs font-bold text-red-600 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="create-password-confirmation" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Konfirmasi Kata Sandi <span class="text-red-600">*</span>
                            </label>
                            <input type="password"
                                   id="create-password-confirmation"
                                   wire:model="password_confirmation"
                                   placeholder="Ulangi kata sandi"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        </div>

                        <div class="p-3 bg-surface-muted border-2 border-ink flex items-center justify-between">
                            <div>
                                <span class="font-display font-bold text-xs uppercase text-ink block">Status Akun Aktif</span>
                                <span class="text-[11px] text-ink/60 block">Akun langsung dapat digunakan untuk masuk panel admin</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="isActive" class="sr-only peer">
                                <div class="w-11 h-6 bg-surface border-2 border-ink peer-focus:outline-none peer-checked:after:translate-x-full peer-checked:after:border-ink after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-ink after:border-2 after:border-ink after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </div>
                    </div>

                    <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                        <button wire:click="closeCreateModal"
                                type="button"
                                class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                            BATAL
                        </button>

                        <button type="submit"
                                class="px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            SIMPAN ADMIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showEditModal && $selectedAdmin)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="edit-modal-title">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">EDIT DATA</span>
                        <h3 id="edit-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            UBAH AKUN ADMIN
                        </h3>
                    </div>
                    <button wire:click="closeEditModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateAdmin" class="flex flex-col flex-1 min-h-0 overflow-hidden font-sans">
                    <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div class="space-y-1.5">
                            <label for="edit-email" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Alamat Email <span class="text-red-600">*</span>
                            </label>
                            <input type="email"
                                   id="edit-email"
                                   wire:model="email"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            @error('email')
                                <span class="text-xs font-bold text-red-600 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="p-3 bg-amber-50 border border-ink text-xs text-amber-950 space-y-1">
                            <span class="font-bold block">Ganti Kata Sandi (Opsional):</span>
                            <p class="text-[11px] text-ink/70">Biarkan kolom kata sandi kosong jika tidak ingin mengubah kata sandi akun ini.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="edit-password" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Kata Sandi Baru
                            </label>
                            <input type="password"
                                   id="edit-password"
                                   wire:model="password"
                                   placeholder="Kosongi jika tidak ingin diubah"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            @error('password')
                                <span class="text-xs font-bold text-red-600 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="edit-password-confirmation" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input type="password"
                                   id="edit-password-confirmation"
                                   wire:model="password_confirmation"
                                   placeholder="Ulangi kata sandi baru"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        </div>

                        <div class="p-3 bg-surface-muted border-2 border-ink flex items-center justify-between">
                            <div>
                                <span class="font-display font-bold text-xs uppercase text-ink block">Status Akun Aktif</span>
                                <span class="text-[11px] text-ink/60 block">Akun dapat mengakses login panel operasional</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="isActive" class="sr-only peer">
                                <div class="w-11 h-6 bg-surface border-2 border-ink peer-focus:outline-none peer-checked:after:translate-x-full peer-checked:after:border-ink after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-ink after:border-2 after:border-ink after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </div>
                        @error('isActive')
                            <span class="text-xs font-bold text-red-600 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                        <button wire:click="closeEditModal"
                                type="button"
                                class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                            BATAL
                        </button>

                        <button type="submit"
                                class="px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDetailModal && $selectedAdmin)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="detail-modal-title">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">INFORMASI LENGKAP</span>
                        <h3 id="detail-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            DETAIL AKUN ADMIN
                        </h3>
                    </div>
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0 font-sans">
                    <div class="p-4 bg-surface-muted border-2 border-ink flex items-center gap-3">
                        <div class="w-12 h-12 border-2 border-ink bg-surface flex items-center justify-center font-display font-black text-base uppercase shadow-brutal-sm shrink-0 {{ $selectedAdmin->isSuperAdmin() ? 'bg-accent text-ink' : 'bg-brand text-surface' }}">
                            {{ substr($selectedAdmin->email, 0, 2) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-display font-black text-base text-ink uppercase truncate">
                                {{ $selectedAdmin->getAdminDisplayName() }}
                            </h4>
                            <span class="text-xs font-medium text-ink/70 truncate block mt-0.5">
                                {{ $selectedAdmin->email }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-surface border border-ink">
                            <span class="font-display font-bold uppercase text-[10px] text-ink/60 block mb-1">Peran Akses</span>
                            @if ($selectedAdmin->isSuperAdmin())
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-accent border border-ink font-display font-black text-[11px] uppercase text-ink">
                                    SUPER ADMIN
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-surface-muted border border-ink font-display font-bold text-[11px] uppercase text-ink">
                                    ADMIN KPR
                                </span>
                            @endif
                        </div>

                        <div class="p-3 bg-surface border border-ink">
                            <span class="font-display font-bold uppercase text-[10px] text-ink/60 block mb-1">Status Autentikasi</span>
                            @if ($selectedAdmin->email_verified_at !== null)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-200 text-emerald-950 border border-ink font-display font-bold text-[11px] uppercase">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-rose-200 text-rose-950 border border-ink font-display font-bold text-[11px] uppercase">
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                        <div class="p-3 bg-surface border border-ink">
                            <span class="font-display font-bold uppercase text-[10px] text-ink/60 block mb-1">Tanggal Dibuat</span>
                            <span class="font-bold text-ink block">{{ $selectedAdmin->created_at->translatedFormat('d F Y, H:i') }} WITA</span>
                        </div>

                        <div class="p-3 bg-surface border border-ink">
                            <span class="font-display font-bold uppercase text-[10px] text-ink/60 block mb-1">Pembaruan Terakhir</span>
                            <span class="font-bold text-ink block">{{ $selectedAdmin->updated_at->translatedFormat('d F Y, H:i') }} WITA</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="font-display font-black text-xs uppercase tracking-wider text-ink block">
                            Aktivitas Log Terakhir
                        </span>

                        @if (count($recentAuditLogs) > 0)
                            <div class="space-y-1.5">
                                @foreach ($recentAuditLogs as $log)
                                    <div class="p-2.5 bg-surface-muted border border-ink text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-display font-bold uppercase text-[10px] text-brand">
                                                {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                            <span class="text-[10px] text-ink/50 font-sans">
                                                {{ $log->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-ink/80 leading-snug">
                                            {{ $log->description }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-3 bg-surface-muted border border-ink text-xs text-ink/60 text-center">
                                Belum ada riwayat audit log tercatat untuk admin ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-end bg-surface-muted shrink-0">
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="px-5 py-2 bg-brand text-surface hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        TUTUP
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showToggleStatusModal && $selectedAdmin)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="toggle-modal-title">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">KONFIRMASI STATUS</span>
                        <h3 id="toggle-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            {{ $selectedAdmin->email_verified_at !== null ? 'NONAKTIFKAN ADMIN' : 'AKTIFKAN ADMIN' }}
                        </h3>
                    </div>
                    <button wire:click="closeToggleStatusModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-3 font-sans">
                    <p class="text-xs sm:text-sm text-ink leading-relaxed">
                        Anda akan mengubah status akun admin <strong class="font-bold text-brand">{{ $selectedAdmin->email }}</strong> menjadi:
                        @if ($selectedAdmin->email_verified_at !== null)
                            <strong class="font-bold text-rose-700">NONAKTIF</strong>.
                        @else
                            <strong class="font-bold text-emerald-700">AKTIF</strong>.
                        @endif
                    </p>

                    @if ($selectedAdmin->email_verified_at !== null)
                        <div class="p-3 bg-amber-50 border border-ink text-xs text-amber-950 space-y-1">
                            <span class="font-bold block">Konsekuensi:</span>
                            <p class="text-[11px] text-ink/80">Admin ini tidak akan dapat login ke panel admin PEMIRA selama akun berstatus nonaktif.</p>
                        </div>
                    @else
                        <div class="p-3 bg-emerald-50 border border-ink text-xs text-emerald-950 space-y-1">
                            <span class="font-bold block">Konsekuensi:</span>
                            <p class="text-[11px] text-ink/80">Admin ini dapat langsung menggunakan kredensial email dan kata sandi untuk masuk panel admin.</p>
                        </div>
                    @endif
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <button wire:click="closeToggleStatusModal"
                            type="button"
                            class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                        BATAL
                    </button>

                    <button wire:click="toggleStatus"
                            type="button"
                            class="px-5 py-2.5 {{ $selectedAdmin->email_verified_at !== null ? 'bg-rose-700 hover:bg-rose-800 text-surface' : 'bg-brand hover:bg-brand-dark text-accent' }} border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        {{ $selectedAdmin->email_verified_at !== null ? 'YA, NONAKTIFKAN' : 'YA, AKTIFKAN' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal && $selectedAdmin)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="delete-modal-title">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-rose-700 block">TINDAKAN PERMANEN</span>
                        <h3 id="delete-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            HAPUS AKUN ADMIN
                        </h3>
                    </div>
                    <button wire:click="closeDeleteModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-3 font-sans">
                    <p class="text-xs sm:text-sm text-ink leading-relaxed">
                        Apakah Anda yakin ingin menghapus akun admin <strong class="font-bold text-rose-800">{{ $selectedAdmin->email }}</strong>?
                    </p>

                    <div class="p-3 bg-rose-50 border border-ink text-xs text-rose-950 space-y-1">
                        <span class="font-bold block">Peringatan:</span>
                        <p class="text-[11px] text-ink/80">Tindakan ini permanen dan tidak dapat dibatalkan. Seluruh data sesi dan akses admin ini akan dicabut secara seketika.</p>
                    </div>
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <button wire:click="closeDeleteModal"
                            type="button"
                            class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                        BATAL
                    </button>

                    <button wire:click="deleteAdmin"
                            type="button"
                            class="px-5 py-2.5 bg-rose-700 hover:bg-rose-800 text-surface border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        HAPUS PERMANEN
                    </button>
                </div>
            </div>
        </div>
    @endif
    @if ($showInvitationsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="invitations-modal-title">
            <div class="w-full sm:max-w-5xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <!-- Header -->
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div class="min-w-0 flex-1 mr-3">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-brand text-accent border border-ink text-[10px] font-sans font-bold uppercase tracking-wider mb-1">
                            <span>INVITATION TRACKING</span>
                        </div>
                        <h3 id="invitations-modal-title" class="font-display font-black text-base sm:text-xl text-brand uppercase truncate min-w-0">
                            DAFTAR UNDANGAN ADMIN (ADMIN INVITATIONS)
                        </h3>
                        <p class="text-xs font-sans text-ink/70 mt-0.5 truncate">
                            Riwayat undangan calon admin melalui email. Token pendaftaran berlaku 24 jam & satu kali pakai.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="openInviteModal"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden sm:inline">UNDANG ADMIN BARU</span>
                            <span class="sm:hidden">UNDANG</span>
                        </button>
                        <button wire:click="closeInvitationsModal"
                                type="button"
                                class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                                aria-label="Tutup modal">
                            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Search & Filter bar -->
                <div class="p-3 sm:p-4 border-b-2 border-ink bg-surface flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
                    <div class="relative flex-1 min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="invitationSearch"
                               placeholder="Cari email calon admin..."
                               class="w-full pl-9 pr-8 py-2 bg-surface-muted border-2 border-ink text-xs sm:text-sm font-sans font-bold text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        @if ($invitationSearch !== '')
                            <button wire:click="$set('invitationSearch', '')"
                                    type="button"
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-ink/50 hover:text-ink cursor-pointer"
                                    aria-label="Bersihkan pencarian">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-1 p-1 bg-surface-muted border-2 border-ink overflow-x-auto shrink-0">
                        <button type="button"
                                wire:click="$set('invitationStatusFilter', 'all')"
                                class="px-2.5 py-1 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $invitationStatusFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                            Semua
                        </button>
                        <button type="button"
                                wire:click="$set('invitationStatusFilter', 'pending')"
                                class="px-2.5 py-1 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $invitationStatusFilter === 'pending' ? 'bg-amber-400 text-ink shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                            Pending
                        </button>
                        <button type="button"
                                wire:click="$set('invitationStatusFilter', 'accepted')"
                                class="px-2.5 py-1 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $invitationStatusFilter === 'accepted' ? 'bg-emerald-400 text-ink shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                            Diterima
                        </button>
                        <button type="button"
                                wire:click="$set('invitationStatusFilter', 'expired')"
                                class="px-2.5 py-1 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $invitationStatusFilter === 'expired' ? 'bg-ink/20 text-ink shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                            Kedaluwarsa
                        </button>
                        <button type="button"
                                wire:click="$set('invitationStatusFilter', 'revoked')"
                                class="px-2.5 py-1 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $invitationStatusFilter === 'revoked' ? 'bg-rose-400 text-ink shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                            Dibatalkan
                        </button>
                    </div>
                </div>

                <!-- Table Content (Scrollable) -->
                <div class="overflow-y-auto overflow-x-auto flex-1 min-w-0">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm font-sans min-w-175">
                        <thead class="sticky top-0 z-10">
                            <tr class="border-b-2 border-ink bg-surface-muted text-ink font-display font-black text-xs uppercase tracking-wider">
                                <th class="p-3 sm:p-3.5">EMAIL CALON ADMIN</th>
                                <th class="p-3 sm:p-3.5">STATUS</th>
                                <th class="p-3 sm:p-3.5">DIUNDANG OLEH</th>
                                <th class="p-3 sm:p-3.5">DIBUAT</th>
                                <th class="p-3 sm:p-3.5">KEDALUWARSA</th>
                                <th class="p-3 sm:p-3.5">DITERIMA</th>
                                <th class="p-3 sm:p-3.5 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-ink bg-surface">
                            @forelse ($adminInvitations as $invitation)
                                <tr class="hover:bg-surface-muted/50 transition-colors">
                                    <td class="p-3 sm:p-3.5">
                                        <span class="font-bold text-ink font-mono text-xs sm:text-sm">{{ $invitation->email }}</span>
                                    </td>
                                    <td class="p-3 sm:p-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 border text-[11px] font-display font-black uppercase {{ $invitation->status()->badgeClass() }}">
                                            {{ $invitation->status()->label() }}
                                        </span>
                                    </td>
                                    <td class="p-3 sm:p-3.5 font-bold text-ink/80 text-xs">
                                        {{ $invitation->inviter?->getAdminDisplayName() ?? $invitation->inviter?->email ?? 'Sistem' }}
                                    </td>
                                    <td class="p-3 sm:p-3.5 text-xs text-ink/70 whitespace-nowrap">
                                        {{ $invitation->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="p-3 sm:p-3.5 text-xs whitespace-nowrap {{ $invitation->isExpired() ? 'text-rose-700 font-bold' : 'text-ink/70' }}">
                                        {{ $invitation->expires_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="p-3 sm:p-3.5 text-xs text-ink/70 whitespace-nowrap">
                                        {{ $invitation->accepted_at ? $invitation->accepted_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="p-3 sm:p-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if ($invitation->isPending())
                                                <div x-data="{
                                                    seconds: {{ $this->getInvitationCooldownSeconds($invitation->id) }},
                                                    timer: null,
                                                    init() {
                                                        if (this.seconds > 0) {
                                                            this.startTimer();
                                                        }
                                                    },
                                                    startTimer() {
                                                        if (this.timer) clearInterval(this.timer);
                                                        this.timer = setInterval(() => {
                                                            if (this.seconds > 0) {
                                                                this.seconds--;
                                                            } else {
                                                                clearInterval(this.timer);
                                                                this.timer = null;
                                                            }
                                                        }, 1000);
                                                    }
                                                }"
                                                @invitation-cooldown-started.window="if ($event.detail.id === {{ $invitation->id }}) { seconds = $event.detail.seconds; startTimer(); }">
                                                    <button wire:click="resendInvitation({{ $invitation->id }})"
                                                            type="button"
                                                            :disabled="seconds > 0"
                                                            wire:loading.attr="disabled"
                                                            :class="seconds > 0 ? 'opacity-50 cursor-not-allowed bg-surface-muted hover:bg-surface-muted shadow-none' : 'bg-surface hover:bg-accent shadow-brutal-sm cursor-pointer'"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 border border-ink text-[11px] font-display font-bold uppercase transition-colors"
                                                            :title="seconds > 0 ? 'Harap tunggu ' + seconds + ' detik sebelum mengirim ulang' : 'Kirim ulang email undangan dengan token baru'">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        <span x-show="seconds === 0">KIRIM ULANG</span>
                                                        <span x-show="seconds > 0" x-text="'TUNGGU (' + seconds + 's)'" x-cloak></span>
                                                    </button>
                                                </div>

                                                <button wire:click="openRevokeModal({{ $invitation->id }})"
                                                        type="button"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface hover:bg-rose-100 text-rose-700 border border-ink text-[11px] font-display font-bold uppercase transition-colors shadow-brutal-sm cursor-pointer"
                                                        title="Batalkan undangan ini">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <span>BATALKAN</span>
                                                </button>
                                            @elseif ($invitation->isExpired())
                                                <div x-data="{
                                                    seconds: {{ $this->getInvitationCooldownSeconds($invitation->id) }},
                                                    timer: null,
                                                    init() {
                                                        if (this.seconds > 0) {
                                                            this.startTimer();
                                                        }
                                                    },
                                                    startTimer() {
                                                        if (this.timer) clearInterval(this.timer);
                                                        this.timer = setInterval(() => {
                                                            if (this.seconds > 0) {
                                                                this.seconds--;
                                                            } else {
                                                                clearInterval(this.timer);
                                                                this.timer = null;
                                                            }
                                                        }, 1000);
                                                    }
                                                }"
                                                @invitation-cooldown-started.window="if ($event.detail.id === {{ $invitation->id }}) { seconds = $event.detail.seconds; startTimer(); }">
                                                    <button wire:click="resendInvitation({{ $invitation->id }})"
                                                            type="button"
                                                            :disabled="seconds > 0"
                                                            wire:loading.attr="disabled"
                                                            :class="seconds > 0 ? 'opacity-50 cursor-not-allowed bg-surface-muted hover:bg-surface-muted shadow-none' : 'bg-surface hover:bg-accent shadow-brutal-sm cursor-pointer'"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 border border-ink text-[11px] font-display font-bold uppercase transition-colors"
                                                            :title="seconds > 0 ? 'Harap tunggu ' + seconds + ' detik sebelum mengirim ulang' : 'Kirim undangan baru'">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        <span x-show="seconds === 0">KIRIM ULANG</span>
                                                        <span x-show="seconds > 0" x-text="'TUNGGU (' + seconds + 's)'" x-cloak></span>
                                                    </button>
                                                </div>
                                            @elseif ($invitation->isAccepted())
                                                <span class="text-[11px] font-display font-bold text-emerald-700 uppercase">AKUN AKTIF</span>
                                            @elseif ($invitation->isRevoked())
                                                <span class="text-[11px] font-display font-bold text-rose-700 uppercase">DIBATALKAN</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <div class="w-10 h-10 bg-surface-muted border-2 border-ink flex items-center justify-center mx-auto shadow-brutal-sm">
                                                <svg class="w-5 h-5 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="font-display font-black text-sm uppercase text-ink">
                                                Tidak Ada Undangan Admin
                                            </div>
                                            <p class="text-xs font-sans text-ink/60">
                                                @if ($invitationSearch !== '' || $invitationStatusFilter !== 'all')
                                                    Tidak ditemukan data undangan yang sesuai dengan filter pencarian.
                                                @else
                                                    Belum ada undangan admin yang dikirimkan.
                                                @endif
                                            </p>
                                            <div class="pt-2">
                                                <button wire:click="openInviteModal"
                                                        type="button"
                                                        class="px-3.5 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider cursor-pointer">
                                                    + UNDANG ADMIN BARU
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination / Footer -->
                <div class="px-4 py-3 sm:px-6 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0 text-xs font-sans">
                    <div class="text-ink/60">
                        Total <strong>{{ $adminInvitations->total() }}</strong> undangan tercatat
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($adminInvitations->hasPages())
                            <div class="text-xs">
                                {{ $adminInvitations->links() }}
                            </div>
                        @endif

                        <button wire:click="closeInvitationsModal"
                                type="button"
                                class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                            TUTUP
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showInviteModal)
        <div class="fixed inset-0 z-60 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="invite-modal-title">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">UNDANGAN EMAIL</span>
                        <h3 id="invite-modal-title" class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            UNDANG CALON ADMIN
                        </h3>
                    </div>
                    <button wire:click="closeInviteModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="sendInvitation" class="flex flex-col flex-1 min-h-0 overflow-hidden font-sans">
                    <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div class="p-3 bg-accent/20 border-2 border-ink text-xs font-sans text-ink space-y-1">
                            <div class="flex items-center gap-2 font-display font-black uppercase text-brand">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Informasi Onboarding Admin</span>
                            </div>
                            <p class="text-ink/80 text-[11px] leading-relaxed">
                                Calon admin akan menerima email berisi tautan pendaftaran aman <strong>satu kali pakai</strong> (berlaku 24 jam). Calon admin akan mengisi nama lengkap dan menentukan kata sandi sendiri.
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="invite-email" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                                Alamat Email Calon Admin <span class="text-red-600">*</span>
                            </label>
                            <input type="email"
                                   id="invite-email"
                                   wire:model="inviteEmail"
                                   placeholder="contoh: calon.admin@pnb.ac.id"
                                   autofocus
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            @error('inviteEmail')
                                <span class="text-xs font-bold text-red-600 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                        <button wire:click="closeInviteModal"
                                type="button"
                                class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                            BATAL
                        </button>

                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer flex items-center gap-2">
                            <span wire:loading.remove wire:target="sendInvitation">KIRIM UNDANGAN</span>
                            <span wire:loading wire:target="sendInvitation" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>MENGIRIM...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showRevokeModal && $selectedInvitation)
        <div class="fixed inset-0 z-60 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs"
             role="dialog"
             aria-modal="true"
             aria-labelledby="revoke-modal-title">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-rose-100 shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-rose-900 block">KONFIRMASI PEMBATALAN</span>
                        <h3 id="revoke-modal-title" class="font-display font-black text-sm sm:text-lg text-rose-950 uppercase truncate min-w-0">
                            BATALKAN UNDANGAN
                        </h3>
                    </div>
                    <button wire:click="closeRevokeModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-4 font-sans text-xs sm:text-sm text-ink">
                    <p class="leading-relaxed">
                        Apakah Anda yakin ingin membatalkan undangan untuk calon admin:
                    </p>
                    <div class="p-3 bg-surface-muted border-2 border-ink font-bold font-mono text-xs sm:text-sm text-brand break-all">
                        {{ $selectedInvitation->email }}
                    </div>
                    <p class="text-ink/70 text-xs leading-relaxed">
                        Setelah dibatalkan, tautan pendaftaran yang telah dikirimkan tidak akan dapat digunakan lagi.
                    </p>
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <button wire:click="closeRevokeModal"
                            type="button"
                            class="px-4 py-2 bg-surface hover:bg-ink/10 border-2 border-ink font-display font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                        BATAL
                    </button>

                    <button wire:click="revokeInvitation"
                            type="button"
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-rose-600 text-surface hover:bg-rose-700 border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        <span wire:loading.remove wire:target="revokeInvitation">YA, BATALKAN UNDANGAN</span>
                        <span wire:loading wire:target="revokeInvitation">MEMPROSES...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
