<?php

namespace Tests\Feature\Notification;

use App\Enums\ElectionNotificationType;
use App\Models\Election;
use App\Models\ElectionEmailNotification;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use App\Models\VotingParticipation;
use App\Notifications\VotingEndingSoonNotification;
use App\Notifications\VotingStartingSoonNotification;
use App\Services\ElectionReminderService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ElectionReminderTest extends TestCase
{
    use RefreshDatabase;

    private StudyProgram $studyProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);
    }

    private function createElection(array $attributes = []): Election
    {
        return Election::create(array_merge([
            'name' => 'PEMIRA 2026',
            'slug' => 'pemira-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-09-10 08:00:00'),
            'registration_end_at' => Carbon::parse('2026-09-20 16:00:00'),
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ], $attributes));
    }

    private function createVoterUser(string $email, bool $verified = true, bool $eligible = true): User
    {
        $eligibleVoter = EligibleVoter::create([
            'nim' => (string) fake()->unique()->numerify('##########'),
            'name' => 'Voter '.fake()->firstName(),
            'date_of_birth' => '2004-01-01',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => $eligible,
        ]);

        $user = User::factory()->create([
            'email' => $email,
            'role' => 'voter',
            'email_verified_at' => $verified ? Carbon::parse('2026-09-15 10:00:00') : null,
        ]);

        VoterAccount::create([
            'user_id' => $user->id,
            'eligible_voter_id' => $eligibleVoter->id,
        ]);

        return $user;
    }

    public function test_voter_with_verified_email_receives_start_reminder(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $voter = $this->createVoterUser('verified_voter@example.com', verified: true);

        $now = Carbon::parse('2026-09-25 05:00:00');
        $service = app(ElectionReminderService::class);
        $results = $service->sendReminders($now);

        $this->assertArrayHasKey($election->id, $results);
        $this->assertEquals(1, $results[$election->id]['start_reminders_sent']);

        Notification::assertSentTo($voter, VotingStartingSoonNotification::class, function ($notification) use ($election) {
            return $notification->election->id === $election->id;
        });

        $this->assertDatabaseHas('election_email_notifications', [
            'election_id' => $election->id,
            'user_id' => $voter->id,
            'type' => ElectionNotificationType::VOTING_START_REMINDER->value,
        ]);
    }

    public function test_voter_with_unverified_email_does_not_receive_reminder(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $unverifiedVoter = $this->createVoterUser('unverified@example.com', verified: false);

        $now = Carbon::parse('2026-09-25 05:00:00');
        $service = app(ElectionReminderService::class);
        $service->sendReminders($now);

        Notification::assertNotSentTo($unverifiedVoter, VotingStartingSoonNotification::class);
        Notification::assertNotSentTo($unverifiedVoter, VotingEndingSoonNotification::class);

        $this->assertDatabaseMissing('election_email_notifications', [
            'user_id' => $unverifiedVoter->id,
        ]);
    }

    public function test_admin_does_not_receive_reminder(): void
    {
        Notification::fake();

        $election = $this->createElection();

        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $now = Carbon::parse('2026-09-25 05:00:00');
        $service = app(ElectionReminderService::class);
        $service->sendReminders($now);

        Notification::assertNotSentTo($admin, VotingStartingSoonNotification::class);
        Notification::assertNotSentTo($admin, VotingEndingSoonNotification::class);
    }

    public function test_super_admin_does_not_receive_reminder(): void
    {
        Notification::fake();

        $election = $this->createElection();

        $superAdmin = User::factory()->superAdmin()->create([
            'email' => 'superadmin@example.com',
            'email_verified_at' => now(),
        ]);

        $now = Carbon::parse('2026-09-25 05:00:00');
        $service = app(ElectionReminderService::class);
        $service->sendReminders($now);

        Notification::assertNotSentTo($superAdmin, VotingStartingSoonNotification::class);
        Notification::assertNotSentTo($superAdmin, VotingEndingSoonNotification::class);
    }

    public function test_voter_who_already_voted_does_not_receive_ending_reminder(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $voter = $this->createVoterUser('voted_voter@example.com', verified: true);

        VotingParticipation::create([
            'election_id' => $election->id,
            'voter_account_id' => $voter->voterAccount->id,
            'voted_at' => Carbon::parse('2026-09-25 10:00:00'),
        ]);

        $now = Carbon::parse('2026-09-25 15:00:00');
        $service = app(ElectionReminderService::class);
        $results = $service->sendReminders($now);

        $this->assertEquals(0, $results[$election->id]['end_reminders_sent'] ?? 0);
        Notification::assertNotSentTo($voter, VotingEndingSoonNotification::class);
    }

    public function test_voter_who_has_not_voted_receives_ending_reminder(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $voter = $this->createVoterUser('not_voted@example.com', verified: true);

        $now = Carbon::parse('2026-09-25 15:00:00');
        $service = app(ElectionReminderService::class);
        $results = $service->sendReminders($now);

        $this->assertArrayHasKey($election->id, $results);
        $this->assertEquals(1, $results[$election->id]['end_reminders_sent']);

        Notification::assertSentTo($voter, VotingEndingSoonNotification::class, function ($notification) use ($election) {
            return $notification->election->id === $election->id;
        });

        $this->assertDatabaseHas('election_email_notifications', [
            'election_id' => $election->id,
            'user_id' => $voter->id,
            'type' => ElectionNotificationType::VOTING_END_REMINDER->value,
        ]);
    }

    public function test_start_reminder_only_sent_during_appropriate_window(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $voter = $this->createVoterUser('window_voter@example.com', verified: true);
        $service = app(ElectionReminderService::class);

        $tooEarly = Carbon::parse('2026-09-25 04:59:59');
        $service->sendReminders($tooEarly);
        Notification::assertNotSentTo($voter, VotingStartingSoonNotification::class);

        $justInTime = Carbon::parse('2026-09-25 05:00:00');
        $service->sendReminders($justInTime);
        Notification::assertSentTo($voter, VotingStartingSoonNotification::class);

        $tooLate = Carbon::parse('2026-09-25 08:05:00');
        $this->assertFalse($service->isStartReminderWindow($election, $tooLate));
    }

    public function test_ending_reminder_only_sent_during_appropriate_window(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $voter = $this->createVoterUser('window_end_voter@example.com', verified: true);
        $service = app(ElectionReminderService::class);

        $tooEarly = Carbon::parse('2026-09-25 14:59:59');
        $service->sendReminders($tooEarly);
        Notification::assertNotSentTo($voter, VotingEndingSoonNotification::class);

        $justInTime = Carbon::parse('2026-09-25 15:00:00');
        $service->sendReminders($justInTime);
        Notification::assertSentTo($voter, VotingEndingSoonNotification::class);

        $tooLate = Carbon::parse('2026-09-25 16:05:00');
        $this->assertFalse($service->isEndReminderWindow($election, $tooLate));
    }

    public function test_reminder_not_sent_twice_to_same_voter_and_election(): void
    {
        Notification::fake();

        $election = $this->createElection();
        $voter = $this->createVoterUser('idempotent_voter@example.com', verified: true);
        $service = app(ElectionReminderService::class);

        $now = Carbon::parse('2026-09-25 05:00:00');

        $firstRun = $service->sendReminders($now);
        $this->assertEquals(1, $firstRun[$election->id]['start_reminders_sent']);

        $secondRun = $service->sendReminders($now->copy()->addMinutes(2));
        $this->assertEquals(0, $secondRun[$election->id]['start_reminders_sent']);

        $this->assertEquals(1, ElectionEmailNotification::where('user_id', $voter->id)
            ->where('election_id', $election->id)
            ->where('type', ElectionNotificationType::VOTING_START_REMINDER)
            ->count());
    }

    public function test_reminder_from_election_a_does_not_affect_election_b(): void
    {
        Notification::fake();

        $electionA = $this->createElection([
            'name' => 'PEMIRA 2026 Jurusan A',
            'slug' => 'pemira-2026-jurusan-a',
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $electionB = $this->createElection([
            'name' => 'PEMIRA 2026 Jurusan B',
            'slug' => 'pemira-2026-jurusan-b',
            'voting_start_at' => Carbon::parse('2026-09-25 12:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 18:00:00'),
        ]);

        $voter = $this->createVoterUser('multi_voter@example.com', verified: true);
        $service = app(ElectionReminderService::class);

        $nowA = Carbon::parse('2026-09-25 05:00:00');
        $service->sendReminders($nowA);

        $this->assertDatabaseHas('election_email_notifications', [
            'election_id' => $electionA->id,
            'user_id' => $voter->id,
            'type' => ElectionNotificationType::VOTING_START_REMINDER->value,
        ]);

        $this->assertDatabaseMissing('election_email_notifications', [
            'election_id' => $electionB->id,
            'user_id' => $voter->id,
        ]);

        $nowB = Carbon::parse('2026-09-25 09:00:00');
        $service->sendReminders($nowB);

        $this->assertDatabaseHas('election_email_notifications', [
            'election_id' => $electionB->id,
            'user_id' => $voter->id,
            'type' => ElectionNotificationType::VOTING_START_REMINDER->value,
        ]);
    }

    public function test_scheduler_safe_when_executed_multiple_times(): void
    {
        $election = $this->createElection();
        $this->createVoterUser('safe_voter@example.com', verified: true);

        for ($i = 0; $i < 4; $i++) {
            $this->artisan('pemira:send-election-reminders', [
                '--now' => '2026-09-25 05:01:00',
            ])->assertSuccessful();
        }

        $this->assertEquals(1, ElectionEmailNotification::where('election_id', $election->id)->count());
    }

    public function test_emails_do_not_contain_candidate_or_ballot_information(): void
    {
        $election = $this->createElection();
        $voter = $this->createVoterUser('privacy_voter@example.com', verified: true);

        $startNotification = new VotingStartingSoonNotification($election, $voter->voterAccount->eligibleVoter->name);
        $startMail = $startNotification->toMail($voter);
        $startHtml = $startMail->render();

        $this->assertStringNotContainsStringIgnoringCase('ballot', $startHtml);
        $this->assertStringNotContainsStringIgnoringCase('kandidat', $startHtml);
        $this->assertStringNotContainsStringIgnoringCase('paslon', $startHtml);

        $endNotification = new VotingEndingSoonNotification($election, $voter->voterAccount->eligibleVoter->name);
        $endMail = $endNotification->toMail($voter);
        $endHtml = $endMail->render();

        $this->assertStringNotContainsStringIgnoringCase('ballot', $endHtml);
        $this->assertStringNotContainsStringIgnoringCase('kandidat', $endHtml);
        $this->assertStringNotContainsStringIgnoringCase('paslon', $endHtml);
        $this->assertStringContainsString('Anda belum menggunakan hak suara pada PEMIRA ini.', $endHtml);
    }

    public function test_emails_contain_correct_election_information(): void
    {
        $election = $this->createElection([
            'name' => 'PEMIRA Mahasiswa 2026',
        ]);
        $voter = $this->createVoterUser('info_voter@example.com', verified: true);
        $voterName = $voter->voterAccount->eligibleVoter->name;

        $startNotification = new VotingStartingSoonNotification($election, $voterName);
        $startMail = $startNotification->toMail($voter);
        $startHtml = $startMail->render();

        $this->assertEquals('PEMIRA Mahasiswa 2026 — Voting Akan Dimulai 3 Jam Lagi', $startMail->subject);
        $this->assertStringContainsString($voterName, $startHtml);
        $this->assertStringContainsString('PEMIRA Mahasiswa 2026', $startHtml);
        $this->assertStringContainsString('25/09/2026', $startHtml);
        $this->assertStringContainsString('08:00 WITA', $startHtml);

        $endNotification = new VotingEndingSoonNotification($election, $voterName);
        $endMail = $endNotification->toMail($voter);
        $endHtml = $endMail->render();

        $this->assertEquals('PEMIRA Mahasiswa 2026 — Voting Berakhir 1 Jam Lagi', $endMail->subject);
        $this->assertStringContainsString($voterName, $endHtml);
        $this->assertStringContainsString('PEMIRA Mahasiswa 2026', $endHtml);
        $this->assertStringContainsString('16:00 WITA', $endHtml);
    }

    public function test_timezone_calculation_is_accurate(): void
    {
        $election = $this->createElection([
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $service = app(ElectionReminderService::class);

        $startRef = Carbon::parse('2026-09-25 05:00:00');
        $this->assertTrue($service->isStartReminderWindow($election, $startRef));

        $endRef = Carbon::parse('2026-09-25 15:00:00');
        $this->assertTrue($service->isEndReminderWindow($election, $endRef));

        $notification = new VotingStartingSoonNotification($election, 'Test Voter');
        $mail = $notification->toMail(new User);
        $html = $mail->render();

        $this->assertStringContainsString('WITA', $html);
    }

    public function test_notifications_implement_should_queue(): void
    {
        $election = $this->createElection();
        $start = new VotingStartingSoonNotification($election, 'Voter');
        $end = new VotingEndingSoonNotification($election, 'Voter');

        $this->assertInstanceOf(ShouldQueue::class, $start);
        $this->assertInstanceOf(ShouldQueue::class, $end);
    }
}
