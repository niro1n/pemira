@extends('layouts.public', [
    'title' => 'Pemeliharaan Sistem — PEMIRA PNB',
    'description' => 'Sistem PEMIRA Politeknik Negeri Bali sedang dalam pemeliharaan berkala.',
])

@section('content')
    <div class="min-h-screen bg-surface-muted flex flex-col justify-between p-4 sm:p-8 selection:bg-accent selection:text-ink">
        <header class="max-w-4xl mx-auto w-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand text-accent border-2 border-ink shadow-brutal-sm flex items-center justify-center font-display font-black text-xl">
                    P
                </div>
                <div>
                    <span class="font-display font-black text-base text-brand uppercase tracking-tight block leading-none">
                        PEMIRA PNB
                    </span>
                    <span class="text-xs font-sans font-bold uppercase tracking-wider text-ink/60 mt-1 block">
                        Politeknik Negeri Bali
                    </span>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 border-2 border-ink shadow-brutal-sm text-xs font-display font-black text-amber-950 uppercase tracking-wider">
                <span class="w-2 h-2 bg-amber-500 inline-block border border-ink"></span>
                <span>PEMELIHARAAN</span>
            </div>
        </header>

        <main class="max-w-xl mx-auto w-full my-8 sm:my-12">
            <div class="bg-surface border-2 border-ink shadow-brutal p-6 sm:p-10 space-y-6 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-accent text-brand border-2 border-ink shadow-brutal mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>

                <div class="space-y-2">
                    <span class="inline-block px-3 py-1 bg-surface-muted border border-ink text-xs font-display font-bold uppercase tracking-wider text-ink/70">
                        STATUS OPERASIONAL SISTEM
                    </span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl text-brand uppercase tracking-tight leading-tight">
                        Website Sedang Dalam Pemeliharaan
                    </h1>
                    <p class="text-xs sm:text-sm font-sans text-ink/70 max-w-md mx-auto leading-relaxed">
                        Kami sedang melakukan pemeliharaan sistem. Silakan kembali beberapa saat lagi.
                    </p>
                </div>

                <div class="p-4 bg-surface-muted border-2 border-ink text-left space-y-2">
                    <div class="flex items-center gap-2 text-xs font-display font-black uppercase text-brand">
                        <span class="w-2 h-2 bg-brand inline-block"></span>
                        <span>Informasi Pemilih & Pengguna</span>
                    </div>
                    <ul class="text-xs font-sans text-ink/80 space-y-1.5 list-disc list-inside leading-relaxed">
                        <li>Seluruh data pemilih dan arsip pemilihan tetap terlindungi dengan aman.</li>
                        <li>Akses akan dibuka kembali secara otomatis setelah proses pemeliharaan selesai.</li>
                    </ul>
                </div>

                <div class="pt-2 border-t-2 border-ink flex items-center justify-between text-xs font-sans text-ink/60">
                    <span class="font-bold">Waktu Sekarang:</span>
                    <span class="font-mono font-bold">{{ now()->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i:s') }} WITA</span>
                </div>
            </div>
        </main>

        <footer class="max-w-4xl mx-auto w-full text-center text-xs font-sans text-ink/50 py-2">
            &copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR) Politeknik Negeri Bali. Seluruh hak cipta dilindungi.
        </footer>
    </div>
@endsection
