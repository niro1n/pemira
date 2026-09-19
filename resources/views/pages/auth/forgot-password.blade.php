<div class="min-h-screen w-full bg-surface-muted border-b-2 border-ink flex flex-col justify-between relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div class="hidden lg:block absolute -top-12 -left-8 font-display font-black text-9xl text-ink/5 tracking-tighter leading-none">
            PEMIRA
        </div>
        <div class="hidden lg:block absolute -bottom-16 right-8 font-display font-black text-9xl text-ink/5 tracking-tighter leading-none">
            2026
        </div>

        <svg class="absolute top-10 right-10 w-44 h-44 text-ink/10 hidden sm:block pointer-events-none" fill="currentColor">
            <pattern id="forgot-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#forgot-dots)" />
        </svg>

        <div class="hidden lg:block absolute top-0 left-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 right-1/4 w-px h-full bg-ink/10"></div>

        <div class="hidden sm:flex absolute top-12 left-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden sm:flex absolute bottom-16 left-12 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden sm:flex absolute bottom-12 right-1/3 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden lg:block absolute top-1/4 right-8 w-3 h-3 bg-accent border border-ink"></div>
        <div class="hidden md:block absolute bottom-1/4 left-10 w-2.5 h-2.5 bg-brand"></div>
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
                <span>KEMBALI KE LOGIN</span>
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
                                LUPA KATA SANDI
                            </span>
                        </div>
                        <span class="bg-accent text-ink px-2 sm:px-2.5 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                            BANTUAN
                        </span>
                    </div>

                    <div class="p-5 sm:p-7">
                        <div class="mb-5 sm:mb-6">
                            <h1 class="text-xl sm:text-2xl font-display font-black text-ink uppercase tracking-tight mb-2">
                                ATUR ULANG KATA SANDI
                            </h1>
                            <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                                Masukkan alamat email yang terdaftar pada akun PEMIRA Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                            </p>
                        </div>

                        @if ($statusMessage)
                            <div class="bg-surface-muted border-2 border-ink p-3.5 shadow-brutal-sm mb-5 text-ink">
                                <div class="flex items-center gap-2 text-xs font-display font-bold uppercase text-brand mb-1">
                                    <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                                    <span>STATUS PERMINTAAN</span>
                                </div>
                                <p class="text-xs font-sans font-medium text-ink/90 leading-relaxed">
                                    {{ $statusMessage }}
                                </p>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="bg-surface-muted border-2 border-ink p-3.5 shadow-brutal-sm mb-5 text-ink">
                                <div class="flex items-center gap-2 text-xs font-display font-bold uppercase text-brand mb-1">
                                    <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                                    <span>PERIKSA KEMBALI DATA ANDA</span>
                                </div>
                                <ul class="text-xs font-sans font-medium space-y-1 text-ink/90">
                                    @foreach ($errors->all() as $error)
                                        <li>&bull; {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form wire:submit="sendResetLink" class="space-y-4 sm:space-y-5">
                            <div>
                                <label for="email" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                    ALAMAT EMAIL TERDAFTAR
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="nama@student.pnb.ac.id"
                                    class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40 {{ $errors->has('email') ? 'border-brand ring-1 ring-brand' : '' }}"
                                >
                                @error('email')
                                    <p class="mt-1.5 text-xs font-sans font-bold text-brand">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove>KIRIM LINK RESET</span>
                                <span wire:loading>MENGIRIMKAN...</span>
                                <span aria-hidden="true" wire:loading.remove>&rarr;</span>
                            </button>
                        </form>

                        <div class="mt-6 pt-5 border-t-2 border-ink/15 text-center">
                            <p class="text-xs sm:text-sm font-sans font-medium text-ink">
                                Sudah ingat kata sandi Anda?
                                <a
                                    href="{{ route('login') }}"
                                    class="font-bold text-brand hover:text-ink underline transition-colors focus:outline-none focus:ring-1 focus:ring-brand"
                                >
                                    Masuk ke akun &rarr;
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-10 w-full bg-brand text-surface border-t-2 border-ink py-3.5 sm:py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-sans font-medium text-surface/70 text-center sm:text-left">
            <span>PEMIRA 2026 — Pemilihan Raya Mahasiswa PNB</span>
            <span>&copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR). All rights reserved.</span>
        </div>
    </footer>
</div>
