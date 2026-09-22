<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>SUARA MAHASISWA & EVALUASI</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                MASUKAN PEMILIH
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                @if ($isSuperAdmin)
                    Pantau, baca, filter, dan kelola seluruh masukan serta penilaian yang diberikan oleh pemilih.
                @else
                    Pantau, baca, dan pelajari masukan serta penilaian pemilih untuk evaluasi penyelenggaraan PEMIRA.
                @endif
            </p>
        </div>

        <div class="shrink-0 flex items-center gap-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase tracking-wider text-ink">
                <span class="w-2 h-2 {{ $isSuperAdmin ? 'bg-accent' : 'bg-brand' }} inline-block border border-ink"></span>
                <span>SCOPE: {{ $isSuperAdmin ? 'SUPER ADMIN (FULL KONTROL)' : 'ADMIN KPR (OPERASIONAL)' }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto pb-1 -mx-1 px-1">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 min-w-135 lg:min-w-0 gap-2 sm:gap-3">
            <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-ink/60 block truncate">TOTAL MASUKAN</span>
                <div class="font-display font-black text-lg sm:text-2xl text-brand mt-0.5 sm:mt-1 truncate">
                    {{ number_format($stats['total'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Semua Tanggapan</span>
            </div>

            <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-amber-800 block truncate">RATA-RATA RATING</span>
                <div class="font-display font-black text-lg sm:text-2xl text-amber-600 mt-0.5 sm:mt-1 truncate flex items-center gap-1">
                    <span>{{ number_format($stats['average_rating'], 1, ',', '.') }}</span>
                    <span class="text-xs font-sans text-ink/50 font-normal">/ 5.0</span>
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Indeks Kepuasan</span>
            </div>

            <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-blue-800 block truncate">DENGAN ULASAN</span>
                <div class="font-display font-black text-lg sm:text-2xl text-blue-700 mt-0.5 sm:mt-1 truncate">
                    {{ number_format($stats['with_comment'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Komentar Tertulis</span>
            </div>

            <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-emerald-800 block truncate">RATING POSITIF</span>
                <div class="font-display font-black text-lg sm:text-2xl text-emerald-700 mt-0.5 sm:mt-1 truncate">
                    {{ number_format($stats['positive'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Bintang 4 & 5</span>
            </div>

            <div class="bg-surface border-2 border-ink p-2.5 sm:p-3 shadow-brutal min-w-0">
                <span class="text-[10px] sm:text-xs font-display font-bold uppercase tracking-wider text-rose-800 block truncate">PERLU EVALUASI</span>
                <div class="font-display font-black text-lg sm:text-2xl text-rose-700 mt-0.5 sm:mt-1 truncate">
                    {{ number_format($stats['critical'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] sm:text-xs text-ink/50 mt-0.5 block truncate">Bintang 1 & 2</span>
            </div>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink p-3.5 sm:p-4 shadow-brutal space-y-3">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="relative flex-1 min-w-0">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari isi masukan, nama pemilih, NIM, atau prodi..."
                       aria-label="Cari masukan pemilih"
                       class="w-full bg-surface-muted border-2 border-ink pl-9 pr-3 py-2 text-xs sm:text-sm font-sans font-medium text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button wire:click="openFilterModal"
                        type="button"
                        aria-label="Buka filter masukan pemilih"
                        class="inline-flex items-center justify-center gap-2 px-3.5 py-2 border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10 {{ $activeFilters > 0 ? 'bg-accent text-ink' : 'bg-surface-muted hover:bg-accent text-ink' }}">
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

                @if ($electionFilter !== 'all')
                    @php
                        $electionLabel = $electionList->firstWhere('id', (int) $electionFilter)?->name ?? 'Pemilihan #' . $electionFilter;
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Pemilihan: {{ $electionLabel }}</span>
                        <button wire:click="clearFilter('election')" type="button" aria-label="Hapus filter pemilihan" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($ratingFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Rating: {{ $ratingFilter }} Bintang</span>
                        <button wire:click="clearFilter('rating')" type="button" aria-label="Hapus filter rating" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($typeFilter !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Tipe: {{ $typeFilter === 'with_comment' ? 'Dengan Komentar' : 'Rating Saja' }}</span>
                        <button wire:click="clearFilter('type')" type="button" aria-label="Hapus filter tipe masukan" class="hover:text-red-600 cursor-pointer p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endif

                @if ($studyProgramFilter !== 'all')
                    @php
                        $prodiLabel = $studyProgramList->firstWhere('id', (int) $studyProgramFilter)?->name ?? 'Prodi #' . $studyProgramFilter;
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-muted border border-ink text-xs font-sans font-bold text-ink shadow-2xs">
                        <span>Prodi: {{ $prodiLabel }}</span>
                        <button wire:click="clearFilter('prodi')" type="button" aria-label="Hapus filter program studi" class="hover:text-red-600 cursor-pointer p-0.5">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg sm:text-xl text-brand uppercase">
                    BELUM ADA MASUKAN
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Belum ada masukan atau ulasan yang dikirimkan oleh pemilih pada sistem PEMIRA.
                </p>
            </div>
        </div>
    @elseif ($feedbacks->isEmpty())
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg text-brand uppercase">
                    MASUKAN TIDAK DITEMUKAN
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Tidak ada masukan yang sesuai dengan kata kunci pencarian atau filter yang dipilih.
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
        <div class="hidden md:block bg-surface border-2 border-ink shadow-brutal overflow-hidden">
            <div class="overflow-auto max-h-[calc(100vh-280px)] min-h-105">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead class="sticky top-0 z-20">
                        <tr class="bg-surface-muted">
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-3 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center w-12 sm:w-16">NO</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">WAKTU (WITA)</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-left whitespace-nowrap">PEMILIH</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center whitespace-nowrap">RATING</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-left whitespace-nowrap">PEMILIHAN</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-left">MASUKAN / ULASAN</th>
                            <th class="sticky top-0 z-20 bg-surface-muted border-b-2 border-ink px-4 py-3 text-xs font-display font-black text-brand uppercase tracking-wider text-center w-24">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feedbacks as $fb)
                            @php
                                $ratingBadgeClass = match ((int) $fb->rating) {
                                    5 => 'bg-emerald-200 text-emerald-950 border border-ink',
                                    4 => 'bg-teal-200 text-teal-950 border border-ink',
                                    3 => 'bg-amber-200 text-amber-950 border border-ink',
                                    2 => 'bg-orange-200 text-orange-950 border border-ink',
                                    default => 'bg-rose-200 text-rose-950 border border-ink',
                                };
                                $voter = $fb->voterAccount?->eligibleVoter;
                            @endphp
                            <tr class="hover:bg-surface-muted/50 transition-colors">
                                <td class="px-3 py-3 text-xs sm:text-sm font-mono font-bold text-ink/70 text-center whitespace-nowrap border-b border-ink/10">
                                    {{ ($feedbacks->currentPage() - 1) * $feedbacks->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <div class="text-xs font-mono font-bold text-ink">
                                        {{ $fb->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') }}
                                    </div>
                                    <div class="text-[10px] font-sans text-ink/50 mt-0.5">
                                        {{ $fb->created_at->timezone('Asia/Makassar')->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-left border-b border-ink/10">
                                    <div class="font-display font-bold text-xs sm:text-sm text-ink leading-tight">
                                        {{ $voter?->name ?? 'Pemilih #' . $fb->voter_account_id }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[10px] font-mono text-ink/70 bg-surface-muted px-1 py-0.2 border border-ink/20">
                                            {{ $voter?->nim ?? '-' }}
                                        </span>
                                        <span class="text-[10px] font-sans text-ink/60 truncate max-w-35" title="{{ $voter?->studyProgram?->name }}">
                                            {{ $voter?->studyProgram?->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <div class="inline-flex flex-col items-center">
                                        <div class="flex items-center text-amber-500 mb-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $fb->rating ? 'text-amber-500 fill-amber-500' : 'text-ink/20 fill-ink/10' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.2 text-[10px] font-display font-bold uppercase {{ $ratingBadgeClass }}">
                                            {{ $fb->rating }} / 5 Bintang
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-left whitespace-nowrap border-b border-ink/10">
                                    <div class="text-xs font-display font-bold text-brand">
                                        {{ $fb->election?->name ?? 'Pemilihan #' . $fb->election_id }}
                                    </div>
                                    <div class="text-[10px] font-mono text-ink/50">
                                        Tahun {{ $fb->election?->year ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-left border-b border-ink/10 max-w-sm">
                                    @if ($fb->comment)
                                        <span class="text-xs font-sans text-ink line-clamp-2 leading-relaxed" title="{{ $fb->comment }}">
                                            "{{ $fb->comment }}"
                                        </span>
                                    @else
                                        <span class="text-xs font-sans text-ink/40 italic">
                                            Hanya rating tanpa ulasan tertulis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap border-b border-ink/10">
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-action-button variant="detail" label="Lihat detail masukan" wire:click="openDetailModal({{ $fb->id }})" />
                                        @if ($isSuperAdmin)
                                            <x-action-button variant="delete" label="Hapus masukan" wire:click="openDeleteModal({{ $fb->id }})" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="block md:hidden space-y-3">
            @foreach ($feedbacks as $fb)
                @php
                    $ratingBadgeClass = match ((int) $fb->rating) {
                        5 => 'bg-emerald-200 text-emerald-950 border border-ink',
                        4 => 'bg-teal-200 text-teal-950 border border-ink',
                        3 => 'bg-amber-200 text-amber-950 border border-ink',
                        2 => 'bg-orange-200 text-orange-950 border border-ink',
                        default => 'bg-rose-200 text-rose-950 border border-ink',
                    };
                    $voter = $fb->voterAccount?->eligibleVoter;
                @endphp
                <div class="bg-surface border-2 border-ink p-4 shadow-brutal space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-surface-muted border border-ink text-[11px] font-mono font-black text-ink/80">
                                #{{ ($feedbacks->currentPage() - 1) * $feedbacks->perPage() + $loop->iteration }}
                            </span>
                            <span class="text-[11px] font-mono text-ink/70">
                                {{ $fb->created_at->timezone('Asia/Makassar')->format('d/m/Y H:i') }} WITA
                            </span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-display font-bold uppercase {{ $ratingBadgeClass }}">
                            ★ {{ $fb->rating }} / 5
                        </span>
                    </div>

                    <div>
                        <div class="font-display font-black text-sm text-ink">
                            {{ $voter?->name ?? 'Pemilih #' . $fb->voter_account_id }}
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs font-sans text-ink/70 mt-0.5">
                            <span class="font-mono">{{ $voter?->nim ?? '-' }}</span>
                            <span>·</span>
                            <span>{{ $voter?->studyProgram?->name ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="bg-surface-muted border border-ink/20 p-2.5">
                        @if ($fb->comment)
                            <p class="text-xs font-sans text-ink italic leading-relaxed line-clamp-3">
                                "{{ $fb->comment }}"
                            </p>
                        @else
                            <p class="text-xs font-sans text-ink/40 italic">
                                Hanya rating tanpa ulasan tertulis
                            </p>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-ink/10 flex items-center justify-between gap-2">
                        <span class="text-[11px] font-display font-bold text-brand uppercase truncate">
                            {{ $fb->election?->name }}
                        </span>
                        <div class="inline-flex items-center gap-1.5">
                            <x-action-button variant="detail" label="Lihat detail masukan" wire:click="openDetailModal({{ $fb->id }})" />
                            @if ($isSuperAdmin)
                                <x-action-button variant="delete" label="Hapus masukan" wire:click="openDeleteModal({{ $fb->id }})" />
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $feedbacks->links() }}
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
                            FILTER MASUKAN PEMILIH
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
                        <label for="filter-election" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Pemilihan
                        </label>
                        <select id="filter-election"
                                wire:model.live="electionFilter"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                            <option value="all">Semua Pemilihan</option>
                            @foreach ($electionList as $elec)
                                <option value="{{ $elec->id }}">{{ $elec->name }} ({{ $elec->year }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Tipe Masukan
                        </label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-surface-muted border-2 border-ink">
                            <button type="button"
                                    wire:click="$set('typeFilter', 'all')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $typeFilter === 'all' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Semua
                            </button>
                            <button type="button"
                                    wire:click="$set('typeFilter', 'with_comment')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $typeFilter === 'with_comment' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Ada Ulasan
                            </button>
                            <button type="button"
                                    wire:click="$set('typeFilter', 'rating_only')"
                                    class="py-2 text-xs font-display font-bold uppercase transition-colors cursor-pointer {{ $typeFilter === 'rating_only' ? 'bg-brand text-surface shadow-xs' : 'text-ink hover:bg-ink/10' }}">
                                Hanya Rating
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="filter-rating" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Rating Bintang
                        </label>
                        <select id="filter-rating"
                                wire:model.live="ratingFilter"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                            <option value="all">Semua Rating</option>
                            <option value="5">★★★★★ (5 Bintang - Sangat Baik)</option>
                            <option value="4">★★★★☆ (4 Bintang - Baik)</option>
                            <option value="3">★★★☆☆ (3 Bintang - Cukup)</option>
                            <option value="2">★★☆☆☆ (2 Bintang - Kurang)</option>
                            <option value="1">★☆☆☆☆ (1 Bintang - Sangat Kurang)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="filter-prodi" class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Program Studi Pemilih
                        </label>
                        <select id="filter-prodi"
                                wire:model.live="studyProgramFilter"
                                class="w-full bg-surface-muted border-2 border-ink px-3 py-2.5 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer">
                            <option value="all">Semua Program Studi</option>
                            @foreach ($studyProgramList as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->name }} ({{ $prodi->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-display font-bold text-xs uppercase tracking-wider text-ink block">
                            Rentang Tanggal Masukan
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-sans font-bold text-ink/60 uppercase block mb-1">Dari Tanggal</span>
                                <input type="date"
                                       id="filter-start-date"
                                       wire:model.live="startDate"
                                       aria-label="Dari tanggal"
                                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            </div>
                            <div>
                                <span class="text-[10px] font-sans font-bold text-ink/60 uppercase block mb-1">Sampai Tanggal</span>
                                <input type="date"
                                       id="filter-end-date"
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

    @if ($showDetailModal && $detail)
        @php
            $voterDetail = $detail->voterAccount?->eligibleVoter;
            $ratingText = match ((int) $detail->rating) {
                5 => 'Sangat Baik (5 / 5)',
                4 => 'Baik (4 / 5)',
                3 => 'Cukup (3 / 5)',
                2 => 'Kurang (2 / 5)',
                default => 'Sangat Kurang (1 / 5)',
            };
            $detailBadgeClass = match ((int) $detail->rating) {
                5 => 'bg-emerald-200 text-emerald-950 border border-ink',
                4 => 'bg-teal-200 text-teal-950 border border-ink',
                3 => 'bg-amber-200 text-amber-950 border border-ink',
                2 => 'bg-orange-200 text-orange-950 border border-ink',
                default => 'bg-rose-200 text-rose-950 border border-ink',
            };
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog"
             aria-modal="true"
             aria-labelledby="detail-modal-title">
            <div class="fixed inset-0 bg-ink/70 backdrop-blur-xs transition-opacity"
                 wire:click="closeDetailModal"></div>

            <div class="min-h-full flex items-center justify-center p-3 sm:p-4">
                <div class="relative w-full max-w-xl bg-surface border-2 border-ink shadow-brutal overflow-hidden">
                    <div class="px-4 py-3.5 sm:px-6 border-b-2 border-ink flex items-center justify-between bg-surface-muted">
                        <div>
                            <span class="text-[11px] font-display font-bold uppercase tracking-wider text-ink/60 block">INFORMASI LENGKAP</span>
                            <h3 id="detail-modal-title" class="font-display font-black text-base sm:text-lg text-brand uppercase">
                                DETAIL MASUKAN PEMILIH
                            </h3>
                        </div>
                        <button wire:click="closeDetailModal"
                                type="button"
                                class="w-9 h-9 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                                aria-label="Tutup modal">
                            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-surface-muted border border-ink/20 p-3">
                                <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/60 block">PEMILIHAN</span>
                                <div class="font-display font-bold text-xs sm:text-sm text-brand mt-0.5">
                                    {{ $detail->election?->name ?? 'Pemilihan #' . $detail->election_id }}
                                </div>
                                <div class="text-[10px] font-mono text-ink/50 mt-0.5">
                                    Tahun {{ $detail->election?->year ?? '-' }}
                                </div>
                            </div>

                            <div class="bg-surface-muted border border-ink/20 p-3">
                                <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/60 block">WAKTU DIKIRIM</span>
                                <div class="font-mono font-bold text-xs sm:text-sm text-ink mt-0.5">
                                    {{ $detail->created_at->timezone('Asia/Makassar')->format('d M Y, H:i:s') }} WITA
                                </div>
                                <div class="text-[10px] font-sans text-ink/50 mt-0.5">
                                    {{ $detail->created_at->timezone('Asia/Makassar')->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <div class="bg-surface-muted border border-ink/20 p-3.5 space-y-2">
                            <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/60 block">IDENTITAS PEMILIH</span>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-display font-black text-sm sm:text-base text-ink">
                                        {{ $voterDetail?->name ?? 'Pemilih #' . $detail->voter_account_id }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-sans text-ink/70 mt-1">
                                        <span>NIM: <strong class="font-mono text-ink">{{ $voterDetail?->nim ?? '-' }}</strong></span>
                                        <span>·</span>
                                        <span>{{ $voterDetail?->studyProgram?->name ?? '-' }}</span>
                                    </div>
                                    @if ($detail->voterAccount?->user?->email)
                                        <div class="text-[11px] font-sans text-ink/50 mt-0.5">
                                            Email: {{ $detail->voterAccount->user->email }}
                                        </div>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 bg-brand text-accent border border-ink text-[10px] font-display font-bold uppercase shrink-0">
                                    TERVERIFIKASI
                                </span>
                            </div>
                        </div>

                        <div class="bg-surface-muted border border-ink/20 p-3.5 space-y-2">
                            <span class="text-[10px] font-display font-bold uppercase tracking-wider text-ink/60 block">PENILAIAN / RATING</span>
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-1 text-amber-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $detail->rating ? 'text-amber-500 fill-amber-500' : 'text-ink/20 fill-ink/10' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="px-2.5 py-0.5 text-xs font-display font-bold uppercase {{ $detailBadgeClass }}">
                                    {{ $ratingText }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-xs font-display font-bold uppercase tracking-wider text-ink block">ISI MASUKAN & SARAN</span>
                            <div class="bg-surface-muted border-2 border-ink p-4 shadow-brutal-sm">
                                @if ($detail->comment)
                                    <p class="text-xs sm:text-sm font-sans text-ink whitespace-pre-line leading-relaxed">
                                        {{ $detail->comment }}
                                    </p>
                                @else
                                    <p class="text-xs sm:text-sm font-sans text-ink/50 italic text-center py-3">
                                        Pemilih ini memberikan penilaian rating tanpa menyertakan pesan ulasan tertulis.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 border-t-2 border-ink bg-surface-muted flex items-center justify-between gap-3">
                        @if ($isSuperAdmin)
                            <button wire:click="openDeleteModal({{ $detail->id }})"
                                    type="button"
                                    class="px-3.5 py-2 bg-rose-100 hover:bg-rose-200 border-2 border-ink text-xs font-display font-bold text-rose-900 shadow-brutal-sm uppercase cursor-pointer min-h-10">
                                HAPUS MASUKAN
                            </button>
                        @else
                            <div></div>
                        @endif

                        <button wire:click="closeDetailModal"
                                type="button"
                                class="px-5 py-2 bg-surface border-2 border-ink text-xs font-display font-bold hover:bg-accent shadow-brutal-sm uppercase cursor-pointer min-h-10">
                            TUTUP
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog"
             aria-modal="true"
             aria-labelledby="delete-modal-title">
            <div class="fixed inset-0 bg-ink/70 backdrop-blur-xs transition-opacity"
                 wire:click="closeDeleteModal"></div>

            <div class="min-h-full flex items-center justify-center p-3 sm:p-4">
                <div class="relative w-full max-w-md bg-surface border-2 border-ink shadow-brutal overflow-hidden">
                    <div class="px-4 py-3.5 sm:px-6 border-b-2 border-ink bg-rose-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-rose-600 inline-block border border-ink"></span>
                            <h3 id="delete-modal-title" class="font-display font-black text-base text-rose-950 uppercase">
                                KONFIRMASI HAPUS MASUKAN
                            </h3>
                        </div>
                        <button wire:click="closeDeleteModal"
                                type="button"
                                class="w-8 h-8 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm cursor-pointer"
                                aria-label="Batal hapus">
                            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 sm:p-6 space-y-3 font-sans">
                        <p class="text-xs sm:text-sm text-ink leading-relaxed">
                            Apakah Anda yakin ingin menghapus data masukan pemilih ini?
                        </p>
                        <div class="p-3 bg-rose-50 border border-rose-300 text-xs text-rose-900 font-bold">
                            Tindakan ini permanen dan akan dicatat secara lengkap pada Audit & Log Aktivitas.
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 border-t-2 border-ink bg-surface-muted flex items-center justify-end gap-3">
                        <button wire:click="closeDeleteModal"
                                type="button"
                                class="px-4 py-2 bg-surface border-2 border-ink text-xs font-display font-bold hover:bg-accent shadow-brutal-sm uppercase cursor-pointer min-h-10">
                            BATAL
                        </button>
                        <button wire:click="deleteFeedback"
                                type="button"
                                class="px-5 py-2 bg-red-600 text-white border-2 border-ink text-xs font-display font-bold hover:bg-red-700 shadow-brutal-sm uppercase cursor-pointer min-h-10">
                            YA, HAPUS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
