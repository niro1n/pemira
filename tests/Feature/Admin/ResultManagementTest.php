<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Results\Index as ResultIndex;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ResultManagementTest extends TestCase
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

    public function test_admin_can_access_results_page(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(10),
            'registration_end_at' => Carbon::now()->subDays(5),
            'voting_start_at' => Carbon::now()->subDays(2),
            'voting_end_at' => Carbon::now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/results');

        $response->assertOk();
        $response->assertSeeLivewire(ResultIndex::class);
        $response->assertSee('HASIL PERHITUNGAN SUARA RESMI');
        $response->assertSee('PEMIRA BEM PNB 2026');
    }

    public function test_legacy_hasil_perhitungan_redirects_to_results(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/hasil-perhitungan');

        $response->assertRedirect(route('admin.results.index'));
        $response->assertStatus(301);
    }

    public function test_super_admin_can_access_results_page(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(10),
            'registration_end_at' => Carbon::now()->subDays(5),
            'voting_start_at' => Carbon::now()->subDays(2),
            'voting_end_at' => Carbon::now()->subDay(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/admin/results');

        $response->assertOk();
        $response->assertSeeLivewire(ResultIndex::class);
        $response->assertSee('HASIL PERHITUNGAN SUARA RESMI');
    }

    public function test_voter_cannot_access_results_page(): void
    {
        $response = $this->actingAs($this->voter)->get('/admin/results');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/results');

        $response->assertRedirect('/login');
    }

    public function test_results_page_displays_finished_election_with_final_status_and_highest_vote_candidate(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(10),
            'registration_end_at' => Carbon::now()->subDays(5),
            'voting_start_at' => Carbon::now()->subDays(2),
            'voting_end_at' => Carbon::now()->subDay(),
        ]);

        $voter1 = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Made Arya',
            'date_of_birth' => '2004-01-10',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $voter2 = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Ni Kadek Ayu',
            'date_of_birth' => '2004-02-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $voter3 = EligibleVoter::create([
            'nim' => '2215354003',
            'name' => 'I Wayan Budi',
            'date_of_birth' => '2004-03-20',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $voter4 = EligibleVoter::create([
            'nim' => '2215354004',
            'name' => 'Ni Putu Dewi',
            'date_of_birth' => '2004-04-25',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $pair1 = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Paslon 1',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair1->id,
            'eligible_voter_id' => $voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair1->id,
            'eligible_voter_id' => $voter2->id,
            'position' => 'wakil',
        ]);

        $pair2 = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 2,
            'vision' => 'Visi Paslon 2',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair2->id,
            'eligible_voter_id' => $voter3->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair2->id,
            'eligible_voter_id' => $voter4->id,
            'position' => 'wakil',
        ]);

        for ($i = 0; $i < 60; $i++) {
            DB::table('ballots')->insert([
                'id' => (string) Str::uuid(),
                'election_id' => $election->id,
                'candidate_pair_id' => $pair1->id,
            ]);
        }

        for ($i = 0; $i < 40; $i++) {
            DB::table('ballots')->insert([
                'id' => (string) Str::uuid(),
                'election_id' => $election->id,
                'candidate_pair_id' => $pair2->id,
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(ResultIndex::class, ['selectedElectionId' => $election->id])
            ->assertSee('PERHITUNGAN RESMI SELESAI')
            ->assertSee('100')
            ->assertSee('60,00%')
            ->assertSee('40,00%')
            ->assertSee('PEROLEHAN SUARA TERTINGGI')
            ->assertSee('I Made Arya')
            ->assertSee('I Wayan Budi')
            ->assertSee('DETAIL PEROLEHAN SUARA');
    }

    public function test_results_page_displays_voting_in_progress_with_live_status(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA LIVE 2026',
            'slug' => 'pemira-live-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(5),
            'registration_end_at' => Carbon::now()->subDays(2),
            'voting_start_at' => Carbon::now()->subHours(2),
            'voting_end_at' => Carbon::now()->addHours(6),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ResultIndex::class, ['selectedElectionId' => $election->id])
            ->assertSee('PEMUNGUTAN SUARA BERLANGSUNG')
            ->assertSee('LIVE COUNT AKTIF')
            ->assertSee('Hasil sementara diperbarui secara langsung dari bilik suara digital secara anonim');
    }

    public function test_results_page_displays_upcoming_election_with_not_started_state(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA MASA DEPAN 2026',
            'slug' => 'pemira-masa-depan-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->addDays(5),
            'registration_end_at' => Carbon::now()->addDays(10),
            'voting_start_at' => Carbon::now()->addDays(15),
            'voting_end_at' => Carbon::now()->addDays(16),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ResultIndex::class, ['selectedElectionId' => $election->id])
            ->assertSee('HASIL BELUM TERSEDIA')
            ->assertSee('PEMUNGUTAN SUARA BELUM DIMULAI');
    }

    public function test_results_page_handles_election_switch(): void
    {
        $election1 = Election::create([
            'name' => 'PEMIRA PERTAMA',
            'slug' => 'pemira-pertama',
            'year' => 2025,
            'registration_start_at' => Carbon::now()->subMonths(2),
            'registration_end_at' => Carbon::now()->subMonths(2)->addDays(5),
            'voting_start_at' => Carbon::now()->subMonths(2)->addDays(6),
            'voting_end_at' => Carbon::now()->subMonths(2)->addDays(7),
        ]);

        $election2 = Election::create([
            'name' => 'PEMIRA KEDUA',
            'slug' => 'pemira-kedua',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(10),
            'registration_end_at' => Carbon::now()->subDays(5),
            'voting_start_at' => Carbon::now()->subDays(2),
            'voting_end_at' => Carbon::now()->subDay(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ResultIndex::class, ['selectedElectionId' => $election1->id])
            ->assertSee('PEMIRA PERTAMA')
            ->call('selectElection', $election2->id)
            ->assertSet('selectedElectionId', $election2->id)
            ->assertSee('PEMIRA KEDUA');
    }

    public function test_results_page_refresh_action_updates_timestamp(): void
    {
        $election = Election::create([
            'name' => 'PEMIRA REFRESH TEST',
            'slug' => 'pemira-refresh-test',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(10),
            'registration_end_at' => Carbon::now()->subDays(5),
            'voting_start_at' => Carbon::now()->subDays(2),
            'voting_end_at' => Carbon::now()->subDay(),
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(ResultIndex::class, ['selectedElectionId' => $election->id])
            ->call('refreshResults')
            ->assertSee('WITA');

        $this->assertNotEmpty($component->get('lastRefreshedAt'));
    }

    public function test_results_page_handles_empty_elections_state(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ResultIndex::class, ['selectedElectionId' => null])
            ->assertSee('BELUM ADA DATA PEMILIHAN')
            ->assertSee('Data pemilihan raya belum dikonfigurasi atau belum tersedia di sistem');
    }
}
