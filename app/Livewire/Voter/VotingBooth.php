<?php

namespace App\Livewire\Voter;

use App\Models\AuditLog;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\User;
use App\Models\VotingParticipation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Bilik Suara Digital')]
class VotingBooth extends Component
{
    public ?int $selectedCandidatePairId = null;

    public bool $showConfirmModal = false;

    public bool $showDetailModal = false;

    public ?int $detailCandidatePairId = null;

    public function mount()
    {
        $this->ensureEligibleToVote();
    }

    protected function ensureEligibleToVote(): void
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->isVoter()) {
            abort(403, 'Akses ditolak.');
        }

        $voterAccount = $user->voterAccount;
        $eligibleVoter = $voterAccount?->eligibleVoter;

        if (! $voterAccount || ! $eligibleVoter || ! $eligibleVoter->is_eligible) {
            session()->flash('error', 'Akun Anda tidak berhak untuk memberikan suara.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $election = Election::current();

        if (! $election || ! $election->isVotingActive()) {
            session()->flash('warning', 'Bilik suara saat ini sedang tidak dibuka.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $alreadyVoted = VotingParticipation::where('election_id', $election->id)
            ->where('voter_account_id', $voterAccount->id)
            ->exists();

        if ($alreadyVoted) {
            session()->flash('info', 'Anda sudah memberikan suara pada pemilihan ini.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $activeCandidatesCount = CandidatePair::where('election_id', $election->id)
            ->where('is_active', true)
            ->count();

        if ($activeCandidatesCount === 0) {
            session()->flash('warning', 'Belum ada pasangan calon aktif pada pemilihan ini.');
            $this->redirectRoute('voter.dashboard');
        }
    }

    public function selectCandidate(int $candidatePairId): void
    {
        $election = Election::current();

        if (! $election || ! $election->isVotingActive()) {
            session()->flash('error', 'Masa pemungutan suara telah berakhir.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $candidate = CandidatePair::where('id', $candidatePairId)
            ->where('election_id', $election->id)
            ->where('is_active', true)
            ->first();

        if (! $candidate) {
            session()->flash('error', 'Pasangan calon yang dipilih tidak valid atau sudah dinonaktifkan.');
            $this->selectedCandidatePairId = null;

            return;
        }

        $this->selectedCandidatePairId = $candidate->id;
    }

    public function openConfirmModal(): void
    {
        if (! $this->selectedCandidatePairId) {
            session()->flash('warning', 'Silakan tentukan pilihan pasangan calon terlebih dahulu.');

            return;
        }

        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
    }

    public function openDetailModal(int $candidatePairId): void
    {
        $this->detailCandidatePairId = $candidatePairId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailCandidatePairId = null;
    }

    public function submitVote()
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->isVoter()) {
            abort(403, 'Akses ditolak.');
        }

        $voterAccount = $user->voterAccount;
        $eligibleVoter = $voterAccount?->eligibleVoter;

        if (! $voterAccount || ! $eligibleVoter || ! $eligibleVoter->is_eligible) {
            session()->flash('error', 'Akun Anda tidak memenuhi syarat untuk memilih.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $election = Election::current();

        if (! $election || ! $election->isVotingActive()) {
            session()->flash('error', 'Waktu pemungutan suara telah selesai atau pemilihan tidak aktif.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $alreadyVoted = VotingParticipation::where('election_id', $election->id)
            ->where('voter_account_id', $voterAccount->id)
            ->exists();

        if ($alreadyVoted) {
            session()->flash('warning', 'Suara Anda sudah tercatat sebelumnya. Pemilihan hanya dapat dilakukan 1 kali.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        if (! $this->selectedCandidatePairId) {
            session()->flash('error', 'Pilihan pasangan calon belum dipilih.');
            $this->showConfirmModal = false;

            return;
        }

        $candidate = CandidatePair::where('id', $this->selectedCandidatePairId)
            ->where('election_id', $election->id)
            ->where('is_active', true)
            ->first();

        if (! $candidate) {
            session()->flash('error', 'Pasangan calon yang dipilih tidak valid atau telah dinonaktifkan.');
            $this->selectedCandidatePairId = null;
            $this->showConfirmModal = false;

            return;
        }

        try {
            DB::transaction(function () use ($election, $voterAccount, $candidate, $user) {
                // Record Participation (Voter identity tied to Election, but NOT to Candidate)
                $participation = VotingParticipation::create([
                    'election_id' => $election->id,
                    'voter_account_id' => $voterAccount->id,
                    'voted_at' => now(),
                ]);

                // Record Anonymous Ballot (Tied to Election & Candidate, but ZERO voter identity)
                DB::table('ballots')->insert([
                    'id' => (string) Str::uuid(),
                    'election_id' => $election->id,
                    'candidate_pair_id' => $candidate->id,
                ]);

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'voting',
                    'entity_type' => 'VotingParticipation',
                    'entity_id' => (string) $participation->id,
                    'description' => 'Pemilih menggunakan hak suara pada pemilihan: '.$election->name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });
        } catch (UniqueConstraintViolationException $e) {
            session()->flash('warning', 'Suara Anda sudah tercatat sebelumnya.');
            $this->redirectRoute('voter.dashboard');

            return;
        } catch (\Throwable $e) {
            session()->flash('error', 'Terjadi kesalahan saat memproses suara Anda. Silakan coba kembali.');

            return;
        }

        session()->flash('success', 'Suara Anda berhasil dicatat secara anonim!');
        $this->redirectRoute('voter.success');
    }

    public function render()
    {
        $election = Election::current();

        $candidates = collect();
        if ($election) {
            $candidates = CandidatePair::where('election_id', $election->id)
                ->where('is_active', true)
                ->orderBy('candidate_number', 'asc')
                ->with([
                    'leaderMember.eligibleVoter.studyProgram',
                    'viceLeaderMember.eligibleVoter.studyProgram',
                    'candidateMissions',
                ])
                ->get();
        }

        $selectedCandidate = null;
        if ($this->selectedCandidatePairId) {
            $selectedCandidate = $candidates->firstWhere('id', $this->selectedCandidatePairId);
        }

        $detailCandidate = null;
        if ($this->detailCandidatePairId) {
            $detailCandidate = $candidates->firstWhere('id', $this->detailCandidatePairId);
        }

        return view('livewire.voter.voting-booth', [
            'election' => $election,
            'candidates' => $candidates,
            'selectedCandidate' => $selectedCandidate,
            'detailCandidate' => $detailCandidate,
        ]);
    }
}
