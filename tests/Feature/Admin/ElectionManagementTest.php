<?php

namespace Tests\Feature\Admin;

use App\Enums\ElectionPhase;
use App\Livewire\Admin\Elections\Index as ElectionIndex;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ElectionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superAdmin;

    private User $voter;

    private StudyProgram $studyProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);

        $this->admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create([
            'email_verified_at' => now(),
        ]);

        $this->voter = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);
    }

    public function test_elections_route_exists(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/elections');
        $response->assertOk();
    }

    public function test_voter_cannot_access_elections_management(): void
    {
        $response = $this->actingAs($this->voter)->get('/admin/elections');
        $response->assertForbidden();
    }

    public function test_admin_can_access_elections_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/elections');
        $response->assertOk();
        $response->assertSee('MANAJEMEN PEMIRA');
        $response->assertSee('BUAT PEMIRA');
    }

    public function test_super_admin_can_access_elections_management(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/elections');
        $response->assertOk();
        $response->assertSee('MANAJEMEN PEMIRA');
    }

    public function test_create_modal_can_create_election_with_valid_data(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('name', 'PEMIRA BEM PNB 2026')
            ->set('year', 2026)
            ->set('registration_start_at', '2026-10-01T08:00')
            ->set('registration_end_at', '2026-10-05T16:00')
            ->set('voting_start_at', '2026-10-10T08:00')
            ->set('voting_end_at', '2026-10-10T16:00')
            ->call('createElection')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('elections', [
            'name' => 'PEMIRA BEM PNB 2026',
            'year' => 2026,
            'slug' => 'pemira-bem-pnb-2026-2026',
        ]);
    }

    public function test_create_validation_rejects_empty_or_invalid_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openCreateModal')
            ->set('name', '')
            ->set('year', null)
            ->call('createElection')
            ->assertHasErrors(['name', 'year']);

        $this->assertEquals(0, Election::count());
    }

    public function test_invalid_schedule_order_is_rejected(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openCreateModal')
            ->set('name', 'PEMIRA Jadwal Error')
            ->set('year', 2026)
            ->set('registration_start_at', '2026-10-05T08:00')
            ->set('registration_end_at', '2026-10-01T16:00')
            ->set('voting_start_at', '2026-09-30T08:00')
            ->set('voting_end_at', '2026-09-29T16:00')
            ->call('createElection')
            ->assertHasErrors(['registration_end_at', 'voting_start_at', 'voting_end_at']);

        $this->assertEquals(0, Election::count());
    }

    public function test_edit_action_updates_election_data(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Lama',
            'slug' => 'pemira-lama-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->addDays(2),
            'registration_end_at' => Carbon::now()->addDays(5),
            'voting_start_at' => Carbon::now()->addDays(10),
            'voting_end_at' => Carbon::now()->addDays(11),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $election->id)
            ->assertSet('showEditModal', true)
            ->assertSet('name', 'PEMIRA Lama')
            ->set('name', 'PEMIRA Baru Direvisi')
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('elections', [
            'id' => $election->id,
            'name' => 'PEMIRA Baru Direvisi',
        ]);
    }

    public function test_active_voting_schedule_cannot_be_directly_changed(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA Live',
            'slug' => 'pemira-live-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(1),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(4),
        ]);

        $this->assertTrue($election->isVotingActive($now));

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $election->id)
            ->assertSet('isVotingActive', true)
            ->set('voting_end_at', $now->copy()->addHours(8)->format('Y-m-d\TH:i'))
            ->call('updateElection')
            ->assertHasErrors(['voting_start_at']);

        Carbon::setTestNow();
    }

    public function test_detail_action_returns_correct_election_data(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Detail Test',
            'slug' => 'pemira-detail-test-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-10-01 08:00:00'),
            'registration_end_at' => Carbon::parse('2026-10-05 16:00:00'),
            'voting_start_at' => Carbon::parse('2026-10-10 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-10-10 16:00:00'),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openDetailModal', $election->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedElection.name', 'PEMIRA Detail Test')
            ->assertSet('detailHistoricalSummary.participations', 0)
            ->assertSet('detailHistoricalSummary.ballots', 0)
            ->assertSet('detailHistoricalSummary.feedbacks', 0);
    }

    public function test_election_without_historical_data_can_be_deleted(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Kosong',
            'slug' => 'pemira-kosong-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->addDays(2),
            'registration_end_at' => Carbon::now()->addDays(5),
            'voting_start_at' => Carbon::now()->addDays(10),
            'voting_end_at' => Carbon::now()->addDays(11),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openDeleteModal', $election->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteElection')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('elections', [
            'id' => $election->id,
        ]);
    }

    public function test_election_with_voting_participation_cannot_be_deleted(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Berjalan',
            'slug' => 'pemira-berjalan-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(5),
            'registration_end_at' => Carbon::now()->subDays(1),
            'voting_start_at' => Carbon::now()->subHours(2),
            'voting_end_at' => Carbon::now()->addHours(4),
        ]);

        $eligibleVoter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Test Voter',
            'date_of_birth' => '2004-03-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $voterAccount = VoterAccount::create([
            'user_id' => $this->voter->id,
            'eligible_voter_id' => $eligibleVoter->id,
        ]);

        DB::table('voting_participations')->insert([
            'election_id' => $election->id,
            'voter_account_id' => $voterAccount->id,
            'voted_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->assertTrue($election->hasHistoricalData());

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openDeleteModal', $election->id)
            ->call('deleteElection')
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('elections', [
            'id' => $election->id,
        ]);
    }

    public function test_election_with_ballots_cannot_be_deleted(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Ballots Protect',
            'slug' => 'pemira-ballots-protect-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(5),
            'registration_end_at' => Carbon::now()->subDays(1),
            'voting_start_at' => Carbon::now()->subHours(2),
            'voting_end_at' => Carbon::now()->addHours(4),
        ]);

        $candidatePairId = DB::table('candidate_pairs')->insertGetId([
            'election_id' => $election->id,
            'candidate_number' => 1,
            'vision' => 'Visi test',
            'mission' => 'Misi test',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('ballots')->insert([
            'id' => (string) Str::uuid(),
            'election_id' => $election->id,
            'candidate_pair_id' => $candidatePairId,
        ]);

        $this->assertTrue($election->hasHistoricalData());

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openDeleteModal', $election->id)
            ->call('deleteElection')
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('elections', [
            'id' => $election->id,
        ]);
    }

    public function test_election_with_feedback_cannot_be_deleted(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA Feedback Protect',
            'slug' => 'pemira-feedback-protect-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(5),
            'registration_end_at' => Carbon::now()->subDays(1),
            'voting_start_at' => Carbon::now()->subHours(2),
            'voting_end_at' => Carbon::now()->addHours(4),
        ]);

        $eligibleVoter = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Feedback Voter',
            'date_of_birth' => '2004-03-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $voterAccount = VoterAccount::create([
            'user_id' => $this->voter->id,
            'eligible_voter_id' => $eligibleVoter->id,
        ]);

        DB::table('feedbacks')->insert([
            'election_id' => $election->id,
            'voter_account_id' => $voterAccount->id,
            'rating' => 5,
            'comment' => 'Bagus sekali',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->assertTrue($election->hasHistoricalData());

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openDeleteModal', $election->id)
            ->call('deleteElection')
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('elections', [
            'id' => $election->id,
        ]);
    }

    public function test_election_state_logic_remains_correct(): void
    {
        $regStart = Carbon::parse('2026-10-01 08:00:00');
        $regEnd = Carbon::parse('2026-10-05 16:00:00');
        $votingStart = Carbon::parse('2026-10-10 08:00:00');
        $votingEnd = Carbon::parse('2026-10-10 16:00:00');

        $election = Election::create([
            'name' => 'PEMIRA Phase Test',
            'slug' => 'pemira-phase-test-2026',
            'year' => 2026,
            'registration_start_at' => $regStart,
            'registration_end_at' => $regEnd,
            'voting_start_at' => $votingStart,
            'voting_end_at' => $votingEnd,
        ]);

        $this->assertEquals(ElectionPhase::UPCOMING, $election->currentPhase(Carbon::parse('2026-09-30 10:00:00')));
        $this->assertEquals(ElectionPhase::REGISTRATION, $election->currentPhase(Carbon::parse('2026-10-02 10:00:00')));
        $this->assertEquals(ElectionPhase::VOTING, $election->currentPhase(Carbon::parse('2026-10-10 10:00:00')));
        $this->assertEquals(ElectionPhase::FINISHED, $election->currentPhase(Carbon::parse('2026-10-10 17:00:00')));
    }
}
