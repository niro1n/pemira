<?php

namespace App\Livewire\Voter;

use App\Models\Election;
use App\Models\Feedback;
use App\Models\User;
use App\Models\VotingParticipation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Bukti Partisipasi Pemilihan')]
class VotingSuccess extends Component
{
    public int $rating = 5;

    public string $comment = '';

    public bool $feedbackSubmitted = false;

    public function mount()
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->isVoter()) {
            abort(403, 'Akses ditolak.');
        }

        $voterAccount = $user->voterAccount;

        if (! $voterAccount) {
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $election = Election::current();

        if (! $election) {
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $participation = VotingParticipation::where('election_id', $election->id)
            ->where('voter_account_id', $voterAccount->id)
            ->first();

        if (! $participation) {
            session()->flash('warning', 'Anda belum memberikan hak suara pada pemilihan ini.');
            $this->redirectRoute('voter.dashboard');

            return;
        }

        $existingFeedback = Feedback::where('election_id', $election->id)
            ->where('voter_account_id', $voterAccount->id)
            ->first();

        if ($existingFeedback) {
            $this->feedbackSubmitted = true;
            $this->rating = $existingFeedback->rating;
            $this->comment = $existingFeedback->comment ?? '';
        }
    }

    public function setRating(int $val): void
    {
        if ($this->feedbackSubmitted) {
            return;
        }

        if ($val >= 1 && $val <= 5) {
            $this->rating = $val;
        }
    }

    public function submitFeedback(): void
    {
        if ($this->feedbackSubmitted) {
            return;
        }

        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $voterAccount = $user?->voterAccount;
        $election = Election::current();

        if (! $voterAccount || ! $election) {
            return;
        }

        Feedback::updateOrCreate(
            [
                'election_id' => $election->id,
                'voter_account_id' => $voterAccount->id,
            ],
            [
                'rating' => $this->rating,
                'comment' => trim($this->comment) !== '' ? trim($this->comment) : null,
            ]
        );

        $this->feedbackSubmitted = true;
        session()->flash('feedback_success', 'Terima kasih atas tanggapan dan masukan Anda untuk peningkatan sistem PEMIRA.');
    }

    public function render()
    {
        $user = Auth::user();
        $voterAccount = $user?->voterAccount;
        $eligibleVoter = $voterAccount?->eligibleVoter;
        $election = Election::current();

        $participation = null;
        if ($election && $voterAccount) {
            $participation = VotingParticipation::where('election_id', $election->id)
                ->where('voter_account_id', $voterAccount->id)
                ->first();
        }

        return view('livewire.voter.voting-success', [
            'user' => $user,
            'eligibleVoter' => $eligibleVoter,
            'election' => $election,
            'participation' => $participation,
        ]);
    }
}
