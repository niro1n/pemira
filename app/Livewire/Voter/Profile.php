<?php

namespace App\Livewire\Voter;

use App\Models\User;
use App\Models\VotingParticipation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Profil Pemilih DPT')]
class Profile extends Component
{
    public function render()
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->isVoter()) {
            abort(403, 'Akses ditolak.');
        }

        $voterAccount = $user->voterAccount;
        $eligibleVoter = $voterAccount?->eligibleVoter;
        $studyProgram = $eligibleVoter?->studyProgram;

        $participations = collect();
        if ($voterAccount) {
            $participations = VotingParticipation::where('voter_account_id', $voterAccount->id)
                ->with('election')
                ->orderBy('voted_at', 'desc')
                ->get();
        }

        return view('livewire.voter.profile', [
            'user' => $user,
            'voterAccount' => $voterAccount,
            'eligibleVoter' => $eligibleVoter,
            'studyProgram' => $studyProgram,
            'participations' => $participations,
        ]);
    }
}
