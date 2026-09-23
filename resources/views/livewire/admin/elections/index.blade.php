<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                MANAJEMEN PEMIRA
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                Kelola data pemilihan, jadwal pendaftaran, dan periode voting digital.
            </p>
        </div>

        <div class="w-full sm:w-auto shrink-0">
            <button wire:click="openCreateModal"
                    type="button"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>BUAT PEMIRA</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-surface border-2 border-ink p-3 sm:p-4 shadow-brutal min-w-0">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3">
            <div class="w-full sm:max-w-md relative flex-1">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama, slug, atau tahun PEMIRA..."
                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-medium text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select wire:model.live="yearFilter"
                        class="w-full sm:w-auto flex-1 sm:flex-initial bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none cursor-pointer min-h-10">
                    <option value="">Semua Tahun</option>
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>

                @if ($search !== '' || $yearFilter !== '')
                    <button wire:click="$set('search', ''); $set('yearFilter', '')"
                            type="button"
                            class="px-3 py-2 bg-surface border-2 border-ink text-xs font-sans font-bold text-ink/70 hover:text-ink shadow-brutal-sm shrink-0 min-h-10">
                        Reset
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden min-w-0">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-ink bg-surface-muted text-xs font-display font-black uppercase tracking-wider text-ink/80">
                        <th class="p-3.5 sm:p-4">Nama & Identitas</th>
                        <th class="p-3.5 sm:p-4">Tahun</th>
                        <th class="p-3.5 sm:p-4">Status / Fase</th>
                        <th class="p-3.5 sm:p-4">Jadwal Pendaftaran</th>
                        <th class="p-3.5 sm:p-4">Jadwal Voting</th>
                        <th class="p-3.5 sm:p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10 text-xs font-sans">
                    @forelse ($elections as $elec)
                        @php
                            $phase = $elec->currentPhase();
                        @endphp
                        <tr class="hover:bg-surface-muted/50 transition-colors">
                            <td class="p-3.5 sm:p-4">
                                <div class="font-display font-black text-sm text-brand uppercase">
                                    {{ $elec->name }}
                                </div>
                                <div class="text-xs font-mono text-ink/60 mt-0.5">
                                    {{ $elec->slug }}
                                </div>
                            </td>
                            <td class="p-3.5 sm:p-4 font-display font-bold text-ink">
                                {{ $elec->year }}
                            </td>
                            <td class="p-3.5 sm:p-4">
                                <span class="inline-block px-2 py-0.5 text-xs font-display font-black uppercase border border-ink {{ $phase === \App\Enums\ElectionPhase::VOTING ? 'bg-accent text-ink' : ($phase === \App\Enums\ElectionPhase::FINISHED ? 'bg-ink text-surface' : 'bg-surface-muted text-brand') }}">
                                    {{ $phase->badgeText() }}
                                </span>
                            </td>
                            <td class="p-3.5 sm:p-4 text-ink/80">
                                <div>{{ $elec->registration_start_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-ink/60">s.d. {{ $elec->registration_end_at->format('d/m/Y H:i') }} WITA</div>
                                @php $pendingReq = $elec->scheduleChangeRequests->first(); @endphp
                                @if ($pendingReq && $pendingReq->hasRegistrationChange())
                                    <div class="mt-2 p-1.5 bg-amber-100 border border-ink text-[11px] font-sans font-bold text-amber-950 shadow-2xs">
                                        <div class="font-display font-black text-amber-800 uppercase text-[10px] flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-amber-600 rounded-full inline-block"></span>
                                            <span>Menunggu Review:</span>
                                        </div>
                                        <div class="font-mono text-[10px] mt-0.5">{{ $pendingReq->new_registration_start_at?->format('d/m H:i') }} — {{ $pendingReq->new_registration_end_at?->format('d/m H:i') }} WITA</div>
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5 sm:p-4 text-ink/80">
                                <div>{{ $elec->voting_start_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-ink/60">s.d. {{ $elec->voting_end_at->format('d/m/Y H:i') }} WITA</div>
                                @if ($pendingReq && ($pendingReq->hasVotingChange() || ! $pendingReq->hasRegistrationChange()))
                                    <div class="mt-2 p-1.5 bg-amber-100 border border-ink text-[11px] font-sans font-bold text-amber-950 shadow-2xs">
                                        <div class="font-display font-black text-amber-800 uppercase text-[10px] flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-amber-600 rounded-full inline-block"></span>
                                            <span>Menunggu Review:</span>
                                        </div>
                                        <div class="font-mono text-[10px] mt-0.5">{{ $pendingReq->new_voting_start_at->format('d/m H:i') }} — {{ $pendingReq->new_voting_end_at->format('d/m H:i') }} WITA</div>
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5 sm:p-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <x-action-button variant="detail" label="Lihat detail" wire:click="openDetailModal({{ $elec->id }})" />
                                    <x-action-button variant="edit" label="Edit pemilihan" wire:click="openEditModal({{ $elec->id }})" />
                                    <x-action-button variant="delete" label="Hapus pemilihan" wire:click="openDeleteModal({{ $elec->id }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 sm:p-8 text-center min-w-0">
                                <div class="font-display font-bold text-sm sm:text-base text-ink/70 uppercase">
                                    Belum ada data PEMIRA yang terdaftar.
                                </div>
                                <p class="text-xs font-sans text-ink/50 mt-1">
                                    Klik tombol "BUAT PEMIRA" di atas untuk menambahkan pemilihan baru.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="block md:hidden divide-y-2 divide-ink/10">
            @forelse ($elections as $elec)
                @php
                    $phase = $elec->currentPhase();
                @endphp
                <div class="p-3.5 sm:p-4 space-y-3 min-w-0">
                    <div class="flex items-start justify-between gap-2 min-w-0">
                        <div class="min-w-0 flex-1">
                            <div class="font-display font-black text-sm text-brand uppercase wrap-break-word leading-tight">
                                {{ $elec->name }}
                            </div>
                            <div class="text-xs font-mono text-ink/60 mt-0.5 break-all">
                                {{ $elec->slug }} · Tahun {{ $elec->year }}
                            </div>
                        </div>
                        <span class="shrink-0 inline-block px-2 py-0.5 text-xs font-display font-black uppercase border border-ink {{ $phase === \App\Enums\ElectionPhase::VOTING ? 'bg-accent text-ink' : ($phase === \App\Enums\ElectionPhase::FINISHED ? 'bg-ink text-surface' : 'bg-surface-muted text-brand') }}">
                            {{ $phase->badgeText() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-2 text-xs font-sans bg-surface-muted p-2.5 border border-ink/20">
                        <div>
                            <span class="text-xs font-bold text-ink/60 uppercase block">Jadwal Pendaftaran</span>
                            <span class="text-ink/90 font-medium">
                                {{ $elec->registration_start_at->format('d/m/Y H:i') }} — {{ $elec->registration_end_at->format('d/m/Y H:i') }} WITA
                            </span>
                            @php $pendingReq = $elec->scheduleChangeRequests->first(); @endphp
                            @if ($pendingReq && $pendingReq->hasRegistrationChange())
                                <div class="mt-1.5 p-1.5 bg-amber-100 border border-ink text-xs font-sans text-amber-950">
                                    <div class="font-display font-black text-amber-800 uppercase text-[10px]">Menunggu Review Super Admin:</div>
                                    <div class="font-mono text-[11px]">{{ $pendingReq->new_registration_start_at?->format('d/m/Y H:i') }} — {{ $pendingReq->new_registration_end_at?->format('d/m/Y H:i') }} WITA</div>
                                </div>
                            @endif
                        </div>
                        <div>
                            <span class="text-xs font-bold text-ink/60 uppercase block">Jadwal Voting</span>
                            <span class="text-brand font-bold">
                                {{ $elec->voting_start_at->format('d/m/Y H:i') }} — {{ $elec->voting_end_at->format('d/m/Y H:i') }} WITA
                            </span>
                            @if ($pendingReq && ($pendingReq->hasVotingChange() || ! $pendingReq->hasRegistrationChange()))
                                <div class="mt-1.5 p-1.5 bg-amber-100 border border-ink text-xs font-sans text-amber-950">
                                    <div class="font-display font-black text-amber-800 uppercase text-[10px]">Menunggu Review Super Admin:</div>
                                    <div class="font-mono text-[11px]">{{ $pendingReq->new_voting_start_at->format('d/m/Y H:i') }} — {{ $pendingReq->new_voting_end_at->format('d/m/Y H:i') }} WITA</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-ink/10 flex items-center justify-end gap-2">
                        <x-action-button variant="detail" label="Lihat detail" wire:click="openDetailModal({{ $elec->id }})" />
                        <x-action-button variant="edit" label="Edit pemilihan" wire:click="openEditModal({{ $elec->id }})" />
                        <x-action-button variant="delete" label="Hapus pemilihan" wire:click="openDeleteModal({{ $elec->id }})" />
                    </div>
                </div>
            @empty
                <div class="p-6 text-center min-w-0">
                    <div class="font-display font-bold text-sm sm:text-base text-ink/70 uppercase">
                        Belum ada data PEMIRA yang terdaftar.
                    </div>
                    <p class="text-xs font-sans text-ink/50 mt-1">
                        Klik tombol "BUAT PEMIRA" di atas untuk menambahkan pemilihan baru.
                    </p>
                </div>
            @endforelse
        </div>

        @if ($elections->hasPages())
            <div class="p-3 sm:p-4 border-t-2 border-ink bg-surface-muted">
                {{ $elections->links() }}
            </div>
        @endif
    </div>

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0 pr-2">
                        BUAT PEMIRA BARU
                    </h3>
                    <button wire:click="closeCreateModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="createElection" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-3.5 sm:p-5 space-y-3 sm:space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Nama PEMIRA <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   wire:model="name"
                                   placeholder="Contoh: PEMIRA BEM PNB 2026"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('name')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Tahun Pemilihan <span class="text-red-600">*</span>
                            </label>
                            <input type="number"
                                   wire:model="year"
                                   min="2020"
                                   max="2099"
                                   placeholder="2026"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('year')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3 bg-surface-muted border-2 border-ink/40 space-y-2.5 sm:space-y-3">
                            <div class="text-xs font-display font-black uppercase text-brand">
                                JADWAL PENDAFTARAN
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Mulai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model="registration_start_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('registration_start_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Selesai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model="registration_end_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('registration_end_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-surface-muted border-2 border-ink/40 space-y-2.5 sm:space-y-3">
                            <div class="text-xs font-display font-black uppercase text-brand">
                                JADWAL VOTING
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Mulai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model="voting_start_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('voting_start_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Selesai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model="voting_end_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('voting_end_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                        <button wire:click="closeCreateModal"
                                type="button"
                                class="flex-1 sm:flex-initial px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10">
                            BATAL
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 sm:flex-initial px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all disabled:opacity-50 cursor-pointer text-center min-h-10">
                            <span wire:loading.remove wire:target="createElection">BUAT PEMIRA</span>
                            <span wire:loading wire:target="createElection">MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0 pr-2">
                        EDIT PEMIRA
                    </h3>
                    <button wire:click="closeEditModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateElection" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-3.5 sm:p-5 space-y-3 sm:space-y-4 overflow-y-auto flex-1 min-w-0">
                        @if ($activeScheduleRequest)
                            <div class="p-3 bg-amber-100 border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold text-ink space-y-1.5">
                                <div class="font-display font-black uppercase text-amber-900 flex items-center justify-between">
                                    <span>PENGAJUAN SEDANG MENUNGGU PERSETUJUAN</span>
                                    <span class="text-[10px] bg-amber-600 text-surface px-1.5 py-0.5 font-mono">PENDING</span>
                                </div>
                                <p class="text-xs text-ink/80 leading-relaxed font-normal">
                                    Pemilihan ini sedang memiliki permohonan perubahan jadwal yang menunggu persetujuan Super Admin:
                                </p>
                                <div class="text-[11px] font-mono bg-surface p-2 border border-ink/20 space-y-0.5">
                                    <div><strong>Mulai Diajukan:</strong> {{ $activeScheduleRequest->new_voting_start_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
                                    <div><strong>Selesai Diajukan:</strong> {{ $activeScheduleRequest->new_voting_end_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
                                    <div class="italic text-ink/70 font-sans mt-1">&ldquo;{{ $activeScheduleRequest->reason }}&rdquo;</div>
                                </div>
                            </div>
                        @elseif ($isVotingActive && ! auth()->user()?->isSuperAdmin())
                            <div class="p-3 bg-accent/20 border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold text-ink space-y-1.5">
                                <div class="font-display font-black uppercase text-brand">
                                    PERHATIAN: VOTING SEDANG BERLANGSUNG
                                </div>
                                <p class="text-ink/80 text-xs font-normal leading-relaxed">
                                    Karena voting sedang aktif, setiap perubahan jadwal voting yang Anda simpan akan otomatis diajukan ke Super Admin untuk disetujui.
                                </p>
                            </div>
                        @elseif ($isVotingActive && auth()->user()?->isSuperAdmin())
                            <div class="p-3 bg-amber-100 border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold text-ink space-y-1.5">
                                <div class="font-display font-black uppercase text-amber-900">
                                    SUPER ADMIN: VOTING SEDANG BERLANGSUNG
                                </div>
                                <p class="text-ink/80 text-xs font-normal leading-relaxed">
                                    Voting sedang aktif. Sebagai Super Admin, Anda memiliki wewenang untuk memperbarui jadwal voting secara langsung jika diperlukan.
                                </p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Nama PEMIRA <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   wire:model="name"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('name')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Tahun Pemilihan <span class="text-red-600">*</span>
                            </label>
                            <input type="number"
                                   wire:model="year"
                                   min="2020"
                                   max="2099"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                            @error('year')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3 bg-surface-muted border-2 border-ink/40 space-y-2.5 sm:space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-display font-black uppercase text-brand">
                                    JADWAL PENDAFTARAN
                                </div>
                                @if (! auth()->user()?->isSuperAdmin())
                                    <span class="text-xs font-display font-black uppercase px-1.5 py-0.5 bg-amber-600 text-surface">MEMERLUKAN APPROVAL</span>
                                @else
                                    <span class="text-xs font-display font-black uppercase px-1.5 py-0.5 bg-amber-600 text-surface">SUPER ADMIN</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Mulai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model.live="registration_start_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('registration_start_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Selesai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model.live="registration_end_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('registration_end_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-surface-muted border-2 border-ink/40 space-y-2.5 sm:space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-display font-black uppercase text-brand">
                                    JADWAL VOTING
                                </div>
                                @if (! auth()->user()?->isSuperAdmin())
                                    <span class="text-xs font-display font-black uppercase px-1.5 py-0.5 bg-amber-600 text-surface">MEMERLUKAN APPROVAL</span>
                                @else
                                    <span class="text-xs font-display font-black uppercase px-1.5 py-0.5 bg-amber-600 text-surface">SUPER ADMIN</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Mulai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model.live="voting_start_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('voting_start_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-sans font-bold text-ink/70 mb-1">
                                        Waktu Selesai <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           lang="id-ID"
                                           wire:model.live="voting_end_at"
                                           class="w-full bg-surface border-2 border-ink px-2.5 py-1.5 text-xs font-sans text-ink focus:outline-none min-h-9.5" />
                                    @error('voting_end_at')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            @php
                                $hasScheduleChange = ($registration_start_at !== $originalRegistrationStart)
                                    || ($registration_end_at !== $originalRegistrationEnd)
                                    || ($voting_start_at !== $originalVotingStart)
                                    || ($voting_end_at !== $originalVotingEnd);
                            @endphp

                            @if (! auth()->user()?->isSuperAdmin())
                                <div class="pt-2 border-t border-ink/20 space-y-1">
                                    <label class="block text-xs font-sans font-bold text-ink">
                                        Alasan Perubahan Jadwal @if ($hasScheduleChange)<span class="text-red-600">* (Wajib Diisi)</span>@endif
                                    </label>
                                    <textarea wire:model="scheduleChangeReason"
                                              rows="3"
                                              placeholder="Jelaskan alasan mengapa jadwal pendaftaran / voting perlu diubah (minimal 10 karakter)..."
                                              class="w-full bg-surface border-2 border-ink p-2 text-xs font-sans text-ink focus:outline-none focus:bg-surface-muted min-h-16"></textarea>
                                    @error('scheduleChangeReason')
                                        <p class="text-xs font-sans font-bold text-red-600 mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                        <button wire:click="closeEditModal"
                                type="button"
                                class="flex-1 sm:flex-initial px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10">
                            BATAL
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 sm:flex-initial px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all disabled:opacity-50 cursor-pointer text-center min-h-10">
                            <span wire:loading.remove wire:target="updateElection">
                                {{ (! auth()->user()?->isSuperAdmin() && $hasScheduleChange) ? 'AJUKAN PERUBAHAN JADWAL' : 'SIMPAN PEMIRA' }}
                            </span>
                            <span wire:loading wire:target="updateElection">MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    @if ($showDetailModal && $selectedElection)
        @php
            $detailPhase = $selectedElection->currentPhase();
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div class="min-w-0 pr-2">
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">INFORMASI DETAIL</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate">
                            {{ $selectedElection->name }}
                        </h3>
                    </div>
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="w-10 h-10 border-2 border-ink bg-surface hover:bg-accent flex items-center justify-center shrink-0 transition-colors shadow-brutal-sm"
                            aria-label="Tutup modal">
                        <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-3.5 sm:p-5 space-y-3 sm:space-y-4 overflow-y-auto flex-1 min-w-0 text-xs font-sans">
                    <div class="flex items-center justify-between p-3 bg-surface-muted border-2 border-ink gap-2">
                        <div class="min-w-0">
                            <span class="text-xs font-sans font-bold text-ink/60 uppercase block">Status Tahapan</span>
                            <span class="font-display font-black text-xs sm:text-sm text-brand uppercase mt-0.5 block truncate">
                                {{ $detailPhase->label() }}
                            </span>
                        </div>
                        <span class="shrink-0 px-2.5 py-1 text-xs font-display font-black uppercase border border-ink {{ $detailPhase === \App\Enums\ElectionPhase::VOTING ? 'bg-accent text-ink' : ($detailPhase === \App\Enums\ElectionPhase::FINISHED ? 'bg-ink text-surface' : 'bg-surface text-brand') }}">
                            {{ $detailPhase->badgeText() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                        <div class="p-3 bg-surface-muted border-2 border-ink">
                            <span class="text-xs font-sans font-bold text-ink/60 uppercase block">Tahun</span>
                            <span class="font-display font-black text-base text-ink mt-0.5 block">
                                {{ $selectedElection->year }}
                            </span>
                        </div>
                        <div class="p-3 bg-surface-muted border-2 border-ink">
                            <span class="text-xs font-sans font-bold text-ink/60 uppercase block">Slug URL</span>
                            <span class="font-mono text-xs text-ink mt-0.5 block truncate">
                                {{ $selectedElection->slug }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="text-xs font-display font-black text-brand uppercase">
                            RENTANG WAKTU PELAKSANAAN
                        </div>
                        <div class="p-3 bg-surface-muted border-2 border-ink space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-ink/15 gap-1">
                                <span class="font-semibold text-ink/80 text-xs">Pendaftaran Akun</span>
                                <span class="font-bold text-ink sm:text-right text-xs">
                                    {{ $selectedElection->registration_start_at->format('d/m/Y H:i') }} - {{ $selectedElection->registration_end_at->format('d/m/Y H:i') }} WITA
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-1 gap-1">
                                <span class="font-semibold text-ink/80 text-xs">Pemungutan Suara</span>
                                <span class="font-bold text-brand sm:text-right text-xs">
                                    {{ $selectedElection->voting_start_at->format('d/m/Y H:i') }} - {{ $selectedElection->voting_end_at->format('d/m/Y H:i') }} WITA
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="text-xs font-display font-black text-brand uppercase">
                            RINGKASAN DATA PEMILIHAN
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                            <div class="p-2.5 bg-surface-muted border-2 border-ink">
                                <div class="text-xs font-bold text-ink/60 uppercase">Paslon</div>
                                <div class="font-display font-black text-sm text-ink mt-0.5">
                                    {{ $detailHistoricalSummary['candidate_pairs'] ?? 0 }}
                                </div>
                            </div>
                            <div class="p-2.5 bg-surface-muted border-2 border-ink">
                                <div class="text-xs font-bold text-ink/60 uppercase">Partisipasi</div>
                                <div class="font-display font-black text-sm text-brand mt-0.5">
                                    {{ $detailHistoricalSummary['participations'] ?? 0 }}
                                </div>
                            </div>
                            <div class="p-2.5 bg-surface-muted border-2 border-ink">
                                <div class="text-xs font-bold text-ink/60 uppercase">Surat Suara</div>
                                <div class="font-display font-black text-sm text-brand mt-0.5">
                                    {{ $detailHistoricalSummary['ballots'] ?? 0 }}
                                </div>
                            </div>
                            <div class="p-2.5 bg-surface-muted border-2 border-ink">
                                <div class="text-xs font-bold text-ink/60 uppercase">Masukan</div>
                                <div class="font-display font-black text-sm text-ink mt-0.5">
                                    {{ $detailHistoricalSummary['feedbacks'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-between gap-2.5 shrink-0">
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors min-h-10">
                        TUTUP
                    </button>
                    <div class="flex items-center gap-2">
                        <button wire:click="openEditModal({{ $selectedElection->id }}); closeDetailModal()"
                                type="button"
                                class="px-3.5 py-2.5 bg-brand text-surface hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase min-h-10">
                            EDIT
                        </button>
                        <button wire:click="openDeleteModal({{ $selectedElection->id }}); closeDetailModal()"
                                type="button"
                                class="px-3.5 py-2.5 bg-surface hover:bg-red-600 hover:text-white border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase min-h-10">
                            HAPUS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal && $selectedElection)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] flex flex-col min-w-0">
                <div class="p-4 sm:p-6 space-y-3.5 overflow-y-auto flex-1">
                    <h3 class="font-display font-black text-lg text-red-600 uppercase">
                        HAPUS PEMIRA?
                    </h3>
                    <div class="font-display font-extrabold text-base text-brand uppercase wrap-break-word leading-tight">
                        {{ $selectedElection->name }}
                    </div>
                    <p class="text-xs font-sans text-ink/80 leading-relaxed">
                        Tindakan ini tidak dapat dilakukan jika PEMIRA sudah memiliki data pemilihan (suara, partisipasi, masukan) yang harus dilindungi.
                    </p>
                </div>

                <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                    <button wire:click="closeDeleteModal"
                            type="button"
                            class="flex-1 sm:flex-initial px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10">
                        BATAL
                    </button>
                    <button wire:click="deleteElection"
                            type="button"
                            class="flex-1 sm:flex-initial px-5 py-2.5 bg-red-600 text-white hover:bg-red-700 border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all cursor-pointer text-center min-h-10">
                        HAPUS
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
