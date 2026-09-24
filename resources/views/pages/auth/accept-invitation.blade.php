<div class="min-h-screen w-full bg-surface-muted border-b-2 border-ink flex flex-col justify-between relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div class="hidden lg:block absolute -top-12 -left-8 font-display font-black text-9xl text-ink/5 tracking-tighter leading-none">
            PEMIRA
        </div>
        <div class="hidden lg:block absolute -bottom-16 right-8 font-display font-black text-9xl text-ink/5 tracking-tighter leading-none">
            2026
        </div>

        <svg class="absolute top-10 right-10 w-44 h-44 text-ink/10 hidden sm:block pointer-events-none" fill="currentColor">
            <pattern id="accept-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#accept-dots)" />
        </svg>

        <div class="hidden lg:block absolute top-0 left-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 right-1/4 w-px h-full bg-ink/10"></div>

        <div class="hidden sm:flex absolute top-12 left-1/4 text-ink/25 font-display font-bold text-lg leading-none">+</div>
        <div class="hidden sm:flex absolute bottom-16 left-12 text-ink/25 font-display font-bold text-lg leading-none">+</div>
        <div class="hidden sm:flex absolute bottom-12 right-1/3 text-ink/25 font-display font-bold text-lg leading-none">+</div>
    </div>

    <header class="relative z-10 w-full bg-surface border-b-2 border-ink">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none focus:ring-2 focus:ring-brand">
                <img
                    src="{{ asset('img/logos/organization/kpr-logo-no-text.png') }}"
                    alt="Logo KPR"
                    class="h-9 w-auto sm:h-11 object-contain shrink-0"
                >
                <div class="flex items-baseline font-display">
                    <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-brand">
                        PEMIRA
                    </span>
                    <span class="text-xl sm:text-2xl font-extrabold text-accent ml-1">
                        '26
                    </span>
                </div>
            </a>

            <a
                href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-ink bg-surface-muted border-2 border-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:bg-surface active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
            >
                <span aria-hidden="true">&larr;</span>
                <span>KE HALAMAN LOGIN</span>
            </a>
        </div>
    </header>

    <main class="relative z-10 flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
        <div class="w-full max-w-lg mx-auto">
            <div class="relative">
                <div class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 w-full h-full bg-accent border-2 border-ink"></div>

                <div class="relative bg-surface border-2 border-ink shadow-brutal sm:shadow-brutal-lg">
                    <div class="bg-brand text-surface px-4 sm:px-6 py-3 sm:py-4 border-b-2 border-ink flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block border border-ink"></span>
                            <span class="font-display font-bold text-xs sm:text-sm uppercase tracking-wider text-surface">
                                UNDANGAN ADMINISTRATOR
                            </span>
                        </div>
                        <span class="bg-accent text-ink px-2 sm:px-2.5 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                            AKTIVASI
                        </span>
                    </div>

                    <div class="p-5 sm:p-7">
                        @if ($invitationStatus !== 'valid')
                            <div class="space-y-4">
                                <div class="bg-surface-muted border-2 border-ink p-4 shadow-brutal-sm text-ink">
                                    <div class="flex items-center gap-2 text-xs font-display font-bold uppercase text-brand mb-2">
                                        <span class="w-2.5 h-2.5 bg-red-600 inline-block border border-ink"></span>
                                        <span>STATUS UNDANGAN TIDAK VALID</span>
                                    </div>
                                    <h2 class="font-display font-black text-lg text-ink uppercase mb-2">
                                        @if ($invitationStatus === 'expired')
                                            Undangan Telah Kedaluwarsa
                                        @elseif ($invitationStatus === 'revoked')
                                            Undangan Telah Dibatalkan
                                        @elseif ($invitationStatus === 'already_accepted')
                                            Undangan Sudah Pernah Digunakan
                                        @else
                                            Tautan Undangan Tidak Ditemukan
                                        @endif
                                    </h2>
                                    <p class="text-xs sm:text-sm font-sans text-ink/80 leading-relaxed">
                                        @if ($invitationStatus === 'expired')
                                            Tautan undangan ini telah melewati batas masa aktif 24 jam. Silakan hubungi Super Admin untuk meminta pengiriman ulang undangan.
                                        @elseif ($invitationStatus === 'revoked')
                                            Undangan untuk mengakses akun administrator ini telah dibatalkan oleh Super Admin.
                                        @elseif ($invitationStatus === 'already_accepted')
                                            Tautan undangan ini sudah berhasil digunakan untuk mendaftarkan akun. Silakan masuk menggunakan email dan kata sandi Anda.
                                        @else
                                            Tautan undangan yang Anda buka tidak valid atau token tidak cocok dengan data sistem.
                                        @endif
                                    </p>
                                </div>

                                <div class="pt-2 text-center">
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal font-display font-black text-xs uppercase tracking-wider transition-all cursor-pointer"
                                    >
                                        MASUK KE PORTAL PEMIRA &rarr;
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="mb-5 sm:mb-6">
                                <h1 class="text-xl sm:text-2xl font-display font-black text-ink uppercase tracking-tight mb-2">
                                    BUAT AKUN ADMIN
                                </h1>
                                <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                                    Lengkapi data diri dan buat kata sandi untuk mengaktifkan akses operasional administrator PEMIRA Anda.
                                </p>
                            </div>

                            @if ($errors->any())
                                <div class="bg-surface-muted border-2 border-ink p-3.5 shadow-brutal-sm mb-5 text-ink">
                                    <div class="flex items-center gap-2 text-xs font-display font-bold uppercase text-brand mb-1">
                                        <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                                        <span>PERIKSA FORMULIR ANDA</span>
                                    </div>
                                    <ul class="text-xs font-sans font-medium space-y-1 text-ink/90">
                                        @foreach ($errors->all() as $error)
                                            <li>&bull; {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form wire:submit="createAccount" class="space-y-4 sm:space-y-5">
                                <div>
                                    <label class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                        Alamat Email (Terkunci)
                                    </label>
                                    <input
                                        type="email"
                                        value="{{ $email }}"
                                        disabled
                                        readonly
                                        class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-bold text-ink/80 bg-surface-muted border-2 border-ink shadow-brutal-sm cursor-not-allowed select-none"
                                    >
                                    <p class="mt-1 text-[11px] font-sans text-ink/60">
                                        Alamat email berasal dari undangan resmi dan tidak dapat diubah.
                                    </p>
                                </div>

                                <div>
                                    <label for="name" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                        Nama Lengkap <span class="text-brand">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        type="text"
                                        wire:model="name"
                                        placeholder="contoh: I Made Surya Pratama"
                                        required
                                        autocomplete="name"
                                        class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                    >
                                    @error('name')
                                        <p class="mt-1.5 text-xs font-sans font-bold text-brand">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                        Kata Sandi Baru <span class="text-brand">*</span>
                                    </label>
                                    <input
                                        id="password"
                                        type="password"
                                        wire:model="password"
                                        placeholder="Minimal 8 karakter"
                                        required
                                        autocomplete="new-password"
                                        class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                    >
                                    @error('password')
                                        <p class="mt-1.5 text-xs font-sans font-bold text-brand">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                        Konfirmasi Kata Sandi Baru <span class="text-brand">*</span>
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        wire:model="password_confirmation"
                                        placeholder="Ulangi kata sandi baru"
                                        required
                                        autocomplete="new-password"
                                        class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                    >
                                </div>

                                <div class="p-3 bg-surface-muted border-2 border-ink text-xs text-ink/80 flex items-center gap-2">
                                    <span class="w-2 h-2 bg-brand shrink-0 inline-block"></span>
                                    <span>Peran Anda akan otomatis menjadi <strong>Admin KPR (Operasional)</strong>.</span>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full mt-2 inline-flex items-center justify-center gap-2 px-5 py-3 sm:py-3.5 text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-brand"
                                >
                                    <span>AKTIFKAN AKUN ADMIN</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-10 w-full bg-surface border-t-2 border-ink py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs sm:text-sm font-sans font-medium text-ink/70">
                &copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR) Politeknik Negeri Bali.
            </p>
        </div>
    </footer>
</div>
