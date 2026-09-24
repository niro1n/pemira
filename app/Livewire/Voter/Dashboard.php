<?php

namespace App\Livewire\Voter;

use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\User;
use App\Models\VotingParticipation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Dashboard Pemilih')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $user->loadMissing('voterAccount.eligibleVoter.studyProgram');
        }

        $voterAccount = $user?->voterAccount;
        $eligibleVoter = $voterAccount?->eligibleVoter;
        $studyProgram = $eligibleVoter?->studyProgram;

        $hasVoterAccount = $voterAccount !== null && $eligibleVoter !== null;

        $election = Election::current();
        $currentPhase = $election?->currentPhase();

        $hasVoted = false;
        $votingParticipation = null;

        if ($election && $voterAccount) {
            $votingParticipation = VotingParticipation::where('election_id', $election->id)
                ->where('voter_account_id', $voterAccount->id)
                ->first();

            $hasVoted = $votingParticipation !== null;
        }

        $activeCandidatesCount = 0;
        if ($election) {
            $activeCandidatesCount = CandidatePair::where('election_id', $election->id)
                ->where('is_active', true)
                ->count();
        }

        $isEligible = (bool) ($eligibleVoter?->is_eligible ?? false);

        return view('livewire.voter.dashboard', [
            'user' => $user,
            'voterAccount' => $voterAccount,
            'eligibleVoter' => $eligibleVoter,
            'studyProgram' => $studyProgram,
            'hasVoterAccount' => $hasVoterAccount,
            'election' => $election,
            'currentPhase' => $currentPhase,
            'hasVoted' => $hasVoted,
            'votingParticipation' => $votingParticipation,
            'activeCandidatesCount' => $activeCandidatesCount,
            'isEligible' => $isEligible,
        ]);
    }
}
