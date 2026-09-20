<?php

namespace App\Notifications;

use App\Models\Election;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class VotingStartingSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Election $election,
        public string $voterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $votingStart = $this->election->voting_start_at;
        $votingDate = $votingStart->format('d/m/Y');
        $votingStartTime = $votingStart->format('H:i').' WITA';
        $votingUrl = $this->getVotingUrl();

        return (new MailMessage)
            ->subject("{$this->election->name} — Voting Akan Dimulai 3 Jam Lagi")
            ->view('emails.voting-starting-soon', [
                'election' => $this->election,
                'voterName' => $this->voterName,
                'votingDate' => $votingDate,
                'votingStartTime' => $votingStartTime,
                'votingUrl' => $votingUrl,
            ])
            ->text('emails.voting-starting-soon-text', [
                'election' => $this->election,
                'voterName' => $this->voterName,
                'votingDate' => $votingDate,
                'votingStartTime' => $votingStartTime,
                'votingUrl' => $votingUrl,
            ]);
    }

    protected function getVotingUrl(): string
    {
        if (Route::has('voter.vote')) {
            return route('voter.vote');
        }

        if (Route::has('vote')) {
            return route('vote');
        }

        return route('home');
    }
}
