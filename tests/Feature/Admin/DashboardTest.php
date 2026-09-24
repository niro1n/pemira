<?php

namespace Tests\Feature\Admin;

use App\Enums\ElectionPhase;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\StudyProgram;
use App\Models\User;
use App\Services\Dashboard\DashboardDataProvider;
use App\Services\Dashboard\DashboardDataProviderInterface;
use Carbon\Carbon;
use Database\Seeders\EligibleVoterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private StudyProgram $studyProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknik Informatika',
            'code' => 'IF',
        ]);
    }

    public function test_voter_cannot_access_admin_dashboard(): void
    {
        $voter = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($voter)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('PEMIRA PNB');
        $response->assertSee('ADMIN KPR');
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin');

        $response->assertOk();
        $response->assertSee('PEMIRA PNB');
        $response->assertSee('SUPER ADMIN');
        $response->assertSee('OTORITAS SISTEM');
    }

    public function test_regular_admin_does_not_see_super_admin_system_authority_section(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertDontSee('OTORITAS SISTEM');
    }

    public function test_unauthenticated_guest_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_data_and_charts_sections(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('election-header', false);
        $response->assertSee('participation-metrics', false);
        $response->assertSee('participation-progress', false);
        $response->assertSee('department-participation', false);
        $response->assertSee('recent-activity', false);
        $response->assertSee('TOTAL DPT');
        $response->assertSee('AKUN TERDAFTAR');
        $response->assertSee('SUARA MASUK');
        $response->assertSee('BELUM MEMILIH');
        $response->assertSee('TINGKAT PARTISIPASI');
        $response->assertSee('PARTISIPASI JURUSAN');
        $response->assertSee('LOG SUARA MASUK TERKINI');
    }

    public function test_election_phase_calculation_from_timestamps(): void
    {
        $provider = app(DashboardDataProvider::class);

        $regStart = '2026-09-01 08:00:00';
        $regEnd = '2026-09-05 16:00:00';
        $votingStart = '2026-09-10 08:00:00';
        $votingEnd = '2026-09-10 16:00:00';

        $phase = $provider->determinePhase($regStart, $regEnd, $votingStart, $votingEnd, Carbon::parse('2026-08-30 10:00:00'));
        $this->assertEquals(ElectionPhase::UPCOMING, $phase);

        $phase = $provider->determinePhase($regStart, $regEnd, $votingStart, $votingEnd, Carbon::parse('2026-09-02 10:00:00'));
        $this->assertEquals(ElectionPhase::REGISTRATION, $phase);

        $phase = $provider->determinePhase($regStart, $regEnd, $votingStart, $votingEnd, Carbon::parse('2026-09-10 12:00:00'));
        $this->assertEquals(ElectionPhase::VOTING, $phase);

        $phase = $provider->determinePhase($regStart, $regEnd, $votingStart, $votingEnd, Carbon::parse('2026-09-10 17:00:00'));
        $this->assertEquals(ElectionPhase::FINISHED, $phase);
    }

    public function test_participation_rate_prevents_division_by_zero(): void
    {
        $provider = app(DashboardDataProvider::class);

        $stats = $provider->calculateParticipationRatios([
            'eligible' => 0,
            'registered' => 0,
            'voted' => 0,
        ]);

        $this->assertEquals(0, $stats['eligible']);
        $this->assertEquals(0, $stats['registered']);
        $this->assertEquals(0, $stats['voted']);
        $this->assertEquals(0, $stats['not_voted']);
        $this->assertEquals(0.0, $stats['rate']);
        $this->assertEquals('0,00%', $stats['rate_formatted']);
    }

    public function test_participation_statistics_calculated_accurately(): void
    {
        $provider = app(DashboardDataProvider::class);

        $stats = $provider->calculateParticipationRatios([
            'eligible' => 2000,
            'registered' => 1500,
            'voted' => 1000,
        ]);

        $this->assertEquals(2000, $stats['eligible']);
        $this->assertEquals(1500, $stats['registered']);
        $this->assertEquals(1000, $stats['voted']);
        $this->assertEquals(1000, $stats['not_voted']);
        $this->assertEquals(50.0, $stats['rate']);
        $this->assertEquals('50,00%', $stats['rate_formatted']);
        $this->assertEquals(75.0, $stats['registration_rate']);
    }

    public function test_finished_election_countdown_does_not_become_negative(): void
    {
        $provider = app(DashboardDataProvider::class);

        $now = Carbon::parse('2026-09-18 18:00:00');
        Carbon::setTestNow($now);

        $provider->setForcedElectionData([
            'registration_start_at' => '2026-09-01 08:00:00',
            'registration_end_at' => '2026-09-05 16:00:00',
            'voting_start_at' => '2026-09-18 08:00:00',
            'voting_end_at' => '2026-09-18 16:00:00',
        ]);

        $electionData = $provider->getElectionData();

        $this->assertEquals('finished', $electionData['phase']);
        $this->assertFalse($electionData['is_live']);
        $this->assertStringContainsString('Telah Berakhir', $electionData['contextual_date_label']);

        Carbon::setTestNow();
    }

    public function test_anonymous_ballot_guarantee(): void
    {
        AuditLog::create([
            'user_id' => null,
            'action' => 'vote',
            'description' => 'Surat suara tercatat secara anonim',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $provider = app(DashboardDataProvider::class);

        $activities = $provider->getRecentActivities();

        $this->assertNotEmpty($activities);

        foreach ($activities as $act) {
            if ($act['type'] === 'Suara') {
                $this->assertStringContainsString('Surat suara tercatat secara anonim', $act['description']);
                $this->assertStringNotContainsString('NIM', $act['description']);
                $this->assertStringNotContainsString('paslon', strtolower($act['description']));
                $this->assertStringNotContainsString('kandidat', strtolower($act['description']));
            }
        }
    }

    public function test_mobile_navigation_uses_local_alpine_state_without_backend_polling(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('mobileMenuOpen', false);
        $response->assertSee('x-data="{ mobileMenuOpen: false }"', false);
    }

    public function test_empty_elections_returns_null_election_data_and_dashboard_renders_empty_state(): void
    {
        $this->assertEquals(0, DB::table('elections')->count());

        $provider = app(DashboardDataProviderInterface::class);
        $this->assertNull($provider->getElectionData());

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Belum Ada Pemilihan');
        $response->assertSee('Kelola Pemilihan');
    }

    public function test_dashboard_renders_upcoming_election_data(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        Election::create([
            'name' => 'PEMIRA PNB 2026',
            'slug' => 'pemira-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(3),
            'registration_end_at' => $now->copy()->addDays(7),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('PEMIRA PNB 2026');
        $response->assertSee('Pemilihan Belum Dimulai');

        Carbon::setTestNow();
    }

    public function test_dashboard_renders_registration_election_data(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        Election::create([
            'name' => 'PEMIRA Masa Registrasi',
            'slug' => 'pemira-masa-registrasi-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(2),
            'registration_end_at' => $now->copy()->addDays(3),
            'voting_start_at' => $now->copy()->addDays(5),
            'voting_end_at' => $now->copy()->addDays(6),
        ]);

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('PEMIRA Masa Registrasi');
        $response->assertSee('Pendaftaran Sedang Berlangsung');

        Carbon::setTestNow();
    }

    public function test_voting_election_takes_context_priority_on_dashboard(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        Election::create([
            'name' => 'PEMIRA Masa Depan',
            'slug' => 'pemira-masa-depan-2027',
            'year' => 2027,
            'registration_start_at' => $now->copy()->addDays(10),
            'registration_end_at' => $now->copy()->addDays(15),
            'voting_start_at' => $now->copy()->addDays(20),
            'voting_end_at' => $now->copy()->addDays(21),
        ]);

        Election::create([
            'name' => 'PEMIRA Sedang Voting',
            'slug' => 'pemira-sedang-voting-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(2),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(6),
        ]);

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('PEMIRA Sedang Voting');
        $response->assertSee('Voting Sedang Berlangsung');

        Carbon::setTestNow();
    }

    public function test_dashboard_renders_finished_election_when_no_active_or_upcoming(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        Election::create([
            'name' => 'PEMIRA Telah Selesai',
            'slug' => 'pemira-telah-selesai-2025',
            'year' => 2025,
            'registration_start_at' => $now->copy()->subDays(30),
            'registration_end_at' => $now->copy()->subDays(25),
            'voting_start_at' => $now->copy()->subDays(20),
            'voting_end_at' => $now->copy()->subDays(19),
        ]);

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('PEMIRA Telah Selesai');
        $response->assertSee('Pemilihan Selesai');

        Carbon::setTestNow();
    }

    public function test_mock_data_provider_functions_with_forced_data_and_database_election_records(): void
    {
        $provider = app(DashboardDataProviderInterface::class);

        $provider->setForcedParticipationStats([
            'eligible' => 2450,
            'registered' => 2210,
            'voted' => 1987,
        ]);

        $participation = $provider->getParticipationStats();
        $this->assertEquals(2450, $participation['eligible']);
        $this->assertEquals(1987, $participation['voted']);

        $visualization = $provider->getParticipationVisualization();
        $this->assertNotEmpty($visualization['ratio_text']);

        $provider->setForcedProgramParticipation([
            [
                'code' => 'TI',
                'name' => 'Jurusan Teknologi Informasi',
                'short_name' => 'Teknologi Informasi',
                'eligible' => 420,
                'voted' => 365,
                'rate' => 86.9,
                'rate_formatted' => '86,90%',
            ],
        ]);

        $programs = $provider->getDepartmentParticipation();
        $this->assertIsArray($programs);
        $this->assertCount(1, $programs);
        $this->assertArrayHasKey('name', $programs[0]);
        $this->assertArrayHasKey('code', $programs[0]);
        $this->assertArrayHasKey('rate', $programs[0]);

        $provider->setForcedRecentActivities([
            [
                'time' => '12:39',
                'title' => 'SUARA DITERIMA',
                'description' => 'Surat suara tercatat secara anonim dari Jurusan Akuntansi (AK)',
                'type' => 'Suara',
                'department' => 'AK',
            ],
        ]);

        $activities = $provider->getRecentActivities();
        $this->assertIsArray($activities);
        $this->assertNotEmpty($activities);
        $this->assertNotNull($activities[0]['department'] ?? null);
    }

    public function test_empty_database_returns_zero_counts_and_empty_activities(): void
    {
        $provider = app(DashboardDataProvider::class);

        $stats = $provider->getParticipationStats();
        $this->assertEquals(0, $stats['eligible']);
        $this->assertEquals(0, $stats['registered']);
        $this->assertEquals(0, $stats['voted']);
        $this->assertEquals(0, $stats['not_voted']);
        $this->assertEquals(0.0, $stats['rate']);
        $this->assertEquals('0,00%', $stats['rate_formatted']);

        $activities = $provider->getRecentActivities();
        $this->assertIsArray($activities);
        $this->assertEmpty($activities);
    }

    public function test_voter_department_detection_from_registered_data(): void
    {
        (new EligibleVoterSeeder)->run();

        $provider = app(DashboardDataProvider::class);
        $departments = $provider->getDepartmentParticipation();

        $codes = array_column($departments, 'code');
        $this->assertContains('TI', $codes);
        $this->assertContains('TE', $codes);
        $this->assertContains('TM', $codes);
        $this->assertContains('TS', $codes);
        $this->assertContains('AK', $codes);
        $this->assertContains('AB', $codes);
        $this->assertContains('PAR', $codes);
    }

    public function test_dashboard_contextual_date_labels_use_indonesian_standard_format(): void
    {
        $provider = app(DashboardDataProvider::class);

        $now = Carbon::parse('2026-09-01 07:00:00');
        Carbon::setTestNow($now);

        $provider->setForcedElectionData([
            'registration_start_at' => '2026-09-09 08:05:00',
            'registration_end_at' => '2026-09-19 16:00:00',
            'voting_start_at' => '2026-09-25 08:00:00',
            'voting_end_at' => '2026-09-25 16:00:00',
        ]);

        $data = $provider->getElectionData();
        $this->assertEquals('Dimulai 09/09/2026 08:05 WITA', $data['contextual_date_label']);

        Carbon::setTestNow(Carbon::parse('2026-09-10 10:00:00'));
        $data = $provider->getElectionData();
        $this->assertEquals('Voting Dimulai 25/09/2026 08:00 WITA', $data['contextual_date_label']);

        Carbon::setTestNow(Carbon::parse('2026-09-25 10:00:00'));
        $data = $provider->getElectionData();
        $this->assertEquals('Berakhir 25/09/2026 16:00 WITA', $data['contextual_date_label']);

        Carbon::setTestNow(Carbon::parse('2026-09-25 18:00:00'));
        $data = $provider->getElectionData();
        $this->assertEquals('Telah Berakhir 25/09/2026 16:00 WITA', $data['contextual_date_label']);

        Carbon::setTestNow();
    }
}
