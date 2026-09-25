<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>MASTER DATA PEMILIH</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                ELIGIBLE VOTERS
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                Data mahasiswa yang berhak mendaftar sebagai pemilih PEMIRA.
            </p>
        </div>

        <div class="flex flex-row items-center gap-2 sm:gap-3 shrink-0 w-full sm:w-auto">
            <button wire:click="openImportModal"
                    type="button"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-surface-muted hover:bg-accent text-ink border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span>IMPORT CSV</span>
            </button>

            <button wire:click="openCreateModal"
                    type="button"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ TAMBAH MAHASISWA</span>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-3 sm:p-4 bg-accent/20 border-2 border-ink shadow-brutal flex items-center justify-between gap-3 text-xs sm:text-sm font-sans font-bold text-ink min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 bg-brand shrink-0 inline-block"></span>
                <span class="wrap-break-word">{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="p-1 hover:bg-ink/10 text-ink shrink-0" aria-label="Tutup pesan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 sm:p-4 bg-red-100 border-2 border-ink shadow-brutal flex items-center justify-between gap-3 text-xs sm:text-sm font-sans font-bold text-red-900 min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 bg-red-600 shrink-0 inline-block"></span>
                <span class="wrap-break-word">{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="p-1 hover:bg-red-200 text-red-900 shrink-0" aria-label="Tutup pesan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-3">
        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-ink/60 block leading-tight">TOTAL ELIGIBLE</span>
            <div class="font-display font-black text-lg sm:text-2xl text-brand mt-0.5 sm:mt-1 truncate">
                {{ number_format($this->statistics['total'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Mahasiswa</span>
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-emerald-800 block leading-tight">DATA LENGKAP</span>
            <div class="font-display font-black text-lg sm:text-2xl text-emerald-700 mt-0.5 sm:mt-1 truncate">
                {{ number_format($this->statistics['complete'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Siap voting</span>
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-amber-800 block leading-tight">TIDAK LENGKAP</span>
            <div class="font-display font-black text-lg sm:text-2xl text-amber-700 mt-0.5 sm:mt-1 truncate">
                {{ number_format($this->statistics['incomplete'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Perlu dilengkapi</span>
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-brand block leading-tight">SUDAH TERDAFTAR</span>
            <div class="font-display font-black text-lg sm:text-2xl text-brand mt-0.5 sm:mt-1 truncate">
                {{ number_format($this->statistics['registered'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Memiliki akun</span>
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-brand block leading-tight">SUDAH VOTING</span>
            @if ($this->statistics['has_current_election'])
                <div class="font-display font-black text-lg sm:text-2xl text-brand mt-0.5 sm:mt-1 truncate">
                    {{ number_format($this->statistics['voted'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate" title="{{ $this->statistics['election_name'] }}">{{ $this->statistics['election_name'] }}</span>
            @else
                <div class="font-display font-bold text-[11px] sm:text-xs text-ink/50 mt-1 sm:mt-2 italic truncate">
                    Tidak ada PEMIRA
                </div>
            @endif
        </div>

        <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
            <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-tight sm:tracking-wider text-ink/70 block leading-tight">BELUM VOTING</span>
            @if ($this->statistics['has_current_election'])
                <div class="font-display font-black text-lg sm:text-2xl text-ink/80 mt-0.5 sm:mt-1 truncate">
                    {{ number_format($this->statistics['unvoted'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate" title="{{ $this->statistics['election_name'] }}">{{ $this->statistics['election_name'] }}</span>
            @else
                <div class="font-display font-bold text-[11px] sm:text-xs text-ink/50 mt-1 sm:mt-2 italic truncate">
                    Tidak ada PEMIRA
                </div>
            @endif
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
                       placeholder="Cari NIM atau Nama pemilih..."
                       aria-label="Cari pemilih"
                       class="w-full bg-surface-muted border-2 border-ink pl-9 pr-3 py-2 text-xs sm:text-sm font-sans font-medium text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="openFilterModal"
                        type="button"
                        aria-label="Buka filter pemilih"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10 {{ $this->activeFilterCount > 0 ? 'bg-accent text-ink' : 'bg-surface-muted hover:bg-accent text-ink' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>{{ $this->activeFilterCount > 0 ? 'FILTER · ' . $this->activeFilterCount : 'FILTER' }}</span>
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

        @if ($this->activeFilterCount > 0)
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-ink/10">
                <span class="text-xs font-display font-bold uppercase text-ink/60 tracking-wider">Filter Aktif:</span>

                @if (! empty($this->studyProgramFilter))
                    @php
                        $selectedSp = $this->studyPrograms->firstWhere('id', $this->studyProgramFilter);
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Jurusan: {{ $selectedSp?->name ?? 'Pilihan' }}</span>
                        <button wire:click="clearFilter('study_program')" type="button" aria-label="Hapus filter jurusan" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($this->completenessFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Status: {{ $this->completenessFilter === 'complete' ? 'Data Lengkap' : 'Data Tidak Lengkap' }}</span>
                        <button wire:click="clearFilter('completeness')" type="button" aria-label="Hapus filter kelengkapan" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($this->registrationFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Registrasi: {{ $this->registrationFilter === 'registered' ? 'Terdaftar' : 'Belum Terdaftar' }}</span>
                        <button wire:click="clearFilter('registration')" type="button" aria-label="Hapus filter registrasi" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($this->votingFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Voting: {{ $this->votingFilter === 'voted' ? 'Sudah Voting' : 'Belum Voting' }}</span>
                        <button wire:click="clearFilter('voting')" type="button" aria-label="Hapus filter voting" class="hover:text-red-600 cursor-pointer p-0.5">
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

    @if ($this->statistics['total'] === 0)
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg sm:text-xl text-brand uppercase">
                    BELUM ADA DATA MAHASISWA
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Import data mahasiswa eligible untuk memulai.
                </p>
            </div>
            <div>
                <button wire:click="openImportModal"
                        type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-bold uppercase tracking-wider transition-all">
                    <span>IMPORT CSV</span>
                </button>
            </div>
        </div>
    @elseif ($this->eligibleVoters->isEmpty())
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg text-brand uppercase">
                    DATA TIDAK DITEMUKAN
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Ubah kata kunci atau filter yang digunakan.
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
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">NIM</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">NAMA</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">JURUSAN</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">TGL LAHIR</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">STATUS DATA</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">REGISTRASI</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">VOTING</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->eligibleVoters as $voter)
                            <tr class="hover:bg-surface-muted/50 transition-colors {{ ! $voter->isComplete() ? 'bg-amber-50/40' : '' }}">
                                <td class="px-3 py-3 text-xs sm:text-sm font-mono font-bold text-ink/70 text-center whitespace-nowrap border-b border-ink/10">
                                    #{{ ($this->eligibleVoters->currentPage() - 1) * $this->eligibleVoters->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 text-xs sm:text-sm font-mono font-bold text-brand whitespace-nowrap border-b border-ink/10">
                                    {{ $voter->nim }}
                                </td>
                                <td class="px-4 py-3 text-xs sm:text-sm font-sans font-bold text-ink uppercase border-b border-ink/10">
                                    {{ $voter->name ?: '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs sm:text-sm font-sans text-ink/80 text-center border-b border-ink/10">
                                    {{ $voter->studyProgram?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs sm:text-sm font-sans text-ink/70 whitespace-nowrap text-center border-b border-ink/10">
                                    {{ $voter->date_of_birth?->format('d-m-Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center border-b border-ink/10">
                                    @if ($voter->isComplete())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-200 text-emerald-950 border border-ink text-xs font-display font-bold uppercase">
                                            <span class="w-1.5 h-1.5 bg-emerald-700 inline-block"></span>
                                            LENGKAP
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-200 text-amber-950 border border-ink text-xs font-display font-bold uppercase">
                                            <span class="w-1.5 h-1.5 bg-amber-700 inline-block"></span>
                                            TIDAK LENGKAP
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center border-b border-ink/10">
                                    @if ($voter->voter_account_exists)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-200 text-emerald-950 border border-ink text-xs font-display font-bold uppercase">
                                            <span class="w-1.5 h-1.5 bg-emerald-700 inline-block"></span>
                                            TERDAFTAR
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-red-100 text-red-900 border border-ink text-xs font-display font-bold uppercase">
                                            <span class="w-1.5 h-1.5 bg-red-600 inline-block"></span>
                                            BELUM TERDAFTAR
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center border-b border-ink/10">
                                    @if (! $this->statistics['has_current_election'])
                                        <span class="text-ink/40 text-xs font-sans italic">-</span>
                                    @elseif ($voter->has_voted)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-brand text-accent border border-ink text-xs font-display font-bold uppercase">
                                            <span class="w-1.5 h-1.5 bg-accent inline-block"></span>
                                            SUDAH VOTING
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 text-amber-900 border border-ink text-xs font-display font-bold uppercase">
                                            <span class="w-1.5 h-1.5 bg-amber-600 inline-block"></span>
                                            BELUM VOTING
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-action-button variant="detail" label="Lihat detail" wire:click="openDetailModal({{ $voter->id }})" />
                                        <x-action-button variant="edit" label="Edit data pemilih" wire:click="openEditModal({{ $voter->id }})" />
                                        <x-action-button variant="delete" label="Hapus data pemilih" wire:click="openDeleteModal({{ $voter->id }})" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $this->eligibleVoters->links() }}
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
                            FILTER PEMILIH
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
                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Status Kelengkapan Data
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-surface-muted border-2 border-ink">
                            <button type="button"
                                    wire:click="$set('completenessFilter', 'all')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $completenessFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Semua
                            </button>
                            <button type="button"
                                    wire:click="$set('completenessFilter', 'complete')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $completenessFilter === 'complete' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Lengkap
                            </button>
                            <button type="button"
                                    wire:click="$set('completenessFilter', 'incomplete')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $completenessFilter === 'incomplete' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Tidak Lengkap
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Jurusan
                        </label>
                        <select wire:model.live="studyProgramFilter"
                                aria-label="Pilih jurusan"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                            <option value="">Semua Jurusan</option>
                            @foreach ($this->studyPrograms as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Status Registrasi Akun
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-surface-muted border-2 border-ink">
                            <button type="button"
                                    wire:click="$set('registrationFilter', 'all')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $registrationFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Semua
                            </button>
                            <button type="button"
                                    wire:click="$set('registrationFilter', 'registered')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $registrationFilter === 'registered' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Terdaftar
                            </button>
                            <button type="button"
                                    wire:click="$set('registrationFilter', 'unregistered')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $registrationFilter === 'unregistered' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Belum
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Status Partisipasi Voting
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-surface-muted border-2 border-ink">
                            <button type="button"
                                    wire:click="$set('votingFilter', 'all')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $votingFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Semua
                            </button>
                            <button type="button"
                                    wire:click="$set('votingFilter', 'voted')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $votingFilter === 'voted' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Sudah
                            </button>
                            <button type="button"
                                    wire:click="$set('votingFilter', 'not_voted')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $votingFilter === 'not_voted' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Belum
                            </button>
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

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">DATA MAHASISWA</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            TAMBAH MAHASISWA ELIGIBLE
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

                <form wire:submit="createEligibleVoter" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Nomor Induk Mahasiswa (NIM) <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   wire:model="nim"
                                   placeholder="Contoh: 2215354001"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-mono font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('nim')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Nama Lengkap Mahasiswa
                            </label>
                            <input type="text"
                                   wire:model="name"
                                   placeholder="Masukkan nama lengkap (opsional jika belum ada)..."
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('name')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Jurusan
                            </label>
                            <select wire:model="study_program_id"
                                    class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer min-h-10">
                                <option value="">Pilih Jurusan (Opsional)</option>
                                @foreach ($this->studyPrograms as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                                @endforeach
                            </select>
                            @error('study_program_id')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Tanggal Lahir
                            </label>
                            <input type="date"
                                   wire:model="date_of_birth"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('date_of_birth')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                        <button wire:click="closeCreateModal"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                            BATAL
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-black uppercase tracking-wider text-center transition-all cursor-pointer min-h-10">
                            SIMPAN MAHASISWA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-lg bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">EDIT DATA</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            EDIT DATA MAHASISWA
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

                <form wire:submit="updateEligibleVoter" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        @if ($isNimLocked)
                            <div class="p-3 bg-amber-50 border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold text-amber-900 flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>NIM terkunci dan tidak dapat diubah karena mahasiswa sudah memiliki akun pemilih terdaftar.</span>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Nomor Induk Mahasiswa (NIM) <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   wire:model="nim"
                                   @if ($isNimLocked) disabled @endif
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-mono font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10 @if($isNimLocked) opacity-60 cursor-not-allowed @endif" />
                            @error('nim')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Nama Lengkap Mahasiswa
                            </label>
                            <input type="text"
                                   wire:model="name"
                                   placeholder="Lengkapi nama mahasiswa..."
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('name')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Jurusan
                            </label>
                            <select wire:model="study_program_id"
                                    class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer min-h-10">
                                <option value="">Pilih Jurusan</option>
                                @foreach ($this->studyPrograms as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                                @endforeach
                            </select>
                            @error('study_program_id')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Tanggal Lahir
                            </label>
                            <input type="date"
                                   wire:model="date_of_birth"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('date_of_birth')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                        <button wire:click="closeEditModal"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                            BATAL
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-black uppercase tracking-wider text-center transition-all cursor-pointer min-h-10">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDetailModal && $selectedEligibleVoter)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">RINCIAN DATA</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            DETAIL MAHASISWA
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

                <div class="p-4 sm:p-5 space-y-3.5 overflow-y-auto flex-1 min-w-0">
                    @if (! $selectedEligibleVoter->isComplete())
                        <div class="p-3.5 bg-amber-50 border-2 border-ink shadow-brutal-sm space-y-2">
                            <div class="flex items-center gap-2 text-xs font-display font-black text-amber-900 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>DATA TIDAK LENGKAP</span>
                            </div>
                            <p class="text-xs font-sans text-amber-800 leading-relaxed">
                                Mahasiswa ini dapat mengikuti PEMIRA tetapi memerlukan kelengkapan data agar dapat melakukan pendaftaran pemilih.
                            </p>
                            <div class="pt-1.5 border-t border-amber-200">
                                <span class="text-[11px] font-display font-bold uppercase tracking-wider text-amber-950 block mb-1">
                                    DATA YANG PERLU DILENGKAPI:
                                </span>
                                <ul class="space-y-1 text-xs font-sans font-bold text-amber-900">
                                    @foreach ($selectedEligibleVoter->missingFields() as $missing)
                                        <li class="flex items-center gap-2">
                                            <span class="w-3.5 h-3.5 border border-ink bg-surface flex items-center justify-center text-[10px] text-amber-800 shrink-0 font-mono font-black">!</span>
                                            <span>{{ $missing }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="p-2.5 bg-emerald-50 border-2 border-ink shadow-brutal-sm flex items-center gap-2 text-xs font-display font-bold text-emerald-800 uppercase">
                            <span class="w-2 h-2 bg-emerald-600 inline-block"></span>
                            <span>DATA MAHASISWA LENGKAP</span>
                        </div>
                    @endif

                    <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">NIM</span>
                        <div class="font-mono font-black text-base text-brand">{{ $selectedEligibleVoter->nim }}</div>
                    </div>

                    <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">NAMA MAHASISWA</span>
                        <div class="font-display font-black text-base text-ink uppercase">
                            {{ $selectedEligibleVoter->name ?: 'Belum tersedia' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">JURUSAN</span>
                            <div class="font-sans font-bold text-xs sm:text-sm text-ink">
                                {{ $selectedEligibleVoter->studyProgram?->name ?? 'Belum terpetakan' }}
                            </div>
                        </div>

                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">TANGGAL LAHIR</span>
                            <div class="font-sans font-bold text-xs sm:text-sm text-ink">
                                {{ $selectedEligibleVoter->date_of_birth?->format('d-m-Y') ?? 'Belum tersedia' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">STATUS REGISTRASI</span>
                            <div>
                                @if ($selectedEligibleVoter->voter_account_exists)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-200 text-emerald-950 border border-ink text-xs font-display font-bold uppercase">
                                        <span class="w-1.5 h-1.5 bg-emerald-700 inline-block"></span>
                                        TERDAFTAR
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-red-100 text-red-900 border border-ink text-xs font-display font-bold uppercase">
                                        <span class="w-1.5 h-1.5 bg-red-600 inline-block"></span>
                                        BELUM TERDAFTAR
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm space-y-1">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">STATUS VOTING</span>
                            <div>
                                @if (! $this->statistics['has_current_election'])
                                    <span class="text-xs font-sans text-ink/50 italic">Tidak ada PEMIRA</span>
                                @elseif ($selectedEligibleVoter->has_voted)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-brand text-accent border border-ink text-xs font-display font-bold uppercase">
                                        SUDAH VOTING
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-surface text-ink/70 border border-ink text-xs font-display font-bold uppercase">
                                        BELUM VOTING
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 text-xs font-sans text-ink/50">
                        Terdaftar di sistem: {{ $selectedEligibleVoter->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </div>
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-accent border-2 border-ink font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                        TUTUP
                    </button>
                    <button wire:click="openEditModal({{ $selectedEligibleVoter->id }})"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-brand text-surface hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-bold text-xs uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                        LENGKAPI / EDIT
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal && $selectedEligibleVoter)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">HAPUS DATA</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            KONFIRMASI HAPUS
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

                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                    @if ($deleteError)
                        <div class="p-4 bg-red-50 border-2 border-ink shadow-brutal-sm space-y-2">
                            <div class="flex items-center gap-2 text-red-900 font-display font-black text-xs uppercase tracking-wider">
                                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>PENGHAPUSAN DILINDUNGI</span>
                            </div>
                            <p class="text-xs font-sans font-bold text-red-800 leading-relaxed">
                                {{ $deleteError }}
                            </p>
                        </div>
                    @else
                        <p class="text-xs sm:text-sm font-sans text-ink leading-relaxed">
                            Apakah Anda yakin ingin menghapus data mahasiswa eligible berikut?
                        </p>

                        <div class="bg-surface-muted border-2 border-ink p-3 space-y-1">
                            <div class="text-xs font-mono font-bold text-brand">{{ $selectedEligibleVoter->nim }}</div>
                            <div class="text-sm font-display font-black text-ink uppercase">{{ $selectedEligibleVoter->name ?: '(Nama belum diisi)' }}</div>
                            <div class="text-xs font-sans text-ink/70">{{ $selectedEligibleVoter->studyProgram?->name ?? 'Jurusan belum terdaftar' }}</div>
                        </div>

                        <p class="text-xs font-sans text-ink/60">
                            Tindakan ini tidak dapat dibatalkan. Data master mahasiswa akan dihapus secara permanen dari daftar eligible voters.
                        </p>
                    @endif
                </div>

                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                    <button wire:click="closeDeleteModal"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                        {{ $deleteError ? 'TUTUP' : 'BATAL' }}
                    </button>
                    @if (! $deleteError)
                        <button wire:click="deleteEligibleVoter"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-red-600 text-surface hover:bg-red-700 border-2 border-ink shadow-brutal text-xs font-display font-black uppercase tracking-wider text-center transition-all cursor-pointer min-h-10">
                            HAPUS MAHASISWA
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-3xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">IMPORT DATA CSV</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            @if ($importStep === 'upload')
                                STEP 1: UPLOAD CSV MAHASISWA
                            @elseif ($importStep === 'preview')
                                STEP 2: PREVIEW &amp; VALIDASI DATA
                            @else
                                STEP 3: HASIL IMPORT
                            @endif
                        </h3>
                    </div>
                    <button wire:click="closeImportModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @if ($importStep === 'upload')
                    <form wire:submit="processUpload" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 min-w-0">
                            <div class="bg-surface-muted border-2 border-dashed border-ink p-6 sm:p-8 text-center space-y-3">
                                <div class="w-12 h-12 bg-surface border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <label for="csvFileInput" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-bold text-xs uppercase tracking-wider cursor-pointer">
                                        <span>PILIH FILE CSV</span>
                                    </label>
                                    <input id="csvFileInput"
                                           type="file"
                                           wire:model="importFile"
                                           accept=".csv,text/csv,application/csv,text/plain"
                                           class="hidden" />
                                </div>
                                <p class="text-xs font-sans text-ink/60">
                                    Format file yang didukung: <span class="font-bold">.csv</span> (Maksimal 20 MB).
                                </p>

                                <div wire:loading wire:target="importFile" class="text-xs font-sans font-bold text-brand animate-pulse">
                                    Mengunggah file CSV... mohon tunggu...
                                </div>

                                @if ($importFile)
                                    <div class="mt-3 p-3 bg-surface border-2 border-ink shadow-brutal-sm text-left inline-block max-w-full">
                                        <div class="flex items-center gap-2 text-xs font-display font-bold text-brand uppercase">
                                            <span class="w-2 h-2 bg-emerald-600 inline-block"></span>
                                            <span class="truncate">{{ $importFile->getClientOriginalName() }}</span>
                                        </div>
                                        <div class="text-[11px] font-mono text-ink/60 mt-0.5">
                                            Ukuran: {{ number_format($importFile->getSize() / 1024, 1) }} KB
                                        </div>
                                    </div>
                                @endif

                                @error('importFile')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="bg-surface-muted border-2 border-ink p-4 space-y-2 text-xs font-sans">
                                <span class="font-display font-bold uppercase tracking-wider text-brand block">KEBIJAKAN KELENGKAPAN DATA</span>
                                <p class="text-ink/70">
                                    File CSV wajib memiliki kolom <span class="font-bold">NIM</span> sebagai identitas utama.
                                    Data dengan Nama, Jurusan, atau Tanggal Lahir yang kosong tetap dapat diimpor dan ditandai sebagai <span class="font-bold text-amber-800">DATA TIDAK LENGKAP</span> untuk dilengkapi kemudian oleh admin.
                                </p>
                                <div class="bg-surface border border-ink p-2.5 font-mono text-xs text-ink/90 overflow-x-auto">
                                    NIM,Nama,Jurusan,Tanggal Lahir<br>
                                    2215354001,I Putu Gede Raditya,Teknologi Informasi,2004-03-15<br>
                                    2215354002,Kadek Ayu Lestari,,
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 p-3.5 bg-amber-50/70 border-2 border-ink text-xs font-sans shadow-brutal-sm">
                                <input type="checkbox"
                                       id="updateExistingUploadCheckbox"
                                       wire:model="updateExisting"
                                       class="w-4 h-4 mt-0.5 text-brand border-2 border-ink rounded-none focus:ring-0 cursor-pointer shrink-0" />
                                <label for="updateExistingUploadCheckbox" class="font-bold text-ink cursor-pointer select-none">
                                    <span>Perbarui data jika NIM sudah ada di database</span>
                                    <span class="block font-normal text-ink/75 mt-0.5">
                                        Centang opsi ini untuk melengkapi/menimpa data mahasiswa yang sudah ada di sistem (misal: mengisi jurusan atau tanggal lahir yang sebelumnya belum lengkap). Jika tidak dicentang, data dengan NIM yang sudah ada akan dilewati.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                            <button wire:click="closeImportModal"
                                    type="button"
                                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                                BATAL
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-black uppercase tracking-wider text-center transition-all cursor-pointer min-h-10 disabled:opacity-50">
                                <span wire:loading.remove wire:target="processUpload">LANJUTKAN PREVIEW</span>
                                <span wire:loading wire:target="processUpload">MEMPROSES FILE...</span>
                            </button>
                        </div>
                    </form>
                @elseif ($importStep === 'preview')
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-ink/60 block">TOTAL BARIS</span>
                                    <div class="font-display font-black text-xl text-ink mt-1">
                                        {{ number_format($importSummary['total'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="bg-emerald-50 border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-emerald-800 block">SIAP DIIMPORT</span>
                                    <div class="font-display font-black text-xl text-emerald-700 mt-1">
                                        {{ number_format($importSummary['valid'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] font-sans font-bold text-emerald-800 mt-0.5">
                                        {{ number_format($importSummary['complete'], 0, ',', '.') }} Lengkap &middot; {{ number_format($importSummary['incomplete'], 0, ',', '.') }} Parsial
                                        @if ($updateExisting && ($importSummary['to_update'] ?? 0) > 0)
                                            <span class="block text-[10px] text-emerald-900 font-semibold mt-0.5">({{ number_format($importSummary['to_update'], 0, ',', '.') }} akan diperbarui)</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-amber-50 border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-amber-800 block">DUPLIKAT</span>
                                    <div class="font-display font-black text-xl text-amber-700 mt-1">
                                        {{ number_format($importSummary['duplicates'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] font-sans text-amber-800 mt-0.5">
                                        {{ $updateExisting ? 'NIM ganda di CSV' : 'NIM sudah ada di DB' }}
                                    </div>
                                </div>

                                <div class="bg-red-50 border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-red-800 block">ERROR / TIDAK VALID</span>
                                    <div class="font-display font-black text-xl text-red-700 mt-1">
                                        {{ number_format($importSummary['errors'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] font-sans text-red-800 mt-0.5">
                                        NIM/Tgl rusak
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-3.5 bg-surface-muted border-2 border-ink text-xs font-sans shadow-brutal-sm">
                                <input type="checkbox"
                                       id="updateExistingCheckbox"
                                       wire:model.live="updateExisting"
                                       class="w-4 h-4 mt-0.5 text-brand border-2 border-ink rounded-none focus:ring-0 cursor-pointer shrink-0" />
                                <label for="updateExistingCheckbox" class="font-bold text-ink cursor-pointer select-none">
                                    <span>Perbarui data jika NIM sudah ada di database</span>
                                    <span class="block font-normal text-ink/75 mt-0.5">
                                        Jika dicentang, data mahasiswa dengan NIM yang sudah ada di database akan diperbarui/dilengkapi (misal mengisi jurusan atau tanggal lahir). Jika tidak dicentang, NIM yang sudah ada akan dilewati.
                                    </span>
                                </label>
                            </div>

                            @if (! empty($importPreview))
                                <div class="space-y-2">
                                    <span class="text-xs font-display font-bold uppercase tracking-wider text-brand block">
                                        PREVIEW DATA (10 BARIS PERTAMA)
                                    </span>
                                    <div class="border-2 border-ink overflow-x-auto shadow-brutal-sm">
                                        <table class="w-full text-left text-xs">
                                            <thead class="bg-surface-muted border-b border-ink">
                                                <tr>
                                                    <th class="p-2 font-display font-bold text-brand uppercase">NIM</th>
                                                    <th class="p-2 font-display font-bold text-brand uppercase">Nama</th>
                                                    <th class="p-2 font-display font-bold text-brand uppercase">Jurusan</th>
                                                    <th class="p-2 font-display font-bold text-brand uppercase">Tgl Lahir</th>
                                                    <th class="p-2 font-display font-bold text-brand uppercase">Status Data</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-ink/10">
                                                @foreach ($importPreview as $preview)
                                                    <tr class="{{ ! $preview['is_complete'] ? 'bg-amber-50/50' : '' }}">
                                                        <td class="p-2 font-mono font-bold">{{ $preview['nim'] }}</td>
                                                        <td class="p-2 uppercase">{{ $preview['name'] }}</td>
                                                        <td class="p-2">{{ $preview['jurusan'] }}</td>
                                                        <td class="p-2">{{ $preview['date_of_birth'] }}</td>
                                                        <td class="p-2">
                                                            @if ($preview['is_complete'])
                                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 border border-ink text-[10px] font-bold">
                                                                    {{ $preview['status'] }}
                                                                </span>
                                                            @else
                                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 border border-ink text-[10px] font-bold">
                                                                    {{ $preview['status'] }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if (! empty($importErrors))
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-display font-bold uppercase tracking-wider text-red-600 block">
                                            CATATAN VALIDASI &amp; PERINGATAN ({{ $totalErrorsCount }} Catatan)
                                        </span>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto border-2 border-ink bg-red-50 p-2.5 space-y-1 text-xs font-sans text-red-900 shadow-brutal-sm">
                                        @foreach ($importErrors as $err)
                                            <div class="flex items-start gap-2">
                                                <span class="font-mono font-bold text-red-700 shrink-0">Baris {{ $err['row'] }}:</span>
                                                <span>{{ $err['message'] }}</span>
                                            </div>
                                        @endforeach
                                        @if ($totalErrorsCount > count($importErrors))
                                            <div class="text-ink/60 font-italic text-[11px] pt-1">
                                                ...dan {{ $totalErrorsCount - count($importErrors) }} catatan lainnya tidak ditampilkan di preview.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                            <button wire:click="resetImportState"
                                    type="button"
                                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-center transition-colors cursor-pointer min-h-10">
                                KEMBALI / UPLOAD ULANG
                            </button>
                            <button wire:click="confirmImport"
                                    type="button"
                                    @if ($importSummary['valid'] === 0) disabled @endif
                                    wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-black uppercase tracking-wider text-center transition-all cursor-pointer min-h-10 disabled:opacity-50">
                                <span wire:loading.remove wire:target="confirmImport,updateExisting">
                                    @if ($updateExisting && ($importSummary['to_update'] ?? 0) > 0)
                                        IMPORT &amp; PERBARUI {{ number_format($importSummary['valid'], 0, ',', '.') }} DATA
                                    @else
                                        IMPORT {{ number_format($importSummary['valid'], 0, ',', '.') }} DATA
                                    @endif
                                </span>
                                <span wire:loading wire:target="confirmImport">
                                    MENYIMPAN DATA...
                                </span>
                                <span wire:loading wire:target="updateExisting">
                                    MEMPERBARUI PREVIEW...
                                </span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        <div class="p-6 sm:p-8 space-y-6 overflow-y-auto flex-1 min-w-0 text-center">
                            <div class="w-16 h-16 bg-emerald-100 border-2 border-ink mx-auto flex items-center justify-center shadow-brutal">
                                <svg class="w-8 h-8 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-display font-black text-xl text-brand uppercase tracking-tight">
                                    IMPORT SELESAI
                                </h3>
                                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1">
                                    Proses import data mahasiswa eligible telah selesai dijalankan.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-xl mx-auto text-left">
                                <div class="bg-emerald-50 border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-emerald-800 block">LENGKAP</span>
                                    <div class="font-display font-black text-xl text-emerald-700 mt-1">
                                        {{ number_format($importResult['complete'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="bg-amber-50 border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-amber-800 block">PARSIAL</span>
                                    <div class="font-display font-black text-xl text-amber-700 mt-1">
                                        {{ number_format($importResult['incomplete'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="bg-surface-muted border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-ink/70 block">DUPLIKAT</span>
                                    <div class="font-display font-black text-xl text-ink/80 mt-1">
                                        {{ number_format($importResult['duplicates'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="bg-red-50 border-2 border-ink p-3 shadow-brutal-sm">
                                    <span class="text-xs font-display font-bold uppercase text-red-800 block">GAGAL</span>
                                    <div class="font-display font-black text-xl text-red-700 mt-1">
                                        {{ number_format($importResult['failed'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-t-2 border-ink flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 bg-surface-muted shrink-0">
                            <button wire:click="closeImportModal"
                                    type="button"
                                    class="w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-black uppercase tracking-wider text-center transition-all cursor-pointer min-h-10">
                                SELESAI &amp; TUTUP
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
