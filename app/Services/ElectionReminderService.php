<?php

namespace App\Services;

use App\Enums\ElectionNotificationType;
use App\Models\Election;
use App\Models\ElectionEmailNotification;
use App\Models\User;
use App\Notifications\VotingEndingSoonNotification;
use App\Notifications\VotingStartingSoonNotification;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionReminderService
{
    public function sendReminders(?CarbonInterface $referenceTime = null): array
    {
        $now = $referenceTime ? Carbon::instance($referenceTime) : Carbon::now();

        $elections = Election::query()
            ->whereNotNull('voting_start_at')
            ->whereNotNull('voting_end_at')
            ->where('voting_start_at', '<=', $now->copy()->addHours(4))
            ->where('voting_end_at', '>', $now)
            ->get();

        $results = [];

        foreach ($elections as $election) {
            $startSent = 0;
            $endSent = 0;

            if ($this->isStartReminderWindow($election, $now)) {
                $startSent = $this->sendStartReminders($election, $now);
            }

            if ($this->isEndReminderWindow($election, $now)) {
                $endSent = $this->sendEndReminders($election, $now);
            }

            if ($startSent > 0 || $endSent > 0 || $this->isStartReminderWindow($election, $now) || $this->isEndReminderWindow($election, $now)) {
                $results[$election->id] = [
                    'election_id' => $election->id,
                    'election_name' => $election->name,
                    'start_reminders_sent' => $startSent,
                    'end_reminders_sent' => $endSent,
                ];
            }
        }

        return $results;
    }

    public function isStartReminderWindow(Election $election, CarbonInterface $now): bool
    {
        if (! $election->voting_start_at) {
            return false;
        }

        $startThreshold = $election->voting_start_at->copy()->subHours(3);

        return $now->greaterThanOrEqualTo($startThreshold)
            && $now->lessThan($election->voting_start_at);
    }

    public function isEndReminderWindow(Election $election, CarbonInterface $now): bool
    {
        if (! $election->voting_start_at || ! $election->voting_end_at) {
            return false;
        }

        $endThreshold = $election->voting_end_at->copy()->subHour();

        return $now->greaterThanOrEqualTo($election->voting_start_at)
            && $now->greaterThanOrEqualTo($endThreshold)
            && $now->lessThan($election->voting_end_at);
    }

    public function sendStartReminders(Election $election, CarbonInterface $now): int
    {
        if (! $this->isStartReminderWindow($election, $now)) {
            return 0;
        }

        $sentCount = 0;

        User::query()
            ->where('role', 'voter')
            ->whereNotNull('email_verified_at')
            ->whereHas('voterAccount.eligibleVoter', function ($q) {
                $q->where('is_eligible', true);
            })
            ->whereDoesntHave('electionEmailNotifications', function ($q) use ($election) {
                $q->where('election_id', $election->id)
                    ->where('type', ElectionNotificationType::VOTING_START_REMINDER->value);
            })
            ->with('voterAccount.eligibleVoter')
            ->chunkById(200, function ($voters) use ($election, $now, &$sentCount) {
                foreach ($voters as $voter) {
                    if ($this->dispatchNotification($voter, $election, ElectionNotificationType::VOTING_START_REMINDER, $now)) {
                        $sentCount++;
                    }
                }
            });

        return $sentCount;
    }

    public function sendEndReminders(Election $election, CarbonInterface $now): int
    {
        if (! $this->isEndReminderWindow($election, $now)) {
            return 0;
        }

        $sentCount = 0;

        User::query()
            ->where('role', 'voter')
            ->whereNotNull('email_verified_at')
            ->whereHas('voterAccount', function ($q) use ($election) {
                $q->whereHas('eligibleVoter', function ($sub) {
                    $sub->where('is_eligible', true);
                })
                    ->whereDoesntHave('votingParticipations', function ($sub) use ($election) {
                        $sub->where('election_id', $election->id);
                    });
            })
            ->whereDoesntHave('electionEmailNotifications', function ($q) use ($election) {
                $q->where('election_id', $election->id)
                    ->where('type', ElectionNotificationType::VOTING_END_REMINDER->value);
            })
            ->with('voterAccount.eligibleVoter')
            ->chunkById(200, function ($voters) use ($election, $now, &$sentCount) {
                foreach ($voters as $voter) {
                    if ($this->dispatchNotification($voter, $election, ElectionNotificationType::VOTING_END_REMINDER, $now)) {
                        $sentCount++;
                    }
                }
            });

        return $sentCount;
    }

    protected function dispatchNotification(
        User $voter,
        Election $election,
        ElectionNotificationType $type,
        CarbonInterface $now
    ): bool {
        DB::beginTransaction();

        try {
            ElectionEmailNotification::create([
                'election_id' => $election->id,
                'user_id' => $voter->id,
                'type' => $type,
                'sent_at' => $now,
            ]);

            $voterName = $voter->voterAccount?->eligibleVoter?->name ?? 'Pemilih';

            if ($type === ElectionNotificationType::VOTING_START_REMINDER) {
                $voter->notify(new VotingStartingSoonNotification($election, $voterName));
            } else {
                $voter->notify(new VotingEndingSoonNotification($election, $voterName));
            }

            DB::commit();

            return true;
        } catch (UniqueConstraintViolationException) {
            DB::rollBack();

            return false;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("Gagal mengirim notifikasi email pemira: {$e->getMessage()}", [
                'election_id' => $election->id,
                'user_id' => $voter->id,
                'type' => $type->value,
            ]);

            return false;
        }
    }
}
