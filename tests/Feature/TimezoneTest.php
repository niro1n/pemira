<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\User;
use App\Notifications\VotingEndingSoonNotification;
use App\Notifications\VotingStartingSoonNotification;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_timezone_is_configured_to_asia_makassar(): void
    {
        $this->assertSame('Asia/Makassar', config('app.timezone'));
        $this->assertSame('Asia/Makassar', date_default_timezone_get());
    }

    public function test_now_and_carbon_use_asia_makassar_with_utc_plus_eight_offset(): void
    {
        $now = now();
        $this->assertSame('Asia/Makassar', $now->timezoneName);
        $this->assertSame('+08:00', $now->format('P'));

        $carbonNow = Carbon::now();
        $this->assertSame('Asia/Makassar', $carbonNow->timezoneName);
        $this->assertSame('+08:00', $carbonNow->format('P'));

        $carbonImmutable = CarbonImmutable::now();
        $this->assertSame('Asia/Makassar', $carbonImmutable->timezoneName);
        $this->assertSame('+08:00', $carbonImmutable->format('P'));
    }

    public function test_audit_log_records_and_formats_timestamp_in_wita(): void
    {
        $log = AuditLog::create([
            'action' => 'login',
            'description' => 'User logged in successfully',
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertSame('Asia/Makassar', $log->created_at->timezoneName);
        $this->assertSame('+08:00', $log->created_at->format('P'));
        $this->assertSame(now()->format('d/m/Y H:i'), $log->created_at->format('d/m/Y H:i'));
        $this->assertSame(now()->format('H:i'), $log->created_at->format('H:i'));
    }

    public function test_election_dates_and_contextual_labels_use_wita(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Test',
            'slug' => 'pemira-test',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-09-21 08:00:00'),
            'registration_end_at' => Carbon::parse('2026-09-22 17:00:00'),
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $this->assertSame('Asia/Makassar', $election->voting_start_at->timezoneName);
        $this->assertSame('25/09/2026 08:00', $election->voting_start_at->format('d/m/Y H:i'));
        $this->assertSame('25/09/2026 16:00', $election->voting_end_at->format('d/m/Y H:i'));

        $refUpcoming = Carbon::parse('2026-09-20 12:00:00');
        $this->assertSame('Dimulai 21/09/2026 08:00 WITA', $election->contextualDateLabel($refUpcoming));

        $refRegistration = Carbon::parse('2026-09-21 10:00:00');
        $this->assertSame('Voting Dimulai 25/09/2026 08:00 WITA', $election->contextualDateLabel($refRegistration));

        $refVoting = Carbon::parse('2026-09-25 10:00:00');
        $this->assertSame('Berakhir 25/09/2026 16:00 WITA', $election->contextualDateLabel($refVoting));

        $refFinished = Carbon::parse('2026-09-26 10:00:00');
        $this->assertSame('Telah Berakhir 25/09/2026 16:00 WITA', $election->contextualDateLabel($refFinished));
    }

    public function test_target_timestamp_ms_returns_accurate_epoch_milliseconds(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Test Countdown',
            'slug' => 'pemira-test-countdown',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-09-21 08:00:00'),
            'registration_end_at' => Carbon::parse('2026-09-22 17:00:00'),
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $ref = Carbon::parse('2026-09-20 12:00:00');
        $targetMs = $election->targetTimestampMs($ref);
        $expectedMs = Carbon::parse('2026-09-21 08:00:00')->getTimestamp() * 1000;

        $this->assertSame($expectedMs, $targetMs);
    }

    public function test_email_notifications_display_wita_time(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Notification Test',
            'slug' => 'pemira-notification-test',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-09-21 08:00:00'),
            'registration_end_at' => Carbon::parse('2026-09-22 17:00:00'),
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $user = User::factory()->create();

        $startNotification = new VotingStartingSoonNotification($election, 'I Putu Gede');
        $startMail = $startNotification->toMail($user);
        $startHtml = $startMail->render();

        $this->assertStringContainsString('08:00 WITA', $startHtml);
        $this->assertStringContainsString('25/09/2026', $startHtml);

        $endNotification = new VotingEndingSoonNotification($election, 'I Putu Gede');
        $endMail = $endNotification->toMail($user);
        $endHtml = $endMail->render();

        $this->assertStringContainsString('25/09/2026 16:00 WITA', $endHtml);
    }
}
