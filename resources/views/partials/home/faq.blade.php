@php
    $humasWaNumber = env('HUMAS_WHATSAPP', config('pemira.contacts.humas.whatsapp_number', '6281337534761'));
    $ketuaWaNumber = env('KETUA_PANITIA_WHATSAPP', config('pemira.contacts.ketua_panitia.whatsapp_number', '628970898383'));

    $humasWhatsappUrl = "https://wa.me/{$humasWaNumber}?text=".rawurlencode('Halo kak Sintya (Humas PEMIRA), saya ingin bertanya seputar PEMIRA.');
    $ketuaWhatsappUrl = "https://wa.me/{$ketuaWaNumber}?text=".rawurlencode('Halo kak Diana (Ketua Panitia PEMIRA), saya ingin bertanya seputar PEMIRA.');
    $whatsappUrl = $humasWhatsappUrl;

    $faqs = [
        [
            'number' => '01',
            'question' => 'Siapa yang dapat memilih?',
            'answer' =>
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent commodo, mauris sed tincidunt consequat, justo erat facilisis lorem, vitae posuere neque erat vel nisl. Seluruh mahasiswa aktif yang terdaftar berhak menggunakan hak pilihnya.',
        ],
        [
            'number' => '02',
            'question' => 'Bagaimana cara mendaftar sebagai pemilih?',
            'answer' =>
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer facilisis, nisl at interdum tincidunt, lorem neque consequat lorem, nec semper urna nisi eget lacus. Pendaftaran dilakukan secara online melalui portal resmi PEMIRA.',
        ],
        [
            'number' => '03',
            'question' => 'Bagaimana jika saya lupa password?',
            'answer' =>
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris feugiat ligula a nisl pellentesque, sit amet bibendum felis dignissim. Gunakan fitur pemulihan kata sandi dengan memasukkan NIM dan tanggal lahir terverifikasi.',
        ],
        [
            'number' => '04',
            'question' => 'Apakah suara yang sudah diberikan dapat diubah?',
            'answer' =>
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis auctor, nunc non faucibus molestie, magna nunc feugiat velit, vel lacinia libero mauris id tortor. Setiap pemilih hanya memiliki satu kali kesempatan dan suara yang telah dikirim bersifat final.',
        ],
        [
            'number' => '05',
            'question' => 'Kapan pemungutan suara dilaksanakan?',
            'answer' =>
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pemungutan suara berlangsung sesuai rentang tanggal pada jadwal resmi. Sistem e-voting akan dibuka secara otomatis pada pukul 08.00 hingga 16.00 WITA.',
        ],
        [
            'number' => '06',
            'question' => 'Kapan hasil PEMIRA diumumkan?',
            'answer' =>
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Penghitungan dan rekapitulasi perolehan suara dilakukan secara transparan segera setelah sesi pemungutan suara resmi ditutup oleh panitia.',
        ],
        [
            'number' => '07',
            'question' => 'Bagaimana jika saya mengalami kendala saat memilih?',
            'answer' =>
                'Apabila mengalami kendala teknis, gagal login, atau gangguan sistem saat voting, silakan langsung menghubungi tim panitia: Sintya (Humas) atau Diana (Ketua Panitia) melalui tautan WhatsApp resmi di bawah.',
        ],
    ];
@endphp

