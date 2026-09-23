<div class="space-y-6" x-data="sponsorLogoCropper()">
    <input type="file"
           x-ref="logoFileInput"
           @change="onFileSelect($event)"
           accept="image/jpeg,image/png,image/jpg,image/webp"
           class="hidden" />

    <div class="bg-surface border-2 border-ink p-4 sm:p-6 shadow-brutal flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-surface-muted border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-brand mb-2">
                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                <span>LANDING PAGE</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-2xl text-brand uppercase tracking-tight leading-tight">
                MANAJEMEN SPONSOR
            </h2>
            <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1">
                Kelola daftar mitra dan logo sponsor yang tampil pada Landing Page PEMIRA.
            </p>
        </div>

        <button wire:click="openCreateModal"
                type="button"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>TAMBAH SPONSOR</span>
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-3.5 bg-accent/20 border-2 border-ink shadow-brutal-sm flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 text-xs font-sans font-bold text-ink">
                <span class="w-2 h-2 bg-accent border border-ink inline-block shrink-0"></span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-ink/60 hover:text-ink text-xs font-bold">✕</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3.5 bg-red-100 border-2 border-ink shadow-brutal-sm flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 text-xs font-sans font-bold text-red-800">
                <span class="w-2 h-2 bg-red-600 border border-ink inline-block shrink-0"></span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-ink/60 hover:text-ink text-xs font-bold">✕</button>
        </div>
    @endif

    <div class="bg-surface border-2 border-ink p-3 sm:p-4 shadow-brutal flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative flex-1">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama sponsor..."
                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans font-medium text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface min-h-10" />
        </div>

        @if ($search !== '')
            <button wire:click="$set('search', '')"
                    type="button"
                    class="px-3 py-2 bg-surface border-2 border-ink text-xs font-sans font-bold text-ink/70 hover:text-ink shadow-brutal-sm shrink-0 min-h-10 cursor-pointer">
                Reset Pencarian
            </button>
        @endif
    </div>

    @if ($sponsors->isEmpty())
        <div class="bg-surface border-2 border-ink p-8 sm:p-12 text-center shadow-brutal space-y-4">
            <div class="w-16 h-16 bg-surface-muted border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm">
                <svg class="w-8 h-8 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-black text-base sm:text-xl text-brand uppercase">
                    BELUM ADA SPONSOR
                </h3>
                <p class="text-xs sm:text-sm font-sans text-ink/70 mt-1 max-w-md mx-auto">
                    @if ($search !== '')
                        Tidak ada data sponsor yang sesuai dengan kata kunci "{{ $search }}".
                    @else
                        Belum ada sponsor yang terdaftar. Tambahkan sponsor pertama untuk ditampilkan pada Landing Page.
                    @endif
                </p>
            </div>
            @if ($search === '')
                <div>
                    <button wire:click="openCreateModal"
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>TAMBAH SPONSOR</span>
                    </button>
                </div>
            @endif
        </div>
    @else
        <div class="bg-surface border-2 border-ink shadow-brutal overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse font-sans text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-brand text-surface border-b-2 border-ink font-display font-black text-xs uppercase tracking-wider">
                            <th class="py-3 px-3 sm:px-4 w-16 text-center">Urutan</th>
                            <th class="py-3 px-3 sm:px-4 w-24 text-center">Logo</th>
                            <th class="py-3 px-3 sm:px-4">Nama Sponsor</th>
                            <th class="py-3 px-3 sm:px-4">Website</th>
                            <th class="py-3 px-3 sm:px-4 w-28 text-center">Status</th>
                            <th class="py-3 px-3 sm:px-4 w-32 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-ink/15">
                        @foreach ($sponsors as $sponsor)
                            <tr class="hover:bg-surface-muted transition-colors">
                                <td class="py-3 px-3 sm:px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 bg-surface-muted border-2 border-ink shadow-brutal-sm font-display font-bold text-xs">
                                        {{ $sponsor->sort_order }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 text-center">
                                    <div class="w-14 h-14 aspect-square bg-surface border-2 border-ink shadow-brutal-sm mx-auto overflow-hidden p-1 flex items-center justify-center">
                                        @if ($sponsor->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($sponsor->logo))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($sponsor->logo) }}"
                                                 alt="{{ $sponsor->name }}"
                                                 class="w-full h-full object-contain" />
                                        @else
                                            <span class="text-[10px] font-bold text-ink/40">NO LOGO</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 sm:px-4">
                                    <div class="font-display font-bold text-ink uppercase text-xs sm:text-sm">
                                        {{ $sponsor->name }}
                                    </div>
                                    <div class="text-[11px] text-ink/60 font-mono mt-0.5">
                                        ID #{{ $sponsor->id }}
                                    </div>
                                </td>
                                <td class="py-3 px-3 sm:px-4">
                                    @if ($sponsor->website_url)
                                        <a href="{{ $sponsor->website_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 text-brand hover:underline font-medium text-xs truncate max-w-xs">
                                            <span class="truncate">{{ $sponsor->website_url }}</span>
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-ink/40 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 text-center">
                                    <button wire:click="toggleStatus({{ $sponsor->id }})"
                                            type="button"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase tracking-wider cursor-pointer transition-transform active:translate-x-0.5 active:translate-y-0.5 {{ $sponsor->is_active ? 'bg-accent text-ink hover:bg-accent/80' : 'bg-surface-muted text-ink/60 hover:bg-surface' }}">
                                        <span class="w-2 h-2 {{ $sponsor->is_active ? 'bg-brand' : 'bg-ink/40' }} inline-block"></span>
                                        <span>{{ $sponsor->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </td>
                                <td class="py-3 px-3 sm:px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <x-action-button variant="detail" label="Lihat detail sponsor" wire:click="openDetailModal({{ $sponsor->id }})" />
                                        <x-action-button variant="edit" label="Edit sponsor" wire:click="openEditModal({{ $sponsor->id }})" />
                                        <x-action-button variant="delete" label="Hapus sponsor" wire:click="openDeleteModal({{ $sponsor->id }})" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($sponsors->hasPages())
                <div class="p-3 sm:p-4 border-t-2 border-ink bg-surface">
                    {{ $sponsors->links() }}
                </div>
            @endif
        </div>
    @endif

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-ink/70 flex items-center justify-center p-3 sm:p-4 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-lg overflow-hidden animate-in fade-in duration-100">
                <div class="bg-brand text-surface px-4 py-3 border-b-2 border-ink flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-wider">
                            TAMBAH SPONSOR BARU
                        </h3>
                    </div>
                    <button wire:click="closeCreateModal" type="button" class="text-surface hover:text-accent font-bold text-base cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="createSponsor" class="p-4 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Nama Sponsor <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                               wire:model="name"
                               placeholder="Contoh: PT Bank Central Asia Tbk"
                               class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        @error('name')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Logo Sponsor (1:1 SQUARE) <span class="text-red-600">*</span>
                        </label>

                        @php
                            $createPreviewUrl = null;
                            if ($logo && method_exists($logo, 'temporaryUrl')) {
                                try {
                                    $createPreviewUrl = $logo->temporaryUrl();
                                } catch (\Throwable $e) {
                                    $createPreviewUrl = null;
                                }
                            }
                        @endphp

                        @if ($logo && $createPreviewUrl)
                            <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm space-y-3">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 aspect-square bg-surface-muted border-2 border-ink overflow-hidden shrink-0 shadow-brutal-sm p-1 flex items-center justify-center">
                                        <img src="{{ $createPreviewUrl }}" alt="Preview Logo" class="w-full h-full object-contain" />
                                    </div>
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase">
                                            <span>✓ HASIL CROP SIAP DISIMPAN</span>
                                        </div>
                                        <p class="text-xs font-sans text-ink/70">
                                            Logo telah dipotong berbentuk square (1:1) dan siap disimpan.
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-ink/15 flex flex-wrap items-center gap-2">
                                    <button type="button"
                                            @click="$refs.logoFileInput.click()"
                                            class="px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors cursor-pointer">
                                        GANTI LOGO & CROP ULANG
                                    </button>
                                    <button type="button"
                                            wire:click="cancelNewLogo"
                                            class="px-3 py-1.5 bg-surface text-ink hover:bg-red-600 hover:text-white border-2 border-ink text-xs font-display font-bold uppercase transition-colors cursor-pointer">
                                        HAPUS LOGO
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
                                        UNGGAH & CROP LOGO SPONSOR
                                    </span>
                                    <p class="text-xs font-sans text-ink/60 mt-0.5 max-w-xs mx-auto">
                                        Pilih logo dari komputer, atur posisi & ukuran dengan editor crop 1:1, lalu simpan.
                                    </p>
                                </div>
                                <button type="button"
                                        @click="$refs.logoFileInput.click()"
                                        class="mt-1 px-4 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                                    PILIH & CROP LOGO
                                </button>
                            </div>
                        @endif

                        <div wire:loading wire:target="logo" class="text-xs font-display font-bold text-brand mt-1">
                            Mengunggah logo hasil crop...
                        </div>

                        @error('logo')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Website URL <span class="text-ink/50 font-normal">(Opsional)</span>
                        </label>
                        <input type="url"
                               wire:model="website_url"
                               placeholder="https://example.com"
                               class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        @error('website_url')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Urutan Tampil <span class="text-red-600">*</span>
                            </label>
                            <input type="number"
                                   wire:model="sort_order"
                                   min="0"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            <p class="text-[11px] text-ink/60 mt-0.5">Nilai lebih kecil tampil lebih awal.</p>
                            @error('sort_order')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Status Penayangan
                            </label>
                            <label class="flex items-center gap-2.5 p-2 bg-surface-muted border-2 border-ink shadow-brutal-sm cursor-pointer mt-0.5">
                                <input type="checkbox"
                                       wire:model="is_active"
                                       class="w-4 h-4 text-brand rounded-none border-2 border-ink focus:ring-0" />
                                <span class="text-xs font-display font-bold uppercase text-ink">
                                    Tampilkan di Landing Page
                                </span>
                            </label>
                            @error('is_active')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t-2 border-ink flex items-center justify-end gap-2.5">
                        <button type="button"
                                wire:click="closeCreateModal"
                                class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase hover:bg-surface-muted transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="createSponsor">SIMPAN SPONSOR</span>
                            <span wire:loading wire:target="createSponsor">MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-ink/70 flex items-center justify-center p-3 sm:p-4 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-lg overflow-hidden animate-in fade-in duration-100">
                <div class="bg-brand text-surface px-4 py-3 border-b-2 border-ink flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-wider">
                            EDIT SPONSOR
                        </h3>
                    </div>
                    <button wire:click="closeEditModal" type="button" class="text-surface hover:text-accent font-bold text-base cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="updateSponsor" class="p-4 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Nama Sponsor <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                               wire:model="name"
                               placeholder="Contoh: PT Bank Central Asia Tbk"
                               class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        @error('name')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Logo Sponsor (1:1 SQUARE)
                        </label>

                        @php
                            $editPreviewUrl = null;
                            if ($logo && method_exists($logo, 'temporaryUrl')) {
                                try {
                                    $editPreviewUrl = $logo->temporaryUrl();
                                } catch (\Throwable $e) {
                                    $editPreviewUrl = null;
                                }
                            }
                        @endphp

                        @if ($logo && $editPreviewUrl)
                            <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm space-y-3">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 aspect-square bg-surface-muted border-2 border-ink overflow-hidden shrink-0 shadow-brutal-sm p-1 flex items-center justify-center">
                                        <img src="{{ $editPreviewUrl }}" alt="Preview Logo Baru" class="w-full h-full object-contain" />
                                    </div>
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-accent text-ink border border-ink text-xs font-display font-black uppercase">
                                            <span>✓ LOGO BARU SIAP DISIMPAN</span>
                                        </div>
                                        <p class="text-xs font-sans text-ink/70">
                                            Logo lama akan otomatis digantikan setelah disimpan.
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-ink/15 flex flex-wrap items-center gap-2">
                                    <button type="button"
                                            @click="$refs.logoFileInput.click()"
                                            class="px-3 py-1.5 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm text-xs font-display font-black uppercase tracking-wider transition-colors cursor-pointer">
                                        GANTI LOGO & CROP ULANG
                                    </button>
                                    <button type="button"
                                            wire:click="cancelNewLogo"
                                            class="px-3 py-1.5 bg-surface text-red-700 hover:bg-red-600 hover:text-white border-2 border-ink text-xs font-display font-bold uppercase transition-colors cursor-pointer">
                                        Batal Ganti Logo
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 aspect-square bg-surface-muted border-2 border-ink overflow-hidden shrink-0 shadow-brutal-sm p-1 flex items-center justify-center">
                                        @if ($existingLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingLogo))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingLogo) }}"
                                                 alt="Logo Saat Ini"
                                                 class="w-full h-full object-contain" />
                                        @else
                                            <span class="text-[10px] font-bold text-ink/40">NO LOGO</span>
                                        @endif
                                    </div>
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <span class="font-display font-bold text-xs uppercase text-ink block">
                                            Logo Saat Ini
                                        </span>
                                        <p class="text-[11px] font-sans text-ink/60">
                                            Format logo harus berbentuk square (1:1).
                                        </p>
                                        <p class="text-[11px] font-sans text-ink/50">
                                            Pilih berkas baru jika ingin mengganti logo.
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-ink/15">
                                    <button type="button"
                                            @click="$refs.logoFileInput.click()"
                                            class="px-3 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                                        PILIH & CROP LOGO BARU
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div wire:loading wire:target="logo" class="text-xs font-display font-bold text-brand mt-1">
                            Mengunggah logo hasil crop...
                        </div>

                        @error('logo')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                            Website URL <span class="text-ink/50 font-normal">(Opsional)</span>
                        </label>
                        <input type="url"
                               wire:model="website_url"
                               placeholder="https://example.com"
                               class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink placeholder:text-ink/40 shadow-brutal-sm focus:outline-none focus:bg-surface" />
                        @error('website_url')
                            <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Urutan Tampil <span class="text-red-600">*</span>
                            </label>
                            <input type="number"
                                   wire:model="sort_order"
                                   min="0"
                                   class="w-full bg-surface-muted border-2 border-ink px-3 py-2 text-xs sm:text-sm font-sans text-ink shadow-brutal-sm focus:outline-none focus:bg-surface" />
                            <p class="text-[11px] text-ink/60 mt-0.5">Nilai lebih kecil tampil lebih awal.</p>
                            @error('sort_order')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-display font-bold uppercase tracking-wider text-ink mb-1">
                                Status Penayangan
                            </label>
                            <label class="flex items-center gap-2.5 p-2 bg-surface-muted border-2 border-ink shadow-brutal-sm cursor-pointer mt-0.5">
                                <input type="checkbox"
                                       wire:model="is_active"
                                       class="w-4 h-4 text-brand rounded-none border-2 border-ink focus:ring-0" />
                                <span class="text-xs font-display font-bold uppercase text-ink">
                                    Tampilkan di Landing Page
                                </span>
                            </label>
                            @error('is_active')
                                <p class="text-xs font-sans font-bold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t-2 border-ink flex items-center justify-end gap-2.5">
                        <button type="button"
                                wire:click="closeEditModal"
                                class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase hover:bg-surface-muted transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="updateSponsor">PERBARUI SPONSOR</span>
                            <span wire:loading wire:target="updateSponsor">MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-ink/70 flex items-center justify-center p-3 sm:p-4 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-md overflow-hidden animate-in fade-in duration-100">
                <div class="bg-red-600 text-white px-4 py-3 border-b-2 border-ink flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-wider">
                            KONFIRMASI HAPUS SPONSOR
                        </h3>
                    </div>
                    <button wire:click="closeDeleteModal" type="button" class="text-white hover:text-accent font-bold text-base cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="p-4 sm:p-6 space-y-4">
                    <div class="w-14 h-14 bg-red-100 border-2 border-ink mx-auto flex items-center justify-center shadow-brutal-sm text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>

                    <div class="text-center space-y-2">
                        <p class="text-xs sm:text-sm font-sans text-ink">
                            Apakah Anda yakin ingin menghapus sponsor berikut?
                        </p>
                        <div class="p-3 bg-surface-muted border-2 border-ink shadow-brutal-sm inline-block">
                            <span class="font-display font-black text-sm uppercase text-brand">
                                {{ $selectedSponsor?->name }}
                            </span>
                        </div>
                        <p class="text-[11px] font-sans text-red-700 font-bold">
                            Tindakan ini tidak dapat dibatalkan. Berkas logo sponsor juga akan dihapus dari penyimpanan.
                        </p>
                    </div>

                    <div class="pt-4 border-t-2 border-ink flex items-center justify-end gap-2.5">
                        <button type="button"
                                wire:click="closeDeleteModal"
                                class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase hover:bg-surface-muted transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="deleteSponsor"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 bg-red-600 text-white hover:bg-red-700 border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="deleteSponsor">YA, HAPUS</span>
                            <span wire:loading wire:target="deleteSponsor">MENGHAPUS...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showDetailModal && $selectedSponsor)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-ink/70 flex items-center justify-center p-3 sm:p-4 backdrop-blur-xs">
            <div class="bg-surface border-2 border-ink shadow-brutal w-full max-w-lg overflow-hidden animate-in fade-in duration-100">
                <div class="bg-brand text-surface px-4 py-3 border-b-2 border-ink flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                        <h3 class="font-display font-black text-sm sm:text-base uppercase tracking-wider">
                            DETAIL SPONSOR
                        </h3>
                    </div>
                    <button wire:click="closeDetailModal" type="button" class="text-surface hover:text-accent font-bold text-base cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="p-4 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 bg-surface-muted border-2 border-ink shadow-brutal-sm">
                        <div class="w-28 h-28 aspect-square bg-surface border-2 border-ink shadow-brutal-sm p-2 flex items-center justify-center overflow-hidden shrink-0">
                            @if ($selectedSponsor->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($selectedSponsor->logo))
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($selectedSponsor->logo) }}"
                                     alt="{{ $selectedSponsor->name }}"
                                     class="w-full h-full object-contain" />
                            @else
                                <span class="text-xs font-bold text-ink/40">NO LOGO</span>
                            @endif
                        </div>

                        <div class="space-y-2 text-center sm:text-left min-w-0 flex-1">
                            <div>
                                <span class="text-[11px] font-sans font-bold uppercase tracking-wider text-ink/60 block">Nama Sponsor</span>
                                <h4 class="font-display font-black text-base sm:text-lg text-brand uppercase leading-tight">
                                    {{ $selectedSponsor->name }}
                                </h4>
                            </div>

                            <div>
                                <span class="text-[11px] font-sans font-bold uppercase tracking-wider text-ink/60 block">Website</span>
                                @if ($selectedSponsor->website_url)
                                    <a href="{{ $selectedSponsor->website_url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 text-xs font-medium text-brand hover:underline">
                                        <span>{{ $selectedSponsor->website_url }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-xs font-sans text-ink/50">Tidak ada tautan website</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm">
                            <span class="text-[11px] font-sans font-bold uppercase tracking-wider text-ink/60 block">Status Penayangan</span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 border border-ink text-xs font-display font-bold uppercase {{ $selectedSponsor->is_active ? 'bg-accent text-ink' : 'bg-surface-muted text-ink/60' }}">
                                <span class="w-2 h-2 {{ $selectedSponsor->is_active ? 'bg-brand' : 'bg-ink/40' }} inline-block"></span>
                                <span>{{ $selectedSponsor->is_active ? 'Aktif di Landing Page' : 'Nonaktif' }}</span>
                            </span>
                        </div>

                        <div class="p-3 bg-surface border-2 border-ink shadow-brutal-sm">
                            <span class="text-[11px] font-sans font-bold uppercase tracking-wider text-ink/60 block">Urutan Tampil</span>
                            <span class="font-display font-black text-sm text-brand block mt-1">
                                Nomor Urut: #{{ $selectedSponsor->sort_order }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t-2 border-ink flex items-center justify-end gap-2.5">
                        <button type="button"
                                wire:click="closeDetailModal"
                                class="px-4 py-2 bg-surface border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase hover:bg-surface-muted transition-colors cursor-pointer">
                            Tutup
                        </button>
                        <button type="button"
                                wire:click="openEditModal({{ $selectedSponsor->id }}); closeDetailModal();"
                                class="px-5 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer">
                            Edit Sponsor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div x-show="showCropModal"
         x-cloak
         class="fixed inset-0 z-60 overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-ink/80 backdrop-blur-xs"
         role="dialog"
         aria-modal="true"
         aria-labelledby="cropper-modal-title">

        <div class="w-full max-w-lg bg-surface border-2 border-ink shadow-brutal flex flex-col min-w-0"
             @click.outside="if (!isUploading) cancelCrop()">

            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b-2 border-ink bg-brand text-surface flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink"></span>
                    <h3 id="cropper-modal-title" class="font-display font-extrabold text-sm sm:text-base uppercase tracking-wider text-surface leading-none">
                        CROP & ATUR POSISI LOGO (1:1)
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
                    <span>Geser logo untuk mengatur posisi di dalam bingkai square (1:1), gunakan kontrol zoom di bawah untuk menyesuaikan ukuran.</span>
                </div>

                <div class="bg-ink/90 border-2 border-ink p-3 flex flex-col items-center justify-center shadow-brutal-sm overflow-hidden">
                    <div class="relative overflow-hidden bg-white/5 border-2 border-accent shadow-brutal select-none mx-auto flex items-center justify-center cursor-grab active:cursor-grabbing touch-none aspect-square"
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
                                 alt="Area Crop Logo"
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
                        Rasio Square (1:1)
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
                    <span x-show="!isUploading">GUNAKAN LOGO</span>
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
function sponsorLogoCropper() {
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
        cropWidth: 280,
        cropHeight: 280,
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
                this.cropHeight = this.cropWidth;
            } else if (screenW < 520) {
                this.cropWidth = Math.min(260, screenW - 80);
                this.cropHeight = this.cropWidth;
            } else {
                this.cropWidth = 280;
                this.cropHeight = 280;
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

                const targetW = 800;
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
                        const croppedFile = new File([blob], 'logo-sponsor.png', { type: 'image/png' });
                        
                        @this.upload('logo', croppedFile, () => {
                            this.isUploading = false;
                            this.showCropModal = false;
                            this.imageSrc = null;
                            this.imageLoaded = false;
                        }, (err) => {
                            this.isUploading = false;
                            this.cropperError = 'Gagal mengunggah logo: ' + (err || 'Terjadi kesalahan.');
                        });
                    }, 'image/png', 0.95);
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
