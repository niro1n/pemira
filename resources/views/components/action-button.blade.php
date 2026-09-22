@props([
    'variant' => 'detail',
    'label' => null,
    'icon' => null,
])

@php
    $variant = $variant ?? 'detail';
    $label = $label ?? match ($variant) {
        'detail' => 'Lihat detail',
        'edit' => 'Edit data',
        'delete' => 'Hapus data',
        default => 'Aksi',
    };

    $icon = $icon ?? match ($variant) {
        'detail' => 'eye',
        'edit' => 'pencil',
        'delete' => 'trash',
        default => 'eye',
    };

    $variantClasses = match ($variant) {
        'detail' => 'bg-surface-muted hover:bg-accent text-ink hover:text-ink',
        'edit' => 'bg-brand text-surface hover:bg-brand-dark hover:text-surface',
        'delete' => 'bg-surface-muted hover:bg-red-600 hover:text-white text-ink',
        default => 'bg-surface-muted hover:bg-accent text-ink',
    };
@endphp

<div class="relative group/action inline-flex items-center justify-center">
    <button {{ $attributes->merge([
        'type' => 'button',
        'class' => "w-8 h-8 sm:w-8.5 sm:h-8.5 inline-flex items-center justify-center border border-ink transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1 {$variantClasses}"
    ]) }} aria-label="{{ $label }}" title="{{ $label }}">
        @if ($icon === 'eye')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        @elseif ($icon === 'pencil')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
        @elseif ($icon === 'trash')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        @endif
    </button>
    <span role="tooltip" class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/action:block group-focus-within/action:block px-2 py-0.5 text-[10px] font-sans font-bold uppercase tracking-wider text-surface bg-ink whitespace-nowrap shadow-sm z-30">
        {{ $label }}
    </span>
</div>
