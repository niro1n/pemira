<?php

namespace App\Notifications;

use App\Models\Election;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class VotingEndingSoonNotification extends Notification implements ShouldQueue
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
        $votingEnd = $this->election->voting_end_at;
        $votingEndTime = $votingEnd->format('d/m/Y H:i').' WITA';
        $votingUrl = $this->getVotingUrl();

        return (new MailMessage)
            ->subject("{$this->election->name} — Voting Berakhir 1 Jam Lagi")
            ->view('emails.voting-ending-soon', [
                'election' => $this->election,
                'voterName' => $this->voterName,
                'votingEndTime' => $votingEndTime,
                'votingUrl' => $votingUrl,
            ])
            ->text('emails.voting-ending-soon-text', [
                'election' => $this->election,
                'voterName' => $this->voterName,
                'votingEndTime' => $votingEndTime,
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
