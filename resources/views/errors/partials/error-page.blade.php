<div class="w-full max-w-xl mx-auto">
    <div class="relative bg-surface border-2 border-ink shadow-brutal-lg p-6 sm:p-10">
        <div class="absolute -top-3 -right-3 px-3 py-1 bg-accent border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase tracking-wider text-ink">
            {{ $badge ?? 'STATUS ' . $code }}
        </div>

        <div class="space-y-6 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center sm:items-baseline gap-2 sm:gap-4 border-b-2 border-ink pb-6">
                <span class="text-7xl sm:text-8xl font-display font-black text-brand tracking-tighter leading-none">
                    {{ $code }}
                </span>
                <div class="h-2 w-2 bg-accent hidden sm:block"></div>
                <span class="text-xs font-mono font-bold tracking-widest uppercase text-ink/60">
                    HTTP RESPONSE
                </span>
            </div>

            <div class="space-y-3">
                <h1 class="text-2xl sm:text-3xl font-display font-extrabold text-ink tracking-tight uppercase">
                    {{ $title }}
                </h1>
                <p class="text-sm sm:text-base font-sans text-ink/80 leading-relaxed">
                    {{ $description }}
                </p>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @if(isset($primaryCtaText) && $primaryCtaText)
                    <a
                        href="{{ $primaryCtaUrl ?? route('home') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-display font-bold uppercase tracking-wider text-surface bg-brand border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-brand-dark active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        <span>{{ $primaryCtaText }}</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                @endif

                @if(isset($secondaryCtaText) && $secondaryCtaText)
                    <a
                        href="{{ $secondaryCtaUrl ?? route('home') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-display font-bold uppercase tracking-wider text-ink bg-surface-muted border-2 border-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm hover:bg-accent/20 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-brand"
                    >
                        <span>{{ $secondaryCtaText }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