<section id="faq"
    class="relative w-full bg-surface border-b-2 border-ink overflow-hidden py-8 sm:py-12 md:py-16">
    <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0" aria-hidden="true">
        <div
            class="hidden md:block absolute -top-10 -left-4 font-display font-black text-8xl lg:text-9xl text-ink/5 tracking-tighter leading-none">
            05
        </div>

        <svg class="absolute top-10 right-8 w-44 h-44 text-ink/10 hidden sm:block pointer-events-none"
            fill="currentColor">
            <pattern id="faq-dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#faq-dots)" />
        </svg>

        <div class="hidden lg:block absolute top-0 left-1/3 w-px h-full bg-ink/10"></div>
        <div class="hidden lg:block absolute top-0 right-1/4 w-px h-full bg-ink/10"></div>
        <div class="hidden sm:flex absolute top-12 right-1/3 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div
            class="hidden sm:flex absolute bottom-12 left-1/4 text-ink/25 font-display font-bold text-lg leading-none">
            +
        </div>
        <div class="hidden lg:block absolute top-1/4 left-8 w-3 h-3 bg-accent border border-ink"></div>
        <div class="hidden md:block absolute bottom-1/4 right-10 w-2.5 h-2.5 bg-brand"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">
            <div class="lg:col-span-5 flex flex-col items-start lg:sticky lg:top-24">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-surface-muted border-2 border-ink shadow-brutal-sm text-xs font-sans font-bold tracking-wider uppercase text-brand mb-3 sm:mb-4">
                    <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                    <span>PERTANYAAN UMUM</span>
                </div>

                <h2
                    class="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl lg:text-5xl tracking-tight uppercase text-ink mb-2 sm:mb-3 leading-none space-y-0.5 sm:space-y-1">
                    <span class="block">MASIH BINGUNG?</span>
                    <span class="block text-brand">KAMI JAWAB.</span>
                </h2>

                <p
                    class="text-xs sm:text-sm lg:text-base text-ink font-sans font-medium leading-relaxed max-w-md mb-4 sm:mb-6">
                    Temukan jawaban atas pertanyaan yang paling sering muncul seputar PEMIRA.
                </p>

                <div class="hidden lg:flex flex-col gap-2 w-full max-w-sm pt-4 border-t-2 border-ink/15">
                    <div
                        class="flex items-center gap-2 text-xs font-sans font-bold uppercase tracking-wider text-brand">
                        <span class="w-2 h-2 bg-accent inline-block border border-ink"></span>
                        <span>7 PERTANYAAN TERSEDIA</span>
                    </div>
                    <div class="text-xs font-sans font-medium text-ink/70 leading-relaxed">
                        Klik pada pertanyaan untuk membaca informasi selengkapnya.
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-2.5 sm:space-y-3" x-data="{ active: null }">
                @foreach ($faqs as $index => $faq)
                    <div
                        class="bg-surface border-2 border-ink shadow-brutal transition-all duration-150 overflow-hidden">
                        <button type="button"
                            class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-brand focus:ring-inset group"
                            @click="active = (active === {{ $index }} ? null : {{ $index }})"
                            :aria-expanded="active === {{ $index }} ? 'true' : 'false'"
                            aria-controls="faq-panel-{{ $index }}" id="faq-btn-{{ $index }}">
                            <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0 pr-1">
                                <span
                                    class="font-display font-black text-base sm:text-lg text-accent group-hover:text-brand transition-colors shrink-0">
                                    {{ $faq['number'] }}
                                </span>
                                <span
                                    class="font-display font-bold text-xs sm:text-sm lg:text-base text-brand uppercase tracking-tight leading-snug">
                                    {{ $faq['question'] }}
                                </span>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-surface-muted border-2 border-ink shadow-brutal-sm flex items-center justify-center shrink-0 font-display font-black text-sm sm:text-base text-brand group-hover:bg-accent group-hover:text-ink transition-colors"
                                aria-hidden="true" x-text="active === {{ $index }} ? '−' : '+'">
                                +
                            </div>
                        </button>

                        <div id="faq-panel-{{ $index }}" role="region"
                            aria-labelledby="faq-btn-{{ $index }}"
                            class="border-t-2 border-ink/10 px-3.5 sm:px-4 pt-2.5 pb-3.5 sm:pb-4 bg-surface-muted/50"
                            x-show="active === {{ $index }}" x-cloak>
                            <div class="pl-0 sm:pl-8">
                                <p class="text-xs sm:text-sm font-sans font-medium text-ink/85 leading-relaxed">
                                    {{ $faq['answer'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 sm:mt-10 lg:mt-12">
            <div
                class="bg-brand text-surface border-2 border-ink shadow-brutal-lg p-4 sm:p-6 lg:p-7 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-8 font-display font-black text-7xl lg:text-8xl text-surface/5 select-none pointer-events-none leading-none"
                    aria-hidden="true">
                    HELP
                </div>

                <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6 items-center">
                    <div class="md:col-span-7">
                        <div
                            class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-brand-dark border border-surface/20 text-xs font-sans font-bold uppercase tracking-wider text-accent mb-2">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-accent inline-block border border-ink"></span>
                            <span>BANTUAN PANITIA</span>
                        </div>

                        <h3
                            class="font-display font-extrabold text-xl sm:text-2xl lg:text-3xl uppercase tracking-tight text-surface leading-tight mb-1 sm:mb-2">
                            MASIH ADA YANG MAU DITANYAKAN?
                        </h3>

                        <p class="text-xs sm:text-sm font-sans font-medium text-surface/85 max-w-xl leading-relaxed">
                            Tidak menemukan jawaban yang kamu cari? Hubungi panitia PEMIRA melalui WhatsApp untuk
                            mendapatkan bantuan langsung:
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs font-sans text-surface/90">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-accent inline-block"></span>
                                Humas: <strong class="text-accent">Sintya</strong>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-accent inline-block"></span>
                                Ketua Panitia: <strong class="text-accent">Diana</strong>
                            </span>
                        </div>
                    </div>

                    <div class="md:col-span-5 flex flex-col gap-2.5 pt-1 md:pt-0">
                        <a href="{{ $humasWhatsappUrl }}" target="_blank" rel="noopener noreferrer"
                            class="w-full inline-flex items-center justify-between gap-2 px-4 py-2.5 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-ink bg-accent border-2 border-ink shadow-brutal hover:bg-accent-light hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-surface">
                            <span>HUBUNGI HUMAS</span>
                            <span class="font-display text-xs font-bold">CHAT &rarr;</span>
                        </a>
                        <a href="{{ $ketuaWhatsappUrl }}" target="_blank" rel="noopener noreferrer"
                            class="w-full inline-flex items-center justify-between gap-2 px-4 py-2.5 text-xs sm:text-sm font-display font-bold tracking-wide uppercase text-surface bg-brand-dark border-2 border-surface/30 hover:border-accent hover:text-accent shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm active:translate-x-1 active:translate-y-1 active:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-surface">
                            <span>KETUA PANITIA</span>
                            <span class="font-display text-xs font-bold text-surface/90">CHAT &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
