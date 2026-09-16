@props([
    'brandName' => 'PEMIRA',
    'period' => "'26",
])

<nav
    class="w-full bg-surface border-b-2 border-ink"
    x-data="{
        mobileOpen: false,
        activeSection: 'beranda',
        sectionIds: ['beranda', 'jadwal', 'paslon', 'cara-memilih', 'faq'],
        isTicking: false,
        init() {
            const initialHash = window.location.hash.replace('#', '');
            if (this.sectionIds.includes(initialHash)) {
                this.activeSection = initialHash;
            } else {
                this.computeActive();
            }

            window.addEventListener('scroll', () => {
                if (!this.isTicking) {
                    window.requestAnimationFrame(() => {
                        this.computeActive();
                        this.isTicking = false;
                    });
                    this.isTicking = true;
                }
            }, { passive: true });

            window.addEventListener('resize', () => {
                this.computeActive();
            }, { passive: true });

            window.addEventListener('hashchange', () => {
                const hash = window.location.hash.replace('#', '');
                if (this.sectionIds.includes(hash)) {
                    this.activeSection = hash;
                } else {
                    this.computeActive();
                }
            });
        },
        computeActive() {
            const scrollY = window.scrollY || window.pageYOffset;
            const viewportHeight = window.innerHeight;
            const scrollHeight = document.documentElement.scrollHeight;

            if (scrollY < 80) {
                this.activeSection = 'beranda';
                return;
            }

            if (scrollY + viewportHeight >= scrollHeight - 60) {
                this.activeSection = 'faq';
                return;
            }

            const targetOffset = 130;
            let current = 'beranda';

            for (let i = 0; i < this.sectionIds.length; i++) {
                const el = document.getElementById(this.sectionIds[i]);
                if (el) {
                    const rect = el.getBoundingClientRect();
                    if (rect.top <= targetOffset) {
                        current = this.sectionIds[i];
                    }
                }
            }
            this.activeSection = current;
        },
        navClick(sectionId) {
            this.activeSection = sectionId;
            this.mobileOpen = false;
        }
    }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group focus:outline-none focus:ring-2 focus:ring-brand">
                    <img
                        src="{{ asset('img/logos/organization/kpr-logo-no-text.png') }}"
                        alt="Logo KPR"
                        class="h-9 w-auto sm:h-11 object-contain shrink-0"
                    >
                    <div class="flex items-baseline font-display">
                        <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-brand">
                            {{ $brandName }}
                        </span>
                        <span class="text-xl sm:text-2xl font-extrabold text-accent ml-1">
                            {{ $period }}
                        </span>
                    </div>
                </a>

                <div class="hidden xl:block h-6 w-0.5 bg-ink mx-6"></div>
            </div>

            <div class="hidden lg:flex items-center gap-1 xl:gap-2">
                @php
                    $navItems = [
                        ['id' => 'beranda', 'label' => 'BERANDA'],
                        ['id' => 'jadwal', 'label' => 'JADWAL'],
                        ['id' => 'paslon', 'label' => 'PASLON'],
                        ['id' => 'cara-memilih', 'label' => 'CARA MEMILIH'],
                        ['id' => 'faq', 'label' => 'FAQ'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <a
                        href="{{ route('home') }}#{{ $item['id'] }}"
                        @click="navClick('{{ $item['id'] }}')"
                        :aria-current="activeSection === '{{ $item['id'] }}' ? 'page' : null"
                        class="px-3 py-1.5 text-sm font-sans transition-all focus:outline-none focus:ring-2 focus:ring-brand border-2"
                        :class="activeSection === '{{ $item['id'] }}'
                            ? 'border-ink bg-surface-muted text-brand font-bold shadow-brutal-sm'
                            : 'border-transparent text-ink font-semibold hover:border-ink hover:bg-surface-muted hover:text-brand'"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="hidden lg:flex items-center">
                @auth
                    <a
                        href="{{ auth()->user()->canAccessAdminPanel() ? url('/admin') : (Route::has('profile') ? route('profile') : route('home')) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        <span>PROFIL</span>
                    </a>
                @else
                    <a
                        href="{{ Route::has('login') ? route('login') : '#' }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        <span>MASUK</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                @endauth
            </div>

            <div class="flex lg:hidden">
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen ? 'true' : 'false'"
                    aria-label="Menu navigasi"
                    class="inline-flex items-center justify-center p-2 text-ink bg-surface-muted border-2 border-ink shadow-brutal-sm hover:bg-surface active:translate-x-0.5 active:translate-y-0.5 active:shadow-none focus:outline-none focus:ring-2 focus:ring-brand"
                >
                    <svg x-show="!mobileOpen" class="w-6 h-6 block" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-linejoin="miter" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6 block" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="square" stroke-linejoin="miter" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="mobileOpen"
        x-cloak
        @click.outside="mobileOpen = false"
        class="lg:hidden border-t-2 border-ink bg-surface px-4 pt-3 pb-5 space-y-2"
    >
        @foreach ($navItems as $item)
            <a
                href="{{ route('home') }}#{{ $item['id'] }}"
                @click="navClick('{{ $item['id'] }}')"
                :aria-current="activeSection === '{{ $item['id'] }}' ? 'page' : null"
                class="block px-3 py-2 text-base font-sans transition-all focus:outline-none focus:ring-2 focus:ring-brand border-2"
                :class="activeSection === '{{ $item['id'] }}'
                    ? 'border-ink bg-surface-muted text-brand font-bold shadow-brutal-sm'
                    : 'border-transparent text-ink font-semibold hover:border-ink hover:bg-surface-muted hover:text-brand'"
            >
                {{ $item['label'] }}
            </a>
        @endforeach

        <div class="pt-2">
            @auth
                <a
                    href="{{ auth()->user()->canAccessAdminPanel() ? url('/admin') : (Route::has('profile') ? route('profile') : route('home')) }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-base font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                >
                    <span>PROFIL</span>
                </a>
            @else
                <a
                    href="{{ Route::has('login') ? route('login') : '#' }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-base font-display font-bold tracking-wide uppercase text-surface bg-brand border-2 border-ink shadow-brutal hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                >
                    <span>MASUK</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endauth
        </div>
    </div>
</nav>

