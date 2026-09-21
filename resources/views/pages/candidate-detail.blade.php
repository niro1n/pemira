@extends('layouts.public', [
    'title' => 'Paslon ' . $candidate->formattedNumber() . ' — ' . ($election ? $election->name : 'PEMIRA PNB 2026'),
    'description' => 'Profil lengkap, visi, dan misi Paslon ' . $candidate->formattedNumber() . ' pada ' . ($election ? $election->name : 'PEMIRA PNB 2026'),
])

@section('content')
    @include('partials.home.navbar', [
        'election' => $election,
        'activeSection' => 'paslon',
    ])

    @php
        $leader = $candidate->candidateMembers->firstWhere('position', 'ketua')?->eligibleVoter;
        $viceLeader = $candidate->candidateMembers->firstWhere('position', 'wakil')?->eligibleVoter;
        $leaderName = $leader?->name ?? 'Kandidat Ketua';
        $viceLeaderName = $viceLeader?->name ?? 'Kandidat Wakil';
        $number = $candidate->formattedNumber();
    @endphp

    <main class="bg-surface py-6 sm:py-8 md:py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-4 sm:space-y-5">
            <div>
                <a href="{{ route('home') }}#paslon"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface text-ink hover:bg-brand hover:text-surface active:bg-ink active:text-surface border-2 border-ink shadow-brutal-sm text-xs font-display font-bold uppercase tracking-wider transition-all focus:outline-none focus:ring-2 focus:ring-brand cursor-pointer">
                    <span aria-hidden="true">&larr;</span>
                    <span>KEMBALI KE PASLON</span>
                </a>
            </div>

            <div class="bg-brand text-surface px-4 py-2.5 sm:px-5 sm:py-3 border-2 border-ink shadow-brutal flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 bg-accent inline-block border border-ink shrink-0"></span>
                    <h1 class="font-display font-black text-lg sm:text-xl md:text-2xl uppercase tracking-wider text-surface leading-none">
                        PASLON {{ $number }}
                    </h1>
                </div>
                <span class="px-2.5 py-0.5 bg-accent text-ink border border-ink font-display font-bold text-xs uppercase tracking-wider">
                    KANDIDAT BEM
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-stretch">
                <div class="md:col-span-5 bg-surface-muted border-2 border-ink p-2.5 shadow-brutal flex items-center justify-center">
                    <div class="w-full aspect-4/5 max-w-[200px] sm:max-w-[220px] md:max-w-none mx-auto bg-surface border-2 border-ink overflow-hidden relative shadow-brutal-sm flex items-center justify-center">
                        @if ($candidate->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($candidate->photo))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($candidate->photo) }}"
                                 alt="Foto Resmi Paslon {{ $number }}"
                                 class="w-full h-full object-cover object-top" />
                        @else
                            <div class="flex flex-col items-center justify-center text-center p-3 space-y-1">
                                <div class="w-10 h-10 bg-surface-muted border border-ink flex items-center justify-center">
                                    <svg class="w-5 h-5 text-ink/40" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                                <span class="font-display font-bold text-[10px] uppercase text-ink/40">
                                    BELUM DIUNGGAH
                                </span>
                            </div>
                        @endif

                        <div class="absolute top-1.5 right-1.5 px-1.5 py-0.2 bg-brand text-accent border border-ink font-display font-black text-[10px]">
                            #{{ $number }}
                        </div>
                    </div>
                </div>

                <div class="md:col-span-7 bg-surface border-2 border-ink shadow-brutal p-3.5 sm:p-4 flex flex-col justify-between space-y-2.5">
                    <div class="bg-surface-muted border border-ink p-2.5 sm:p-3 space-y-0.5">
                        <div class="flex items-center justify-between text-[11px] font-sans font-bold uppercase tracking-wider text-brand">
                            <span>CALON KETUA BEM</span>
                            <span class="px-1.5 py-0.2 bg-accent/30 border border-ink font-mono text-[10px] font-bold">KETUA</span>
                        </div>
                        <div class="font-display font-black text-sm sm:text-base text-ink uppercase leading-snug">
                            {{ $leaderName }}
                        </div>
                        <div class="text-xs font-sans text-ink/75 pt-1 border-t border-ink/10 flex flex-wrap items-center gap-x-1.5">
                            <span>NIM: <span class="font-mono font-bold text-ink">{{ $leader?->nim ?? '-' }}</span></span>
                            <span class="text-ink/40">&bull;</span>
                            <span>{{ $leader?->studyProgram?->name ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="bg-surface-muted border border-ink p-2.5 sm:p-3 space-y-0.5">
                        <div class="flex items-center justify-between text-[11px] font-sans font-bold uppercase tracking-wider text-brand">
                            <span>CALON WAKIL KETUA BEM</span>
                            <span class="px-1.5 py-0.2 bg-accent/30 border border-ink font-mono text-[10px] font-bold">WAKIL</span>
                        </div>
                        <div class="font-display font-black text-sm sm:text-base text-ink uppercase leading-snug">
                            {{ $viceLeaderName }}
                        </div>
                        <div class="text-xs font-sans text-ink/75 pt-1 border-t border-ink/10 flex flex-wrap items-center gap-x-1.5">
                            <span>NIM: <span class="font-mono font-bold text-ink">{{ $viceLeader?->nim ?? '-' }}</span></span>
                            <span class="text-ink/40">&bull;</span>
                            <span>{{ $viceLeader?->studyProgram?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-surface border-2 border-ink shadow-brutal p-4 sm:p-5 space-y-3.5">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-brand text-accent font-display font-bold text-[11px] uppercase tracking-wider border border-ink">
                        <span class="w-1.5 h-1.5 bg-accent inline-block"></span>
                        <span>VISI</span>
                    </div>
                    <p class="font-sans text-xs sm:text-sm font-semibold text-ink leading-relaxed">
                        {{ $candidate->vision }}
                    </p>
                </div>

                <div class="border-t-2 border-ink/15"></div>

                <div class="space-y-2.5">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-brand text-accent font-display font-bold text-[11px] uppercase tracking-wider border border-ink">
                        <span class="w-1.5 h-1.5 bg-accent inline-block"></span>
                        <span>MISI PASLON {{ $number }}</span>
                    </div>

                    @if ($candidate->candidateMissions->isNotEmpty())
                        <ol class="space-y-2">
                            @foreach ($candidate->candidateMissions as $idx => $missionItem)
                                <li class="flex items-start gap-2.5 bg-surface-muted border-2 border-ink p-2.5 sm:p-3 shadow-brutal-sm">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 bg-brand text-accent font-mono font-bold text-xs border border-ink shrink-0 select-none">
                                        {{ str_pad((string) ($missionItem->sort_order ?: ($idx + 1)), 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="font-sans text-xs sm:text-sm font-medium text-ink leading-relaxed flex-1 break-words">
                                        {{ $missionItem->content }}
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    @elseif (! empty($candidate->mission))
                        <div class="p-3 bg-surface-muted border-2 border-ink font-sans text-xs sm:text-sm font-medium text-ink leading-relaxed whitespace-pre-line">
                            {{ $candidate->mission }}
                        </div>
                    @else
                        <p class="text-xs font-sans text-ink/60 italic">Belum ada butir misi yang ditetapkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @include('partials.home.footer', ['election' => $election])
@endsection
