<?php

namespace Tests\Feature;

use App\Enums\ElectionPhase;
use App\Models\Election;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_without_errors_when_no_elections_exist(): void
    {
        $this->assertEquals(0, Election::count());

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Jadwal pemilihan belum tersedia.');
        $response->assertSee('PEMIRA');
    }

    public function test_landing_page_renders_upcoming_election_data(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(5),
            'registration_end_at' => $now->copy()->addDays(10),
            'voting_start_at' => $now->copy()->addDays(15),
            'voting_end_at' => $now->copy()->addDays(16),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PEMIRA BEM PNB 2026');
        $response->assertSee('PEMILIHAN AKAN DATANG');
        $response->assertSee('PENDAFTARAN DIMULAI DALAM');
        $response->assertSee($election->registration_start_at->translatedFormat('d F Y'));

        Carbon::setTestNow();
    }

    public function test_landing_page_renders_registration_election_data(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA Tahap Pendaftaran',
            'slug' => 'pemira-tahap-pendaftaran-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(2),
            'registration_end_at' => $now->copy()->addDays(4),
            'voting_start_at' => $now->copy()->addDays(8),
            'voting_end_at' => $now->copy()->addDays(9),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PEMIRA Tahap Pendaftaran');
        $response->assertSee('PENDAFTARAN SEDANG BERLANGSUNG');
        $response->assertSee('PEMUNGUTAN SUARA DIMULAI DALAM');

        Carbon::setTestNow();
    }

    public function test_landing_page_renders_voting_election_data(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA Sedang Voting',
            'slug' => 'pemira-sedang-voting-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(6),
            'registration_end_at' => $now->copy()->subDays(2),
            'voting_start_at' => $now->copy()->subHours(3),
            'voting_end_at' => $now->copy()->addHours(5),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PEMIRA Sedang Voting');
        $response->assertSee('VOTING SEDANG BERLANGSUNG');
        $response->assertSee('PEMUNGUTAN SUARA BERAKHIR DALAM');

        Carbon::setTestNow();
    }

    public function test_landing_page_renders_finished_election_when_only_finished_available(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA PNB 2025 Selesai',
            'slug' => 'pemira-pnb-2025-selesai',
            'year' => 2025,
            'registration_start_at' => $now->copy()->subDays(40),
            'registration_end_at' => $now->copy()->subDays(35),
            'voting_start_at' => $now->copy()->subDays(30),
            'voting_end_at' => $now->copy()->subDays(29),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PEMIRA PNB 2025 Selesai');
        $response->assertSee('PEMILIHAN TELAH SELESAI');

        Carbon::setTestNow();
    }

    public function test_election_current_context_prioritizes_voting_over_others(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $upcoming = Election::create([
            'name' => 'PEMIRA Upcoming 2027',
            'slug' => 'pemira-upcoming-2027',
            'year' => 2027,
            'registration_start_at' => $now->copy()->addDays(30),
            'registration_end_at' => $now->copy()->addDays(35),
            'voting_start_at' => $now->copy()->addDays(40),
            'voting_end_at' => $now->copy()->addDays(41),
        ]);

        $voting = Election::create([
            'name' => 'PEMIRA Active Voting 2026',
            'slug' => 'pemira-active-voting-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(2),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(4),
        ]);

        $current = Election::current($now);
        $this->assertEquals($voting->id, $current->id);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('PEMIRA Active Voting 2026');

        Carbon::setTestNow();
    }

    public function test_election_countdown_target_and_timestamp_accuracy(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');

        $election = new Election([
            'name' => 'PEMIRA Countdown Test',
            'slug' => 'pemira-countdown-test',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-09-20 08:00:00'),
            'registration_end_at' => Carbon::parse('2026-09-22 16:00:00'),
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $this->assertEquals(ElectionPhase::UPCOMING, $election->currentPhase($now));
        $this->assertEquals('2026-09-20 08:00:00', $election->countdownTarget($now)->format('Y-m-d H:i:s'));
        $this->assertEquals(Carbon::parse('2026-09-20 08:00:00')->timestamp * 1000, $election->targetTimestampMs($now));

        $regTime = Carbon::parse('2026-09-21 10:00:00');
        $this->assertEquals(ElectionPhase::REGISTRATION, $election->currentPhase($regTime));
        $this->assertEquals('2026-09-25 08:00:00', $election->countdownTarget($regTime)->format('Y-m-d H:i:s'));

        $votingTime = Carbon::parse('2026-09-25 10:00:00');
        $this->assertEquals(ElectionPhase::VOTING, $election->currentPhase($votingTime));
        $this->assertEquals('2026-09-25 16:00:00', $election->countdownTarget($votingTime)->format('Y-m-d H:i:s'));

        $finishedTime = Carbon::parse('2026-09-25 18:00:00');
        $this->assertEquals(ElectionPhase::FINISHED, $election->currentPhase($finishedTime));
        $this->assertNull($election->countdownTarget($finishedTime));
        $this->assertNull($election->targetTimestampMs($finishedTime));
    }
}
