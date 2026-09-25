<div class="min-h-screen w-full bg-surface-muted border-b-2 border-ink flex flex-col justify-between relative overflow-hidden"
    x-data="{
        showPassword: false,
        showConfirmPassword: false,
        countdown: 60,
        timer: null,
        resendAvailable: false,
        init() {
            $wire.on('otp-sent', () => {
                this.startCountdown();
                this.$nextTick(() => {
                    if (this.$refs.otp0) this.$refs.otp0.focus();
                });
            });
        },
        startCountdown() {
            this.countdown = 60;
            this.resendAvailable = false;
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    this.resendAvailable = true;
                    clearInterval(this.timer);
                    this.timer = null;
                }
            }, 1000);
        },
        handleOtpInput(e, index) {
            const val = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = val ? val.slice(-1) : '';
            if (val && index < 5) {
                const nextRef = this.$refs['otp' + (index + 1)];
                if (nextRef) nextRef.focus();
            }
        },
        handleOtpKeydown(e, index) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                const prevRef = this.$refs['otp' + (index - 1)];
                if (prevRef) prevRef.focus();
            }
        },
        handleOtpPaste(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            for (let i = 0; i < 6; i++) {
                const char = pasteData[i] || '';
                const inputRef = this.$refs['otp' + i];
                if (inputRef) {
                    inputRef.value = char;
                    inputRef.dispatchEvent(new Event('input'));
                }
            }
            const focusIdx = Math.min(pasteData.length, 5);
            const targetRef = this.$refs['otp' + focusIdx];
            if (targetRef) targetRef.focus();
        }
    }">

    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div class="hidden lg:block absolute -top-12 -left-8 font-display font-black text-9xl text-ink/5 tracking-tighter leading-none">
            DAFTAR
        </div>
        <div class="hidden lg:block absolute -bottom-16 right-8 font-display font-black text-9xl text-ink/5 tracking-tighter leading-none">
            2026
        </div>

        <svg class="absolute top-10 right-10 w-44 h-44 text-ink/10 hidden sm:block pointer-events-none" fill="currentColor">
            <pattern id="register-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#register-dots)" />
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
                href="{{ route('home') }}"
                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-ink bg-surface-muted border-2 border-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:bg-surface active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
            >
                <span aria-hidden="true">&larr;</span>
                <span>KEMBALI</span>
            </a>
        </div>
    </header>

    <main class="relative z-10 flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
        <div class="w-full max-w-5xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <div class="lg:col-span-5 flex flex-col items-start lg:sticky lg:top-24">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-surface border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold tracking-wider uppercase text-brand mb-4 sm:mb-6">
                        <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                        <span>PENDAFTARAN PEMILIH</span>
                    </div>

                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight uppercase leading-none mb-3 sm:mb-4 text-ink space-y-1">
                        <span class="block text-brand">BUAT AKUN.</span>
                        <span class="block">GUNAKAN SUARA.</span>
                    </h1>

                    <p class="text-xs sm:text-sm lg:text-base text-ink font-sans font-medium leading-relaxed max-w-lg mb-6 sm:mb-8">
                        Daftarkan akun pemilih aktif Politeknik Negeri Bali untuk mendapatkan hak akses e-voting pada pemilihan ketua & wakil ketua BEM.
                    </p>

                    <div class="w-full bg-surface border-2 border-ink p-4 sm:p-5 shadow-brutal mb-6 lg:mb-0 space-y-3">
                        <div class="flex items-center justify-between gap-2 pb-2 border-b border-ink/15">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                                <span class="text-xs font-display font-bold uppercase tracking-wider text-brand">
                                    KETENTUAN PEMILIH
                                </span>
                            </div>
                            <span class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60">
                                DPT RESMI
                            </span>
                        </div>

                        <div class="text-xs font-sans font-medium text-ink/85 space-y-2">
                            <p>
                                Data pemilih dicocokkan otomatis dari daftar mahasiswa aktif resmi Politeknik Negeri Bali (DPT).
                            </p>
                            <p>
                                Kamu tidak perlu menginput nama atau program studi secara manual. Sistem akan memverifikasi berdasarkan NIM dan tanggal lahir.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-ink/10 flex items-center justify-between text-xs font-display font-bold text-brand uppercase">
                            <span>TAHAP: {{ $currentStep }} / 04</span>
                            <span class="text-accent">&#9632; LUBER JURDIL</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 w-full max-w-xl mx-auto lg:max-w-none">
                    <div class="relative">
                        <div class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 w-full h-full bg-accent border-2 border-ink"></div>

                        <div class="relative bg-surface border-2 border-ink shadow-brutal sm:shadow-brutal-lg">
                            <div class="bg-brand text-surface px-4 sm:px-6 py-3 sm:py-4 border-b-2 border-ink flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-accent inline-block border border-ink"></span>
                                    <span class="font-display font-bold text-xs sm:text-sm uppercase tracking-wider text-surface">
                                        REGISTRASI PEMILIH
                                    </span>
                                </div>
                                <span class="bg-accent text-ink px-2 sm:px-2.5 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                    E-VOTING 2026
                                </span>
                            </div>

                            <div class="bg-surface-muted border-b-2 border-ink px-4 sm:px-6 py-3">
                                <div class="grid grid-cols-4 gap-1.5 sm:gap-2">
                                    <div class="flex flex-col items-center text-center p-1.5 sm:p-2 border-2 transition-all {{ $currentStep === 1 ? 'border-ink bg-surface text-brand shadow-brutal-sm' : ($currentStep > 1 ? 'border-ink/30 bg-surface text-accent' : 'border-transparent text-ink/40') }}">
                                        <span class="font-display font-extrabold text-xs sm:text-sm leading-none">{{ $currentStep > 1 ? '✓' : '01' }}</span>
                                        <span class="text-xs font-sans font-bold uppercase tracking-tight mt-0.5">DATA</span>
                                    </div>

                                    <div class="flex flex-col items-center text-center p-1.5 sm:p-2 border-2 transition-all {{ $currentStep === 2 ? 'border-ink bg-surface text-brand shadow-brutal-sm' : ($currentStep > 2 ? 'border-ink/30 bg-surface text-accent' : 'border-transparent text-ink/40') }}">
                                        <span class="font-display font-extrabold text-xs sm:text-sm leading-none">{{ $currentStep > 2 ? '✓' : '02' }}</span>
                                        <span class="text-xs font-sans font-bold uppercase tracking-tight mt-0.5">AKUN</span>
                                    </div>

                                    <div class="flex flex-col items-center text-center p-1.5 sm:p-2 border-2 transition-all {{ $currentStep === 3 ? 'border-ink bg-surface text-brand shadow-brutal-sm' : ($currentStep > 3 ? 'border-ink/30 bg-surface text-accent' : 'border-transparent text-ink/40') }}">
                                        <span class="font-display font-extrabold text-xs sm:text-sm leading-none">{{ $currentStep > 3 ? '✓' : '03' }}</span>
                                        <span class="text-xs font-sans font-bold uppercase tracking-tight mt-0.5">OTP</span>
                                    </div>

                                    <div class="flex flex-col items-center text-center p-1.5 sm:p-2 border-2 transition-all {{ $currentStep === 4 ? 'border-ink bg-surface text-accent shadow-brutal-sm font-bold' : 'border-transparent text-ink/40' }}">
                                        <span class="font-display font-extrabold text-xs sm:text-sm leading-none">04</span>
                                        <span class="text-xs font-sans font-bold uppercase tracking-tight mt-0.5">SELESAI</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 sm:p-7">
                                @if ($errors->any() && ! $isIncompleteVoter && ! $isUnregisteredVoter)
                                    <div class="bg-surface-muted border-2 border-ink p-3.5 shadow-brutal-sm mb-5 text-ink">
                                        <div class="flex items-center gap-2 text-xs font-display font-bold uppercase text-brand mb-1">
                                            <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                                            <span>TERJADI KESALAHAN</span>
                                        </div>
                                        <ul class="text-xs font-sans font-medium space-y-1 text-ink/90">
                                            @foreach ($errors->all() as $error)
                                                <li>&bull; {{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($currentStep === 1)
                                    <div>
                                        <div class="mb-5">
                                            <h2 class="font-display font-bold text-base sm:text-lg text-brand uppercase tracking-tight mb-1">
                                                01. VERIFIKASI DATA MAHASISWA
                                            </h2>
                                            <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                                                Masukkan Nomor Induk Mahasiswa (NIM) dan tanggal lahir terdaftar untuk mencocokkan status kelayakan pemilih.
                                            </p>
                                        </div>

                                        @if ($isIncompleteVoter)
                                            <div class="bg-amber-50 border-2 border-ink p-4 sm:p-5 shadow-brutal mb-6 space-y-3.5">
                                                <div class="flex items-center gap-2 pb-2 border-b border-ink/20">
                                                    <span class="w-3 h-3 bg-amber-500 inline-block border border-ink"></span>
                                                    <span class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-brand">
                                                        DATA PEMILIH BELUM LENGKAP
                                                    </span>
                                                </div>

                                                <div class="space-y-2 text-xs sm:text-sm font-sans text-ink">
                                                    <p class="font-semibold text-brand">
                                                        Halo {{ $incompleteVoterName ?? 'Mahasiswa' }} (NIM: {{ $incompleteVoterNim ?? $nim }}),
                                                    </p>
                                                    <p class="text-ink/85 leading-relaxed text-xs sm:text-sm">
                                                        NIM kamu terdaftar dalam DPT PEMIRA, namun akun belum dapat dibuat secara mandiri karena ada data yang belum lengkap di sistem:
                                                    </p>
                                                    <ul class="list-disc list-inside font-bold text-amber-900 bg-amber-100/70 p-2.5 border border-ink/20 space-y-1 text-xs sm:text-sm">
                                                        @foreach ($incompleteMissingFields as $field)
                                                            <li>{{ $field }} belum terdata</li>
                                                        @endforeach
                                                    </ul>
                                                    <p class="text-ink/80 leading-relaxed text-xs">
                                                        Untuk melengkapi data dan mengaktifkan hak suara kamu di PEMIRA, silakan hubungi Tim Humas melalui kontak WhatsApp berikut:
                                                    </p>
                                                </div>

                                                <div class="pt-1 flex flex-wrap gap-2">
                                                    <a href="{{ $this->humasWhatsappUrl }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                                                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                                        </svg>
                                                        <span>HUBUNGI TIM HUMAS</span>
                                                        <span aria-hidden="true">&rarr;</span>
                                                    </a>

                                                    <button type="button"
                                                            wire:click="resetIncompleteState"
                                                            class="inline-flex items-center justify-center px-3.5 py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-ink transition-colors cursor-pointer">
                                                        COBA NIM LAIN
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($isUnregisteredVoter)
                                            <div class="bg-rose-50 border-2 border-ink p-4 sm:p-5 shadow-brutal mb-6 space-y-3">
                                                <div class="flex items-center gap-2 pb-2 border-b border-ink/20">
                                                    <span class="w-2.5 h-2.5 bg-red-600 inline-block border border-ink"></span>
                                                    <span class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-brand">
                                                        NIM BELUM TERDAFTAR DI DPT
                                                    </span>
                                                </div>

                                                <div class="space-y-1.5 text-xs sm:text-sm font-sans text-ink">
                                                    <p class="font-semibold text-brand">
                                                        NIM ({{ $unregisteredNim ?? $nim }}) tidak tercatat dalam data DPT PEMIRA.
                                                    </p>
                                                    <p class="text-ink/85 leading-relaxed text-xs">
                                                        Pendaftaran akun hanya bagi mahasiswa aktif di DPT. Jika kamu mahasiswa aktif PNB, silakan hubungi panitia untuk verifikasi:
                                                    </p>
                                                </div>

                                                <div class="pt-1 flex flex-wrap gap-2">
                                                    <a href="{{ $this->unregisteredHumasWhatsappUrl }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-accent text-ink hover:bg-accent-light border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                                                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                                        </svg>
                                                        <span>HUBUNGI HUMAS</span>
                                                        <span aria-hidden="true">&rarr;</span>
                                                    </a>

                                                    <a href="{{ $this->ketuaPanitiaWhatsappUrl }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-brand text-accent hover:bg-brand-dark border-2 border-ink shadow-brutal-sm font-display font-bold text-xs uppercase tracking-wider transition-all">
                                                        <span>KETUA PANITIA</span>
                                                        <span aria-hidden="true">&rarr;</span>
                                                    </a>

                                                    <button type="button"
                                                            wire:click="resetUnregisteredState"
                                                            class="inline-flex items-center justify-center px-3.5 py-2 bg-surface hover:bg-ink/10 border-2 border-ink text-xs font-display font-bold uppercase tracking-wider text-ink transition-colors cursor-pointer">
                                                        COBA NIM LAIN
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <form wire:submit="validateStudent" class="space-y-4 sm:space-y-5">
                                            <div>
                                                <label for="nim" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                                    NOMOR INDUK MAHASISWA (NIM)
                                                </label>
                                                <input
                                                    type="text"
                                                    id="nim"
                                                    wire:model="nim"
                                                    required
                                                    autofocus
                                                    autocomplete="off"
                                                    placeholder="Contoh: 2215354001"
                                                    class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                                >
                                                @error('nim')
                                                    @if (! $isIncompleteVoter && ! $isUnregisteredVoter)
                                                        <p class="mt-1.5 text-xs font-sans font-bold text-brand">
                                                            {{ $message }}
                                                        </p>
                                                    @endif
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="birth_date" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                                    TANGGAL LAHIR
                                                </label>
                                                <input
                                                    type="date"
                                                    id="birth_date"
                                                    wire:model="birth_date"
                                                    required
                                                    class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all"
                                                >
                                                <p class="mt-1 text-xs font-sans font-medium text-ink/60">
                                                    Digunakan sebagai autentikasi awal identitas mahasiswa.
                                                </p>
                                                @error('birth_date')
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
                                                <span wire:loading.remove wire:target="validateStudent">LANJUTKAN</span>
                                                <span wire:loading wire:target="validateStudent">MEMVERIFIKASI...</span>
                                                <span aria-hidden="true" wire:loading.remove wire:target="validateStudent">&rarr;</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if ($currentStep === 2)
                                    <div>
                                        <div class="mb-5">
                                            <h2 class="font-display font-bold text-base sm:text-lg text-brand uppercase tracking-tight mb-1">
                                                02. DATA AKUN & KATA SANDI
                                            </h2>
                                            <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                                                Data mahasiswa ditemukan. Lengkapi alamat email aktif dan buat kata sandi untuk akun voter kamu.
                                            </p>
                                        </div>

                                        <div class="bg-surface-muted border-2 border-ink p-3.5 sm:p-4 shadow-brutal-sm mb-5 space-y-2.5">
                                            <div class="flex items-center justify-between pb-2 border-b border-ink/15">
                                                <span class="text-xs font-display font-bold uppercase tracking-wider text-brand flex items-center gap-1.5">
                                                    <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                                                    <span>DATA MAHASISWA TERVERIFIKASI</span>
                                                </span>
                                                <span class="bg-accent text-ink px-2 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                                    ELIGIBLE
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                                <div>
                                                    <span class="text-xs font-sans font-bold uppercase text-ink/60 block">NAMA LENGKAP</span>
                                                    <span class="font-display font-bold text-brand uppercase">{{ $studentName }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-sans font-bold uppercase text-ink/60 block">NIM</span>
                                                    <span class="font-display font-bold text-brand uppercase">{{ $studentNim }}</span>
                                                </div>
                                            </div>

                                            <div class="text-xs pt-1 border-t border-ink/10">
                                                <span class="text-xs font-sans font-bold uppercase text-ink/60 block">PROGRAM STUDI</span>
                                                <span class="font-sans font-medium text-ink">{{ $studentProdi }}</span>
                                            </div>
                                        </div>

                                        <form wire:submit="submitAccountData" class="space-y-4 sm:space-y-5">
                                            <div>
                                                <label for="email" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                                    ALAMAT EMAIL AKTIF
                                                </label>
                                                <input
                                                    type="email"
                                                    id="email"
                                                    wire:model="email"
                                                    required
                                                    autocomplete="email"
                                                    placeholder="nama@student.pnb.ac.id"
                                                    class="w-full px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                                >
                                                <p class="mt-1 text-xs font-sans font-medium text-ink/60">
                                                    Kode verifikasi (OTP) akan dikirimkan ke alamat email ini.
                                                </p>
                                                @error('email')
                                                    <p class="mt-1.5 text-xs font-sans font-bold text-brand">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="password" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                                    BUAT KATA SANDI
                                                </label>
                                                <div class="relative">
                                                    <input
                                                        :type="showPassword ? 'text' : 'password'"
                                                        id="password"
                                                        wire:model="password"
                                                        required
                                                        autocomplete="new-password"
                                                        placeholder="Minimal 8 karakter"
                                                        class="w-full px-3.5 py-2.5 sm:py-3 pr-11 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                                    >
                                                    <button
                                                        type="button"
                                                        @click="showPassword = !showPassword"
                                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-ink/70 hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand transition-colors"
                                                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                                        :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                                    >
                                                        <svg x-show="!showPassword" class="w-5 h-5 block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <svg x-show="showPassword" x-cloak class="w-5 h-5 block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                @error('password')
                                                    <p class="mt-1.5 text-xs font-sans font-bold text-brand">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="password_confirmation" class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-1.5">
                                                    KONFIRMASI KATA SANDI
                                                </label>
                                                <div class="relative">
                                                    <input
                                                        :type="showConfirmPassword ? 'text' : 'password'"
                                                        id="password_confirmation"
                                                        wire:model="password_confirmation"
                                                        required
                                                        autocomplete="new-password"
                                                        placeholder="Ulangi kata sandi"
                                                        class="w-full px-3.5 py-2.5 sm:py-3 pr-11 text-xs sm:text-sm font-sans font-medium text-ink bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all placeholder:text-ink/40"
                                                    >
                                                    <button
                                                        type="button"
                                                        @click="showConfirmPassword = !showConfirmPassword"
                                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-ink/70 hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand transition-colors"
                                                        :aria-label="showConfirmPassword ? 'Sembunyikan konfirmasi kata sandi' : 'Tampilkan konfirmasi kata sandi'"
                                                        :title="showConfirmPassword ? 'Sembunyikan konfirmasi kata sandi' : 'Tampilkan konfirmasi kata sandi'"
                                                    >
                                                        <svg x-show="!showConfirmPassword" class="w-5 h-5 block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <svg x-show="showConfirmPassword" x-cloak class="w-5 h-5 block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3 pt-2">
                                                <button
                                                    type="button"
                                                    wire:click="goToStep(1)"
                                                    class="inline-flex items-center justify-center px-4 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-ink bg-surface-muted border-2 border-ink shadow-brutal-sm hover:bg-surface active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                                                >
                                                    &larr; KEMBALI
                                                </button>

                                                <button
                                                    type="submit"
                                                    wire:loading.attr="disabled"
                                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand disabled:opacity-60 disabled:cursor-not-allowed"
                                                >
                                                    <span wire:loading.remove wire:target="submitAccountData">LANJUTKAN KE VERIFIKASI</span>
                                                    <span wire:loading wire:target="submitAccountData">MENGIRIM OTP...</span>
                                                    <span aria-hidden="true" wire:loading.remove wire:target="submitAccountData">&rarr;</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if ($currentStep === 3)
                                    <div>
                                        <div class="mb-5">
                                            <h2 class="font-display font-bold text-base sm:text-lg text-brand uppercase tracking-tight mb-1">
                                                03. VERIFIKASI EMAIL (OTP)
                                            </h2>
                                            <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 leading-relaxed">
                                                Masukkan 6 digit kode verifikasi yang telah dikirimkan ke email:
                                                <span class="font-bold text-brand block sm:inline mt-0.5 sm:mt-0">{{ $email }}</span>
                                            </p>
                                        </div>

                                        <form wire:submit="verifyOtp" class="space-y-5">
                                            <div>
                                                <label class="block text-xs sm:text-sm font-display font-bold uppercase tracking-wider text-brand mb-2 text-center">
                                                    KODE VERIFIKASI 6 DIGIT
                                                </label>

                                                <div class="flex items-center justify-center gap-2 sm:gap-3" @paste="handleOtpPaste($event)">
                                                    @for ($i = 0; $i < 6; $i++)
                                                        <input
                                                            type="text"
                                                            inputmode="numeric"
                                                            pattern="[0-9]*"
                                                            maxlength="1"
                                                            wire:model="otp.{{ $i }}"
                                                            x-ref="otp{{ $i }}"
                                                            @input="handleOtpInput($event, {{ $i }})"
                                                            @keydown="handleOtpKeydown($event, {{ $i }})"
                                                            class="w-10 h-12 sm:w-12 sm:h-14 text-center font-display font-black text-xl sm:text-2xl text-brand bg-surface border-2 border-ink shadow-brutal-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-ink transition-all select-all"
                                                        >
                                                    @endfor
                                                </div>

                                                <p class="mt-2 text-center text-xs font-sans font-medium text-ink/60">
                                                    Kode berlaku selama 5 menit.
                                                </p>
                                                @error('otp')
                                                    <p class="mt-1.5 text-center text-xs font-sans font-bold text-brand">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <div class="bg-surface-muted border-2 border-ink p-3 text-center">
                                                <div class="text-xs font-sans font-medium text-ink">
                                                    Tidak menerima kode?
                                                    <button
                                                        type="button"
                                                        wire:click="resendOtp"
                                                        :disabled="!resendAvailable"
                                                        class="font-display font-bold uppercase text-brand hover:text-ink underline disabled:opacity-50 disabled:no-underline disabled:cursor-not-allowed transition-colors ml-1"
                                                    >
                                                        Kirim ulang kode
                                                    </button>
                                                    <span x-show="!resendAvailable" class="text-ink/60 font-sans ml-1 text-xs">
                                                        (<span x-text="countdown">60</span>s)
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3 pt-2">
                                                <button
                                                    type="button"
                                                    wire:click="goToStep(2)"
                                                    class="inline-flex items-center justify-center px-4 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-ink bg-surface-muted border-2 border-ink shadow-brutal-sm hover:bg-surface active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                                                >
                                                    &larr; UBAH EMAIL
                                                </button>

                                                <button
                                                    type="submit"
                                                    wire:loading.attr="disabled"
                                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand disabled:opacity-60 disabled:cursor-not-allowed"
                                                >
                                                    <span wire:loading.remove wire:target="verifyOtp">VERIFIKASI & SELESAIKAN</span>
                                                    <span wire:loading wire:target="verifyOtp">MEMVERIFIKASI...</span>
                                                    <span aria-hidden="true" wire:loading.remove wire:target="verifyOtp">&rarr;</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if ($currentStep === 4)
                                    <div>
                                        <div class="text-center py-2 space-y-4">
                                            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto bg-accent border-2 border-ink shadow-brutal flex items-center justify-center">
                                                <span class="font-display font-black text-2xl sm:text-3xl text-ink">✓</span>
                                            </div>

                                            <div>
                                                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-brand uppercase tracking-tight">
                                                    REGISTRASI BERHASIL!
                                                </h2>
                                                <p class="text-xs sm:text-sm font-sans font-medium text-ink/80 max-w-sm mx-auto mt-1 leading-relaxed">
                                                    Akun pemilih atas nama <span class="font-bold text-brand uppercase">{{ $studentName }}</span> telah aktif dan terverifikasi.
                                                </p>
                                            </div>

                                            <div class="bg-surface-muted border-2 border-ink p-4 text-left shadow-brutal-sm space-y-2 max-w-sm mx-auto">
                                                <div class="flex items-center justify-between pb-1.5 border-b border-ink/15 text-xs">
                                                    <span class="text-xs font-sans font-bold uppercase text-ink/60">NIM</span>
                                                    <span class="font-display font-bold text-brand uppercase">{{ $studentNim }}</span>
                                                </div>
                                                <div class="flex items-center justify-between pb-1.5 border-b border-ink/15 text-xs">
                                                    <span class="text-xs font-sans font-bold uppercase text-ink/60">EMAIL</span>
                                                    <span class="font-sans font-medium text-ink truncate max-w-50">{{ $email }}</span>
                                                </div>
                                                <div class="flex items-center justify-between pt-0.5 text-xs">
                                                    <span class="text-xs font-sans font-bold uppercase text-ink/60">STATUS HAK SUARA</span>
                                                    <span class="bg-accent text-ink px-2 py-0.5 text-xs font-display font-bold uppercase border border-ink">
                                                        TERDAFTAR
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="pt-2">
                                                <a
                                                    href="{{ route('login') }}"
                                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                                                >
                                                    <span>MASUK KE PEMIRA</span>
                                                    <span aria-hidden="true">&rarr;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-6 pt-5 border-t-2 border-ink/15 text-center">
                                    <p class="text-xs sm:text-sm font-sans font-medium text-ink">
                                        Sudah memiliki akun pemilih?
                                        <a
                                            href="{{ route('login') }}"
                                            class="font-bold text-brand hover:text-ink underline transition-colors focus:outline-none focus:ring-1 focus:ring-brand"
                                        >
                                            Masuk di sini &rarr;
                                        </a>
                                    </p>
                                </div>
                            </div>
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
