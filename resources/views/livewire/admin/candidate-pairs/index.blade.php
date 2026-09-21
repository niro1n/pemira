<div class="space-y-4 sm:space-y-6" x-data="candidatePhotoCropper()">
    <input type="file"
           x-ref="photoFileInput"
           @change="onFileSelect($event)"
           accept="image/jpeg,image/png,image/jpg,image/webp"
           class="hidden" />

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal min-w-0">
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-accent/20 border border-ink text-xs font-sans font-bold text-brand uppercase tracking-wider mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>DATA KANDIDAT PEMIRA</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-brand uppercase tracking-tight">
                MANAJEMEN PASLON
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 leading-relaxed">
                Kelola pasangan calon Ketua & Wakil Ketua, foto resmi, visi, misi, dan status kepesertaan.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
            @if ($availableElections->isNotEmpty())
                <div class="flex items-center gap-2 bg-surface-muted border-2 border-ink p-1 shadow-brutal-sm">
                    <span class="text-xs font-display font-black text-brand uppercase px-2 hidden sm:inline-block">PEMIRA:</span>
                    <select wire:model.live="selectedElectionId"
                            class="bg-surface border border-ink px-3 py-1.5 text-xs font-sans font-bold text-ink focus:outline-none cursor-pointer min-h-9.5 flex-1 sm:flex-initial">
                        @foreach ($availableElections as $elec)
                            <option value="{{ $elec->id }}">
                                {{ $elec->name }} ({{ $elec->year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedElection)
                    <button wire:click="openCreateModal"
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer min-h-10.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>TAMBAH PASLON</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-3 sm:p-4 bg-accent/20 border-2 border-ink shadow-brutal flex items-center justify-between gap-3 text-xs sm:text-sm font-sans font-bold text-ink min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 bg-brand shrink-0 inline-block"></span>
                <span class="break-words">{{ session('success') }}</span>
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
                <span class="break-words">{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="p-1 hover:bg-red-200 text-red-900 shrink-0" aria-label="Tutup pesan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if (! $selectedElection)
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-lg sm:text-xl text-brand uppercase">
                    BELUM ADA PEMILIHAN
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    Belum ada data PEMIRA yang terdaftar dalam sistem. Silakan buat pemilihan terlebih dahulu melalui menu Pemilihan.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.elections.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal text-xs font-display font-bold uppercase tracking-wider transition-all">
                    <span>BUAT PEMILIHAN BARU</span>
                </a>
            </div>
        </div>
    @else
        <div class="bg-surface border-2 border-ink p-3 sm:p-4 shadow-brutal min-w-0 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="relative flex-1">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nomor paslon, nama/NIM Ketua atau Wakil..."
                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-medium text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
            </div>

            @if ($search !== '')
                <button wire:click="$set('search', '')"
                        type="button"
                        class="px-3 py-2 bg-surface border-2 border-ink text-xs font-sans font-bold text-ink/70 hover:text-ink shadow-brutal-sm shrink-0 min-h-10">
                    Reset Pencarian
                </button>
            @endif
        </div>

        @if ($candidatePairs->isEmpty())
            <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
                <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                    <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-display font-black text-base sm:text-xl text-brand uppercase">
                        BELUM ADA PASLON
                    </h3>
                    <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                        @if ($search !== '')
                            Tidak ada pasangan calon yang sesuai dengan kata kunci pencarian "{{ $search }}".
                        @else
                            Tambahkan pasangan calon untuk pemilihan {{ $selectedElection->name }}.
                        @endif
                    </p>
                </div>
                <div>
                    <button wire:click="openCreateModal"
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>TAMBAH PASLON</span>
                    </button>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 min-w-0">
                @foreach ($candidatePairs as $pair)
                    @php
                        $leader = $pair->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter;
                        $viceLeader = $pair->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter;
                    @endphp
                    <div class="bg-surface border-2 border-ink shadow-brutal flex flex-col justify-between min-w-0 transition-transform hover:-translate-y-0.5">
                        <div>
                            <div class="bg-brand text-surface px-4 py-3 border-b-2 border-ink flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink shrink-0"></span>
                                    <span class="font-display font-black text-base uppercase tracking-wider truncate">
                                        PASLON {{ $pair->formattedNumber() }}
                                    </span>
                                </div>

                                <button wire:click="toggleStatus({{ $pair->id }})"
                                        type="button"
                                        title="Klik untuk mengubah status aktif"
                                        class="shrink-0 px-2 py-0.5 text-xs font-display font-black uppercase border border-ink transition-colors {{ $pair->is_active ? 'bg-accent text-ink hover:bg-accent/80' : 'bg-surface-muted text-ink/50 hover:bg-surface' }}">
                                    {{ $pair->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                                </button>
                            </div>

                            <div class="p-3.5 sm:p-4 border-b-2 border-ink bg-surface-muted">
                                <div class="w-full aspect-4/3 bg-surface border-2 border-ink overflow-hidden relative shadow-brutal-sm flex items-center justify-center">
                                    @if ($pair->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($pair->photo))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($pair->photo) }}"
                                             alt="Foto Paslon {{ $pair->formattedNumber() }}"
                                             class="w-full h-full object-cover" />
                                    @else
                                        <div class="flex flex-col items-center justify-center text-center p-4 space-y-1">
                                            <div class="w-12 h-12 bg-surface-muted border-2 border-ink flex items-center justify-center mb-1">
                                                <svg class="w-6 h-6 text-ink/40" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                </svg>
                                            </div>
                                            <span class="font-display font-bold text-xs uppercase tracking-wider text-ink/50">
                                                FOTO BELUM DIUNGGAH
                                            </span>
                                        </div>
                                    @endif

                                    <div class="absolute top-2 right-2 px-2 py-0.5 bg-brand text-accent border border-ink font-display font-black text-xs">
                                        #{{ $pair->formattedNumber() }}
                                    </div>
                                </div>
                            </div>

                            <div class="p-3.5 sm:p-4 space-y-3 min-w-0">
                                <div class="bg-surface-muted border-2 border-ink p-2.5 shadow-brutal-sm space-y-0.5">
                                    <div class="flex items-center justify-between text-xs font-sans font-bold uppercase tracking-wider text-brand">
                                        <span>CALON KETUA</span>
                                        <span class="text-ink/50 font-mono">{{ $leader?->nim ?? '-' }}</span>
                                    </div>
                                    <div class="font-display font-black text-sm text-ink uppercase truncate">
                                        {{ $leader?->name ?? 'Belum dipilih' }}
                                    </div>
                                    <div class="text-xs font-sans text-ink/70 truncate">
                                        {{ $leader?->studyProgram?->name ?? '-' }}
                                    </div>
                                </div>

                                <div class="bg-surface-muted border-2 border-ink p-2.5 shadow-brutal-sm space-y-0.5">
                                    <div class="flex items-center justify-between text-xs font-sans font-bold uppercase tracking-wider text-brand">
                                        <span>CALON WAKIL KETUA</span>
                                        <span class="text-ink/50 font-mono">{{ $viceLeader?->nim ?? '-' }}</span>
                                    </div>
                                    <div class="font-display font-black text-sm text-ink uppercase truncate">
                                        {{ $viceLeader?->name ?? 'Belum dipilih' }}
                                    </div>
                                    <div class="text-xs font-sans text-ink/70 truncate">
                                        {{ $viceLeader?->studyProgram?->name ?? '-' }}
                                    </div>
                                </div>

                                <div class="pt-1">
                                    <span class="text-xs font-sans font-bold text-brand uppercase tracking-wider block mb-0.5">RINGKASAN VISI</span>
                                    <p class="text-xs font-sans text-ink/80 italic line-clamp-2 leading-relaxed">
                                        &ldquo;{{ $pair->vision }}&rdquo;
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3.5 sm:p-4 pt-0 border-t border-ink/15 mt-3 grid grid-cols-3 gap-2">
                            <button wire:click="openDetailModal({{ $pair->id }})"
                                    type="button"
                                    class="w-full py-2 bg-surface-muted hover:bg-accent border border-ink font-display font-bold text-xs uppercase text-center transition-colors min-h-9">
                                LIHAT
                            </button>
                            <button wire:click="openEditModal({{ $pair->id }})"
                                    type="button"
                                    class="w-full py-2 bg-brand text-surface hover:bg-brand-dark border border-ink font-display font-bold text-xs uppercase text-center transition-colors min-h-9">
                                EDIT
                            </button>
                            <button wire:click="openDeleteModal({{ $pair->id }})"
                                    type="button"
                                    class="w-full py-2 bg-surface-muted hover:bg-red-600 hover:text-white border border-ink font-display font-bold text-xs uppercase text-center transition-colors min-h-9">
                                HAPUS
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-2xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">{{ $selectedElection?->name }}</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            TAMBAH PASANGAN CALON
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

                <form wire:submit="createCandidatePair" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-3.5 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                    Nomor Urut Paslon <span class="text-red-600">*</span>
                                </label>
                                <input type="number"
                                       wire:model="candidate_number"
                                       min="1"
                                       max="999"
                                       placeholder="Contoh: 1"
                                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                                @error('candidate_number')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                    Status Keaktifan
                                </label>
                                <select wire:model.live="is_active"
                                        class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer min-h-10">
                                    <option value="1" @selected((int) $is_active === 1)>Aktif (Ikut Serta Pemilihan)</option>
                                    <option value="0" @selected((int) $is_active === 0)>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-3 sm:p-4 bg-surface-muted border-2 border-ink space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-display font-black uppercase text-brand">
                                    CALON KETUA <span class="text-red-600">*</span>
                                </label>
                                @if ($selectedLeader)
                                    <button wire:click="clearLeader" type="button" class="text-xs font-sans font-bold text-red-600 hover:underline">
                                        Ganti Mahasiswa
                                    </button>
                                @endif
                            </div>

                            @if ($selectedLeader)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-display font-black text-sm text-brand uppercase truncate">
                                            {{ $selectedLeader->name }}
                                        </div>
                                        <div class="text-xs font-sans text-ink/80 mt-0.5">
                                            NIM: <span class="font-mono font-bold">{{ $selectedLeader->nim }}</span> · {{ $selectedLeader->studyProgram?->name }}
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase shrink-0">
                                        TERPILIH
                                    </span>
                                </div>
                            @else
                                <div class="relative">
                                    <input type="text"
                                           wire:model.live.debounce.300ms="leaderSearch"
                                           placeholder="Ketik NIM atau nama mahasiswa calon Ketua..."
                                           class="w-full bg-surface border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink focus:outline-none min-h-10" />
                                    
                                    @if ($this->leaderSearchResults->isNotEmpty())
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink shadow-brutal max-h-48 overflow-y-auto z-20 divide-y divide-ink/10">
                                            @foreach ($this->leaderSearchResults as $student)
                                                <button wire:click="selectLeader({{ $student->id }})"
                                                        type="button"
                                                        class="w-full text-left p-2.5 hover:bg-accent/20 transition-colors flex items-center justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-display font-bold text-xs text-ink uppercase truncate">
                                                            {{ $student->name }}
                                                        </div>
                                                        <div class="text-xs font-sans text-ink/70">
                                                            <span class="font-mono font-semibold">{{ $student->nim }}</span> · {{ $student->studyProgram?->name }}
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-display font-bold uppercase px-2 py-1 bg-brand text-surface shrink-0">
                                                        PILIH
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif (strlen(trim($leaderSearch)) >= 2)
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink p-3 text-xs font-sans text-ink/60 text-center shadow-brutal z-20">
                                            Tidak ada mahasiswa ditemukan dengan kata kunci "{{ $leaderSearch }}".
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @error('leader_id')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3 sm:p-4 bg-surface-muted border-2 border-ink space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-display font-black uppercase text-brand">
                                    CALON WAKIL KETUA <span class="text-red-600">*</span>
                                </label>
                                @if ($selectedViceLeader)
                                    <button wire:click="clearViceLeader" type="button" class="text-xs font-sans font-bold text-red-600 hover:underline">
                                        Ganti Mahasiswa
                                    </button>
                                @endif
                            </div>

                            @if ($selectedViceLeader)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-display font-black text-sm text-brand uppercase truncate">
                                            {{ $selectedViceLeader->name }}
                                        </div>
                                        <div class="text-xs font-sans text-ink/80 mt-0.5">
                                            NIM: <span class="font-mono font-bold">{{ $selectedViceLeader->nim }}</span> · {{ $selectedViceLeader->studyProgram?->name }}
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase shrink-0">
                                        TERPILIH
                                    </span>
                                </div>
                            @else
                                <div class="relative">
                                    <input type="text"
                                           wire:model.live.debounce.300ms="viceLeaderSearch"
                                           placeholder="Ketik NIM atau nama mahasiswa calon Wakil Ketua..."
                                           class="w-full bg-surface border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink focus:outline-none min-h-10" />
                                    
                                    @if ($this->viceLeaderSearchResults->isNotEmpty())
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink shadow-brutal max-h-48 overflow-y-auto z-20 divide-y divide-ink/10">
                                            @foreach ($this->viceLeaderSearchResults as $student)
                                                <button wire:click="selectViceLeader({{ $student->id }})"
                                                        type="button"
                                                        class="w-full text-left p-2.5 hover:bg-accent/20 transition-colors flex items-center justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-display font-bold text-xs text-ink uppercase truncate">
                                                            {{ $student->name }}
                                                        </div>
                                                        <div class="text-xs font-sans text-ink/70">
                                                            <span class="font-mono font-semibold">{{ $student->nim }}</span> · {{ $student->studyProgram?->name }}
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-display font-bold uppercase px-2 py-1 bg-brand text-surface shrink-0">
                                                        PILIH
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif (strlen(trim($viceLeaderSearch)) >= 2)
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink p-3 text-xs font-sans text-ink/60 text-center shadow-brutal z-20">
                                            Tidak ada mahasiswa ditemukan dengan kata kunci "{{ $viceLeaderSearch }}".
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @error('vice_leader_id')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3 sm:p-4 bg-surface-muted border-2 border-ink space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-display font-black uppercase tracking-wider text-brand">
                                    FOTO RESMI PASLON (OPSIONAL)
                                </label>
                                <span class="text-xs font-sans text-ink/60 uppercase font-semibold">
                                    Rasio Portrait (Vertikal)
                                </span>
                            </div>

                            @php
                                $photoPreviewUrl = null;
                                if ($photo && method_exists($photo, 'temporaryUrl')) {
                                    try {
                                        $photoPreviewUrl = $photo->temporaryUrl();
                                    } catch (\Throwable $e) {
                                        $photoPreviewUrl = null;
                                    }
                                }
                            @endphp

                            @if ($photo && $photoPreviewUrl)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm space-y-3">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <div class="w-full sm:w-36 aspect-3/4 bg-surface-muted border-2 border-ink overflow-hidden shrink-0 shadow-brutal-sm">
                                            <img src="{{ $photoPreviewUrl }}" alt="Preview Hasil Crop" class="w-full h-full object-cover object-top" />
                                        </div>
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase">
                                                <span>✓ HASIL CROP SIAP DISIMPAN</span>
                                            </div>
                                            <p class="text-xs font-sans text-ink/70">
                                                Foto telah berhasil dipotong dan siap disimpan ke data Paslon.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-ink/15 flex flex-wrap items-center gap-2">
                                        <button type="button"
                                                @click="$refs.photoFileInput.click()"
                                                class="px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors cursor-pointer">
                                            GANTI FOTO & CROP ULANG
                                        </button>
                                        <button type="button"
                                                wire:click="cancelNewPhoto"
                                                class="px-3 py-1.5 bg-surface text-ink hover:bg-red-600 hover:text-white border-2 border-ink text-xs font-display font-bold uppercase transition-colors cursor-pointer">
                                            HAPUS FOTO
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-surface border-2 border-dashed border-ink flex flex-col items-center justify-center text-center space-y-2">
                                    <div class="w-12 h-12 bg-surface-muted border-2 border-ink flex items-center justify-center shadow-brutal-sm">
                                        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-display font-black text-xs uppercase tracking-wider text-brand block">
                                            UNGGAH & CROP FOTO PASLON
                                        </span>
                                        <p class="text-xs font-sans text-ink/60 mt-0.5 max-w-xs mx-auto">
                                            Pilih foto dari komputer, sesuaikan posisi wajah dengan editor crop, lalu simpan.
                                        </p>
                                    </div>
                                    <button type="button"
                                            @click="$refs.photoFileInput.click()"
                                            class="mt-1 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                                        PILIH & CROP FOTO
                                    </button>
                                </div>
                            @endif

                            @error('photo')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Visi Paslon <span class="text-red-600">*</span>
                            </label>
                            <textarea wire:model="vision"
                                      rows="3"
                                      placeholder="Tuliskan rumusan visi pasangan calon..."
                                      class="w-full bg-surface-muted border-2 border-ink p-3 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface"></textarea>
                            @error('vision')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink">
                                    Misi Paslon <span class="text-red-600">*</span>
                                </label>
                                <span class="text-[11px] font-sans text-ink/60 font-medium">
                                    Minimal 1 butir misi
                                </span>
                            </div>

                            @error('missionItems')
                                <p class="text-xs font-sans font-bold text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="space-y-2">
                                @foreach ($missionItems as $index => $item)
                                    <div wire:key="create-mission-item-{{ $index }}" class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-2 bg-brand text-accent font-display font-black text-xs border-2 border-ink shadow-brutal-sm shrink-0 select-none">
                                                MISI {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                            </span>

                                            <input type="text"
                                                   wire:model="missionItems.{{ $index }}.content"
                                                   placeholder="Tuliskan butir misi ke-{{ $index + 1 }}..."
                                                   class="flex-1 bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />

                                            @if (count($missionItems) > 1)
                                                <button type="button"
                                                        wire:click="removeMission({{ $index }})"
                                                        class="w-10 h-10 bg-surface hover:bg-red-600 hover:text-white border-2 border-ink shadow-brutal-sm flex items-center justify-center shrink-0 transition-colors cursor-pointer text-ink font-bold text-base"
                                                        title="Hapus butir misi {{ $index + 1 }}"
                                                        aria-label="Hapus butir misi {{ $index + 1 }}">
                                                    &times;
                                                </button>
                                            @endif
                                        </div>

                                        @error("missionItems.{$index}.content")
                                            <p class="text-xs font-sans font-bold text-red-600 pl-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <button type="button"
                                    wire:click="addMission"
                                    class="w-full py-2.5 bg-surface hover:bg-accent border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors flex items-center justify-center gap-2 cursor-pointer min-h-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ TAMBAH MISI</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                        <button wire:click="closeCreateModal"
                                type="button"
                                class="flex-1 sm:flex-initial px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10 cursor-pointer">
                            BATAL
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 sm:flex-initial px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all disabled:opacity-50 cursor-pointer text-center min-h-10">
                            <span wire:loading.remove wire:target="createCandidatePair">SIMPAN PASLON</span>
                            <span wire:loading wire:target="createCandidatePair">MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-2xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div>
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">{{ $selectedElection?->name }}</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate min-w-0">
                            EDIT PASLON {{ $selectedCandidatePair?->formattedNumber() }}
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

                <form wire:submit="updateCandidatePair" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-3.5 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                    Nomor Urut Paslon <span class="text-red-600">*</span>
                                </label>
                                <input type="number"
                                       wire:model="candidate_number"
                                       min="1"
                                       max="999"
                                       class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
                                @error('candidate_number')
                                    <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                    Status Keaktifan
                                </label>
                                <select wire:model.live="is_active"
                                        class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-bold text-ink shadow-brutal-sm focus:outline-none focus:bg-surface cursor-pointer min-h-10">
                                    <option value="1" @selected((int) $is_active === 1)>Aktif (Ikut Serta Pemilihan)</option>
                                    <option value="0" @selected((int) $is_active === 0)>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-3 sm:p-4 bg-surface-muted border-2 border-ink space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-display font-black uppercase text-brand">
                                    CALON KETUA <span class="text-red-600">*</span>
                                </label>
                                @if ($selectedLeader)
                                    <button wire:click="clearLeader" type="button" class="text-xs font-sans font-bold text-red-600 hover:underline">
                                        Ganti Mahasiswa
                                    </button>
                                @endif
                            </div>

                            @if ($selectedLeader)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-display font-black text-sm text-brand uppercase truncate">
                                            {{ $selectedLeader->name }}
                                        </div>
                                        <div class="text-xs font-sans text-ink/80 mt-0.5">
                                            NIM: <span class="font-mono font-bold">{{ $selectedLeader->nim }}</span> · {{ $selectedLeader->studyProgram?->name }}
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase shrink-0">
                                        TERPILIH
                                    </span>
                                </div>
                            @else
                                <div class="relative">
                                    <input type="text"
                                           wire:model.live.debounce.300ms="leaderSearch"
                                           placeholder="Ketik NIM atau nama mahasiswa calon Ketua..."
                                           class="w-full bg-surface border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink focus:outline-none min-h-10" />
                                    
                                    @if ($this->leaderSearchResults->isNotEmpty())
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink shadow-brutal max-h-48 overflow-y-auto z-20 divide-y divide-ink/10">
                                            @foreach ($this->leaderSearchResults as $student)
                                                <button wire:click="selectLeader({{ $student->id }})"
                                                        type="button"
                                                        class="w-full text-left p-2.5 hover:bg-accent/20 transition-colors flex items-center justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-display font-bold text-xs text-ink uppercase truncate">
                                                            {{ $student->name }}
                                                        </div>
                                                        <div class="text-xs font-sans text-ink/70">
                                                            <span class="font-mono font-semibold">{{ $student->nim }}</span> · {{ $student->studyProgram?->name }}
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-display font-bold uppercase px-2 py-1 bg-brand text-surface shrink-0">
                                                        PILIH
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif (strlen(trim($leaderSearch)) >= 2)
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink p-3 text-xs font-sans text-ink/60 text-center shadow-brutal z-20">
                                            Tidak ada mahasiswa ditemukan dengan kata kunci "{{ $leaderSearch }}".
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @error('leader_id')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3 sm:p-4 bg-surface-muted border-2 border-ink space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-display font-black uppercase text-brand">
                                    CALON WAKIL KETUA <span class="text-red-600">*</span>
                                </label>
                                @if ($selectedViceLeader)
                                    <button wire:click="clearViceLeader" type="button" class="text-xs font-sans font-bold text-red-600 hover:underline">
                                        Ganti Mahasiswa
                                    </button>
                                @endif
                            </div>

                            @if ($selectedViceLeader)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-display font-black text-sm text-brand uppercase truncate">
                                            {{ $selectedViceLeader->name }}
                                        </div>
                                        <div class="text-xs font-sans text-ink/80 mt-0.5">
                                            NIM: <span class="font-mono font-bold">{{ $selectedViceLeader->nim }}</span> · {{ $selectedViceLeader->studyProgram?->name }}
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase shrink-0">
                                        TERPILIH
                                    </span>
                                </div>
                            @else
                                <div class="relative">
                                    <input type="text"
                                           wire:model.live.debounce.300ms="viceLeaderSearch"
                                           placeholder="Ketik NIM atau nama mahasiswa calon Wakil Ketua..."
                                           class="w-full bg-surface border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink focus:outline-none min-h-10" />
                                    
                                    @if ($this->viceLeaderSearchResults->isNotEmpty())
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink shadow-brutal max-h-48 overflow-y-auto z-20 divide-y divide-ink/10">
                                            @foreach ($this->viceLeaderSearchResults as $student)
                                                <button wire:click="selectViceLeader({{ $student->id }})"
                                                        type="button"
                                                        class="w-full text-left p-2.5 hover:bg-accent/20 transition-colors flex items-center justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-display font-bold text-xs text-ink uppercase truncate">
                                                            {{ $student->name }}
                                                        </div>
                                                        <div class="text-xs font-sans text-ink/70">
                                                            <span class="font-mono font-semibold">{{ $student->nim }}</span> · {{ $student->studyProgram?->name }}
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-display font-bold uppercase px-2 py-1 bg-brand text-surface shrink-0">
                                                        PILIH
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif (strlen(trim($viceLeaderSearch)) >= 2)
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-surface border-2 border-ink p-3 text-xs font-sans text-ink/60 text-center shadow-brutal z-20">
                                            Tidak ada mahasiswa ditemukan dengan kata kunci "{{ $viceLeaderSearch }}".
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @error('vice_leader_id')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-3 sm:p-4 bg-surface-muted border-2 border-ink space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-display font-black uppercase tracking-wider text-brand">
                                    FOTO RESMI PASLON
                                </label>
                                <span class="text-xs font-sans text-ink/60 uppercase font-semibold">
                                    Rasio Portrait (Vertikal)
                                </span>
                            </div>

                            @php
                                $editPhotoPreviewUrl = null;
                                if ($photo && method_exists($photo, 'temporaryUrl')) {
                                    try {
                                        $editPhotoPreviewUrl = $photo->temporaryUrl();
                                    } catch (\Throwable $e) {
                                        $editPhotoPreviewUrl = null;
                                    }
                                }
                            @endphp

                            @if ($photo && $editPhotoPreviewUrl)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm space-y-3">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <div class="w-full sm:w-36 aspect-3/4 bg-surface-muted border-2 border-ink overflow-hidden shrink-0 shadow-brutal-sm">
                                            <img src="{{ $editPhotoPreviewUrl }}" alt="Preview Foto Baru" class="w-full h-full object-cover object-top" />
                                        </div>
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase">
                                                <span>✓ FOTO BARU (HASIL CROP)</span>
                                            </div>
                                            <p class="text-xs font-sans text-ink/70">
                                                Foto baru akan menggantikan foto lama saat Anda menyimpan perubahan.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-ink/15 flex flex-wrap items-center gap-2">
                                        <button type="button"
                                                @click="$refs.photoFileInput.click()"
                                                class="px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors cursor-pointer">
                                            GANTI & CROP ULANG
                                        </button>
                                        <button type="button"
                                                wire:click="cancelNewPhoto"
                                                class="px-3 py-1.5 bg-surface text-ink hover:bg-surface-muted border-2 border-ink text-xs font-display font-bold uppercase transition-colors cursor-pointer">
                                            BATALKAN FOTO BARU
                                        </button>
                                    </div>
                                </div>
                            @elseif ($existingPhoto && ! $removePhoto)
                                <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm space-y-3">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <div class="w-full sm:w-36 aspect-3/4 bg-surface-muted border-2 border-ink overflow-hidden shrink-0 shadow-brutal-sm">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($existingPhoto) }}" alt="Foto Saat Ini" class="w-full h-full object-cover object-top" />
                                        </div>
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-surface-muted text-ink border border-ink text-xs font-display font-black uppercase">
                                                <span>FOTO SAAT INI TERSIMPAN</span>
                                            </div>
                                            <p class="text-xs font-sans text-ink/70">
                                                Foto ini saat ini aktif ditampilkan pada Landing Page.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-ink/15 flex flex-wrap items-center gap-2">
                                        <button type="button"
                                                @click="$refs.photoFileInput.click()"
                                                class="px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors cursor-pointer">
                                            GANTI FOTO (CROP)
                                        </button>
                                        <button type="button"
                                                wire:click="$set('removePhoto', true)"
                                                class="px-3 py-1.5 bg-surface text-ink hover:bg-red-600 hover:text-white border-2 border-ink text-xs font-display font-bold uppercase transition-colors cursor-pointer">
                                            HAPUS FOTO
                                        </button>
                                    </div>
                                </div>
                            @elseif ($removePhoto)
                                <div class="p-3 bg-red-50 border-2 border-red-400 text-xs font-sans text-red-900 flex items-center justify-between gap-3 shadow-brutal-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 bg-red-600 inline-block shrink-0"></span>
                                        <span>Foto akan dihapus secara permanen saat Anda menyimpan perubahan.</span>
                                    </div>
                                    <button type="button"
                                            wire:click="$set('removePhoto', false)"
                                            class="px-2.5 py-1 bg-surface border border-ink text-xs font-display font-bold uppercase hover:bg-surface-muted cursor-pointer shrink-0">
                                        BATAL HAPUS
                                    </button>
                                </div>
                            @else
                                <div class="p-4 bg-surface border-2 border-dashed border-ink flex flex-col items-center justify-center text-center space-y-2">
                                    <div class="w-12 h-12 bg-surface-muted border-2 border-ink flex items-center justify-center shadow-brutal-sm">
                                        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-sans font-bold text-ink">Belum Ada Foto Paslon</p>
                                        <p class="text-xs font-sans text-ink/60 mt-0.5">Format: JPG, JPEG, PNG, WEBP (Maks 10MB)</p>
                                    </div>
                                    <button type="button"
                                            @click="$refs.photoFileInput.click()"
                                            class="mt-1 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all cursor-pointer">
                                        PILIH & CROP FOTO
                                    </button>
                                </div>
                            @endif

                            @error('photo')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Visi Paslon <span class="text-red-600">*</span>
                            </label>
                            <textarea wire:model="vision"
                                      rows="3"
                                      class="w-full bg-surface-muted border-2 border-ink p-3 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface"></textarea>
                            @error('vision')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink">
                                    Misi Paslon <span class="text-red-600">*</span>
                                </label>
                                <span class="text-[11px] font-sans text-ink/60 font-medium">
                                    Minimal 1 butir misi
                                </span>
                            </div>

                            @error('missionItems')
                                <p class="text-xs font-sans font-bold text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="space-y-2">
                                @foreach ($missionItems as $index => $item)
                                    <div wire:key="edit-mission-item-{{ $index }}" class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-2 bg-brand text-accent font-display font-black text-xs border-2 border-ink shadow-brutal-sm shrink-0 select-none">
                                                MISI {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                            </span>

                                            <input type="text"
                                                   wire:model="missionItems.{{ $index }}.content"
                                                   placeholder="Tuliskan butir misi ke-{{ $index + 1 }}..."
                                                   class="flex-1 bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />

                                            @if (count($missionItems) > 1)
                                                <button type="button"
                                                        wire:click="removeMission({{ $index }})"
                                                        class="w-10 h-10 bg-surface hover:bg-red-600 hover:text-white border-2 border-ink shadow-brutal-sm flex items-center justify-center shrink-0 transition-colors cursor-pointer text-ink font-bold text-base"
                                                        title="Hapus butir misi {{ $index + 1 }}"
                                                        aria-label="Hapus butir misi {{ $index + 1 }}">
                                                    &times;
                                                </button>
                                            @endif
                                        </div>

                                        @error("missionItems.{$index}.content")
                                            <p class="text-xs font-sans font-bold text-red-600 pl-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <button type="button"
                                    wire:click="addMission"
                                    class="w-full py-2.5 bg-surface hover:bg-accent border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors flex items-center justify-center gap-2 cursor-pointer min-h-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ TAMBAH MISI</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                        <button wire:click="closeEditModal"
                                type="button"
                                class="flex-1 sm:flex-initial px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10 cursor-pointer">
                            BATAL
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 sm:flex-initial px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all disabled:opacity-50 cursor-pointer text-center min-h-10">
                            <span wire:loading.remove wire:target="updateCandidatePair">SIMPAN PERUBAHAN</span>
                            <span wire:loading wire:target="updateCandidatePair">MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDetailModal && $selectedCandidatePair)
        @php
            $detailLeader = $selectedCandidatePair->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter;
            $detailVice = $selectedCandidatePair->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter;
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-2xl bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] sm:max-h-[90vh] flex flex-col min-w-0">
                <div class="px-4 py-3 sm:px-5 sm:py-4 border-b-2 border-ink flex items-center justify-between bg-surface-muted shrink-0">
                    <div class="min-w-0 pr-2">
                        <span class="text-xs font-display font-bold uppercase tracking-wider text-ink/60 block">{{ $selectedCandidatePair->election?->name }}</span>
                        <h3 class="font-display font-black text-sm sm:text-lg text-brand uppercase truncate">
                            DETAIL PASLON {{ $selectedCandidatePair->formattedNumber() }}
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

                <div class="p-3.5 sm:p-5 space-y-4 overflow-y-auto flex-1 min-w-0 text-xs font-sans">
                    <div class="flex items-center justify-between p-3 bg-surface-muted border-2 border-ink gap-2">
                        <div>
                            <span class="text-xs font-bold text-ink/60 uppercase block">Status Keikutsertaan</span>
                            <span class="font-display font-black text-sm text-brand uppercase mt-0.5 block">
                                {{ $selectedCandidatePair->is_active ? 'AKTIF (PESERTA PEMILIHAN)' : 'TIDAK AKTIF' }}
                            </span>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-display font-black uppercase border border-ink {{ $selectedCandidatePair->is_active ? 'bg-accent text-ink' : 'bg-surface text-ink/60' }}">
                            {{ $selectedCandidatePair->is_active ? 'AKTIF' : 'INAKTIF' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1 bg-surface-muted border-2 border-ink p-2 flex items-center justify-center">
                            @if ($selectedCandidatePair->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($selectedCandidatePair->photo))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($selectedCandidatePair->photo) }}"
                                     alt="Foto Paslon {{ $selectedCandidatePair->formattedNumber() }}"
                                     class="w-full aspect-3/4 object-cover border border-ink" />
                            @else
                                <div class="w-full aspect-3/4 bg-surface border border-ink flex flex-col items-center justify-center p-3 text-center">
                                    <svg class="w-8 h-8 text-ink/30 mb-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                    <span class="text-xs font-sans font-bold text-ink/40 uppercase">Tanpa Foto</span>
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-2 space-y-3">
                            <div class="p-3 bg-surface-muted border-2 border-ink space-y-1">
                                <span class="text-xs font-display font-black text-brand uppercase tracking-wider block">CALON KETUA</span>
                                <div class="font-display font-black text-sm text-ink uppercase">
                                    {{ $detailLeader?->name ?? '-' }}
                                </div>
                                <div class="text-xs font-sans text-ink/80">
                                    NIM: <span class="font-mono font-bold">{{ $detailLeader?->nim ?? '-' }}</span>
                                </div>
                                <div class="text-xs font-sans text-ink/70">
                                    Program Studi: <span class="font-semibold">{{ $detailLeader?->studyProgram?->name ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="p-3 bg-surface-muted border-2 border-ink space-y-1">
                                <span class="text-xs font-display font-black text-brand uppercase tracking-wider block">CALON WAKIL KETUA</span>
                                <div class="font-display font-black text-sm text-ink uppercase">
                                    {{ $detailVice?->name ?? '-' }}
                                </div>
                                <div class="text-xs font-sans text-ink/80">
                                    NIM: <span class="font-mono font-bold">{{ $detailVice?->nim ?? '-' }}</span>
                                </div>
                                <div class="text-xs font-sans text-ink/70">
                                    Program Studi: <span class="font-semibold">{{ $detailVice?->studyProgram?->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-xs font-display font-black text-brand uppercase block">VISI</span>
                        <div class="p-3 bg-surface-muted border-2 border-ink leading-relaxed font-sans text-ink whitespace-pre-line">
                            {{ $selectedCandidatePair->vision }}
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-xs font-display font-black text-brand uppercase block">MISI</span>
                        <div class="p-3 bg-surface-muted border-2 border-ink space-y-2">
                            @if ($selectedCandidatePair->candidateMissions->isNotEmpty())
                                <ol class="space-y-2">
                                    @foreach ($selectedCandidatePair->candidateMissions as $idx => $missionItem)
                                        <li class="flex items-start gap-2.5 text-xs font-sans text-ink">
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 bg-brand text-accent font-mono font-bold text-[10px] border border-ink shrink-0">
                                                {{ str_pad((string) ($missionItem->sort_order ?: ($idx + 1)), 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="leading-relaxed flex-1 break-words">
                                                {{ $missionItem->content }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ol>
                            @elseif (! empty($selectedCandidatePair->mission))
                                <div class="leading-relaxed font-sans text-ink whitespace-pre-line">
                                    {{ $selectedCandidatePair->mission }}
                                </div>
                            @else
                                <p class="text-xs font-sans text-ink/60 italic">Belum ada butir misi yang ditetapkan.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-between gap-2.5 shrink-0">
                    <button wire:click="closeDetailModal"
                            type="button"
                            class="px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors min-h-10 cursor-pointer">
                        TUTUP
                    </button>
                    <div class="flex items-center gap-2">
                        <button wire:click="openEditModal({{ $selectedCandidatePair->id }}); closeDetailModal()"
                                type="button"
                                class="px-3.5 py-2.5 bg-brand text-surface hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase min-h-10 cursor-pointer">
                            EDIT
                        </button>
                        <button wire:click="openDeleteModal({{ $selectedCandidatePair->id }}); closeDetailModal()"
                                type="button"
                                class="px-3.5 py-2.5 bg-surface hover:bg-red-600 hover:text-white border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase min-h-10 cursor-pointer">
                            HAPUS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal && $selectedCandidatePair)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-2.5 sm:p-4 bg-ink/70 backdrop-blur-xs">
            <div class="w-full sm:max-w-md bg-surface border-2 border-ink shadow-brutal max-h-[92dvh] flex flex-col min-w-0">
                <div class="p-4 sm:p-6 space-y-3.5 overflow-y-auto flex-1">
                    <h3 class="font-display font-black text-lg text-red-600 uppercase">
                        HAPUS PASANGAN CALON?
                    </h3>
                    <div class="font-display font-extrabold text-base text-brand uppercase break-words leading-tight">
                        PASLON {{ $selectedCandidatePair->formattedNumber() }}
                    </div>
                    <div class="text-xs font-sans text-ink/80 space-y-1 bg-surface-muted p-2.5 border border-ink/20">
                        <div>Ketua: <span class="font-bold">{{ $selectedCandidatePair->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter?->name ?? '-' }}</span></div>
                        <div>Wakil: <span class="font-bold">{{ $selectedCandidatePair->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter?->name ?? '-' }}</span></div>
                    </div>
                    <p class="text-xs font-sans text-ink/80 leading-relaxed">
                        Pasangan calon tidak dapat dihapus jika sudah digunakan dalam data pemilihan (surat suara masuk).
                    </p>
                </div>

                <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex flex-row items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                    <button wire:click="closeDeleteModal"
                            type="button"
                            class="flex-1 sm:flex-initial px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10 cursor-pointer">
                        BATAL
                    </button>
                    <button wire:click="deleteCandidatePair"
                            type="button"
                            class="flex-1 sm:flex-initial px-5 py-2.5 bg-red-600 text-white hover:bg-red-700 border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all cursor-pointer text-center min-h-10">
                        HAPUS
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div x-show="showCropModal"
         x-cloak
         class="fixed inset-0 z-[60] overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-ink/80 backdrop-blur-xs"
         role="dialog"
         aria-modal="true"
         aria-labelledby="cropper-modal-title">

        <div class="w-full max-w-lg bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0"
             @click.outside="if (!isUploading) cancelCrop()">

            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b-2 border-ink bg-brand text-surface flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                    <h3 id="cropper-modal-title" class="font-display font-extrabold text-sm sm:text-base uppercase tracking-wider text-surface leading-none">
                        CROP & ATUR POSISI FOTO
                    </h3>
                </div>
                <button type="button"
                        @click="if (!isUploading) cancelCrop()"
                        :disabled="isUploading"
                        class="p-1.5 bg-surface text-ink hover:bg-accent border border-ink shadow-brutal-sm transition-colors cursor-pointer disabled:opacity-50"
                        aria-label="Tutup editor crop">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-5 space-y-4">
                <div class="p-2.5 bg-surface-muted border-2 border-ink text-xs font-sans text-ink/80 flex items-start gap-2">
                    <span class="text-brand font-display font-bold text-sm leading-none shrink-0 mt-0.5">ℹ</span>
                    <span>Geser foto untuk menempatkan wajah di dalam bingkai, gunakan kontrol zoom di bawah untuk menyesuaikan ukuran.</span>
                </div>

                <div class="bg-ink/90 border-2 border-ink p-3 flex flex-col items-center justify-center shadow-brutal-sm overflow-hidden">
                    <div class="relative overflow-hidden bg-black border-2 border-accent shadow-brutal select-none mx-auto flex items-center justify-center cursor-grab active:cursor-grabbing touch-none"
                         :style="`width: ${cropWidth}px; height: ${cropHeight}px;`"
                         @mousedown="onPointerDown($event)"
                         @mousemove="onPointerMove($event)"
                         @mouseup="onPointerUp()"
                         @mouseleave="onPointerUp()"
                         @touchstart="onPointerDown($event)"
                         @touchmove="onPointerMove($event)"
                         @touchend="onPointerUp()"
                         @wheel="onWheel($event)">

                        <template x-if="imageSrc">
                            <img :src="imageSrc"
                                 alt="Area Crop"
                                 class="absolute pointer-events-none max-w-none transition-none select-none"
                                 :style="`
                                    width: ${getImageDimensions().width}px;
                                    height: ${getImageDimensions().height}px;
                                    transform: translate(${panX}px, ${panY}px);
                                 `" />
                        </template>

                        <div class="absolute inset-0 pointer-events-none grid grid-cols-3 grid-rows-3 border border-white/40">
                            <div class="border-r border-b border-white/20"></div>
                            <div class="border-r border-b border-white/20"></div>
                            <div class="border-b border-white/20"></div>
                            <div class="border-r border-b border-white/20"></div>
                            <div class="border-r border-b border-white/20"></div>
                            <div class="border-b border-white/20"></div>
                            <div class="border-r border-b border-white/20"></div>
                            <div class="border-r border-b border-white/20"></div>
                            <div></div>
                        </div>
                    </div>

                    <span class="text-xs font-sans font-bold uppercase tracking-wider text-surface/70 mt-2">
                        Rasio Portrait (Vertikal)
                    </span>
                </div>

                <div class="p-3 bg-surface-muted border-2 border-ink space-y-2">
                    <div class="flex items-center justify-between text-xs font-display font-bold uppercase text-brand">
                        <span>KONTROL ZOOM</span>
                        <span class="font-mono text-ink/70" x-text="`${Math.round(zoom * 100)}%`">100%</span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button type="button"
                                @click="zoomOut()"
                                :disabled="zoom <= minZoom"
                                class="w-8 h-8 bg-surface border-2 border-ink shadow-brutal-sm font-display font-black text-sm flex items-center justify-center hover:bg-accent disabled:opacity-40 disabled:hover:bg-surface cursor-pointer"
                                aria-label="Perkecil zoom">
                            −
                        </button>

                        <input type="range"
                               min="1.0"
                               max="3.5"
                               step="0.05"
                               :value="zoom"
                               @input="setZoom($event.target.value)"
                               class="flex-1 accent-brand h-2 bg-surface border border-ink cursor-pointer" />

                        <button type="button"
                                @click="zoomIn()"
                                :disabled="zoom >= maxZoom"
                                class="w-8 h-8 bg-surface border-2 border-ink shadow-brutal-sm font-display font-black text-sm flex items-center justify-center hover:bg-accent disabled:opacity-40 disabled:hover:bg-surface cursor-pointer"
                                aria-label="Perbesar zoom">
                            +
                        </button>

                        <button type="button"
                                @click="resetCrop()"
                                class="px-2.5 py-1.5 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase hover:bg-accent cursor-pointer">
                            RESET
                        </button>
                    </div>
                </div>

                <div x-show="cropperError" x-cloak class="p-2.5 bg-red-100 border-2 border-ink text-xs font-sans font-bold text-red-900">
                    <span x-text="cropperError"></span>
                </div>
            </div>

            <div class="p-3.5 sm:p-4 border-t-2 border-ink bg-surface-muted flex items-center justify-end gap-2.5 sm:gap-3 shrink-0">
                <button type="button"
                        @click="cancelCrop()"
                        :disabled="isUploading"
                        class="px-4 py-2.5 bg-surface border-2 border-ink text-xs font-display font-bold uppercase tracking-wider hover:bg-surface-muted transition-colors text-center min-h-10 cursor-pointer disabled:opacity-50">
                    BATAL
                </button>
                <button type="button"
                        @click="applyCrop()"
                        :disabled="isUploading"
                        class="px-5 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-all disabled:opacity-50 cursor-pointer text-center min-h-10 flex items-center gap-2">
                    <span x-show="!isUploading">GUNAKAN FOTO</span>
                    <span x-show="isUploading" x-cloak class="flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-accent" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>MEMPROSES...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function candidatePhotoCropper() {
    return {
        showCropModal: false,
        imageSrc: null,
        imageLoaded: false,
        naturalWidth: 0,
        naturalHeight: 0,
        zoom: 1.0,
        minZoom: 1.0,
        maxZoom: 3.5,
        panX: 0,
        panY: 0,
        isDragging: false,
        startX: 0,
        startY: 0,
        startPanX: 0,
        startPanY: 0,
        cropWidth: 270,
        cropHeight: 360,
        isUploading: false,
        cropperError: null,

        init() {
            this.updateCropBoxSize();
            window.addEventListener('resize', () => {
                if (this.showCropModal) {
                    this.updateCropBoxSize();
                }
            });
        },

        updateCropBoxSize() {
            const screenW = window.innerWidth;
            if (screenW < 380) {
                this.cropWidth = Math.max(180, screenW - 80);
                this.cropHeight = Math.round(this.cropWidth * (4 / 3));
            } else if (screenW < 520) {
                this.cropWidth = Math.min(240, screenW - 80);
                this.cropHeight = Math.round(this.cropWidth * (4 / 3));
            } else {
                this.cropWidth = 270;
                this.cropHeight = 360;
            }
            if (this.imageLoaded) {
                this.clampPan();
            }
        },

        onFileSelect(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Format file tidak didukung. Harap pilih gambar JPEG, PNG, JPG, atau WebP.');
                e.target.value = '';
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 10MB.');
                e.target.value = '';
                return;
            }

            this.cropperError = null;
            const reader = new FileReader();
            reader.onload = (event) => {
                this.imageSrc = event.target.result;
                const img = new Image();
                img.onload = () => {
                    this.naturalWidth = img.naturalWidth;
                    this.naturalHeight = img.naturalHeight;
                    this.imageLoaded = true;
                    this.zoom = 1.0;
                    this.panX = 0;
                    this.panY = 0;
                    this.showCropModal = true;
                    this.updateCropBoxSize();
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
            e.target.value = '';
        },

        getBaseScale() {
            if (!this.naturalWidth || !this.naturalHeight) return 1;
            return Math.max(this.cropWidth / this.naturalWidth, this.cropHeight / this.naturalHeight);
        },

        getScale() {
            return this.getBaseScale() * this.zoom;
        },

        getImageDimensions() {
            const scale = this.getScale();
            return {
                width: Math.round(this.naturalWidth * scale),
                height: Math.round(this.naturalHeight * scale)
            };
        },

        getMaxPan() {
            const dims = this.getImageDimensions();
            return {
                maxX: Math.max(0, (dims.width - this.cropWidth) / 2),
                maxY: Math.max(0, (dims.height - this.cropHeight) / 2)
            };
        },

        clampPan() {
            const max = this.getMaxPan();
            this.panX = Math.max(-max.maxX, Math.min(max.maxX, this.panX));
            this.panY = Math.max(-max.maxY, Math.min(max.maxY, this.panY));
        },

        setZoom(val) {
            this.zoom = Math.max(this.minZoom, Math.min(this.maxZoom, parseFloat(val)));
            this.clampPan();
        },

        zoomIn() {
            this.setZoom(this.zoom + 0.15);
        },

        zoomOut() {
            this.setZoom(this.zoom - 0.15);
        },

        resetCrop() {
            this.zoom = 1.0;
            this.panX = 0;
            this.panY = 0;
        },

        onPointerDown(e) {
            this.isDragging = true;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            this.startX = clientX;
            this.startY = clientY;
            this.startPanX = this.panX;
            this.startPanY = this.panY;
        },

        onPointerMove(e) {
            if (!this.isDragging) return;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            const dx = clientX - this.startX;
            const dy = clientY - this.startY;
            this.panX = this.startPanX + dx;
            this.panY = this.startPanY + dy;
            this.clampPan();
        },

        onPointerUp() {
            this.isDragging = false;
        },

        onWheel(e) {
            e.preventDefault();
            const delta = e.deltaY < 0 ? 0.1 : -0.1;
            this.setZoom(this.zoom + delta);
        },

        cancelCrop() {
            this.showCropModal = false;
            this.imageSrc = null;
            this.imageLoaded = false;
            this.cropperError = null;
        },

        applyCrop() {
            if (!this.imageLoaded || this.isUploading) return;
            this.isUploading = true;
            this.cropperError = null;

            try {
                const scale = this.getScale();
                const dims = this.getImageDimensions();

                const imgLeft = (this.cropWidth - dims.width) / 2 + this.panX;
                const imgTop = (this.cropHeight - dims.height) / 2 + this.panY;

                const srcX = Math.max(0, -imgLeft / scale);
                const srcY = Math.max(0, -imgTop / scale);
                const srcW = Math.min(this.naturalWidth - srcX, this.cropWidth / scale);
                const srcH = Math.min(this.naturalHeight - srcY, this.cropHeight / scale);

                const targetW = 600;
                const targetH = 800;

                const canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                const ctx = canvas.getContext('2d');

                const img = new Image();
                img.onload = () => {
                    ctx.drawImage(img, srcX, srcY, srcW, srcH, 0, 0, targetW, targetH);
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            this.isUploading = false;
                            this.cropperError = 'Gagal memproses gambar hasil crop.';
                            return;
                        }
                        const croppedFile = new File([blob], 'foto-paslon.jpg', { type: 'image/jpeg' });
                        
                        @this.upload('photo', croppedFile, () => {
                            this.isUploading = false;
                            this.showCropModal = false;
                            this.imageSrc = null;
                            this.imageLoaded = false;
                        }, (err) => {
                            this.isUploading = false;
                            this.cropperError = 'Gagal mengunggah foto: ' + (err || 'Terjadi kesalahan.');
                        });
                    }, 'image/jpeg', 0.92);
                };
                img.src = this.imageSrc;
            } catch (err) {
                this.isUploading = false;
                this.cropperError = 'Gagal melakukan crop: ' + err.message;
            }
        }
    };
}
</script>
