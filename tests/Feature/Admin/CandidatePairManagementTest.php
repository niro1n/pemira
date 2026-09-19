<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\CandidatePairs\Index as CandidatePairIndex;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CandidatePairManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superAdmin;

    private User $voter;

    private StudyProgram $studyProgram;

    private Election $election;

    private EligibleVoter $voter1;

    private EligibleVoter $voter2;

    private EligibleVoter $voter3;

    private EligibleVoter $voter4;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

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

        $this->election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->addDays(2),
            'registration_end_at' => Carbon::now()->addDays(5),
            'voting_start_at' => Carbon::now()->addDays(10),
            'voting_end_at' => Carbon::now()->addDays(11),
        ]);

        $this->voter1 = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Made Arya',
            'date_of_birth' => '2004-01-10',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $this->voter2 = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Ni Kadek Ayu',
            'date_of_birth' => '2004-02-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $this->voter3 = EligibleVoter::create([
            'nim' => '2215354003',
            'name' => 'I Wayan Budi',
            'date_of_birth' => '2004-03-20',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $this->voter4 = EligibleVoter::create([
            'nim' => '2215354004',
            'name' => 'Ni Putu Dewi',
            'date_of_birth' => '2004-04-25',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);
    }

    public function test_voter_cannot_access_candidate_pair_management(): void
    {
        $response = $this->actingAs($this->voter)->get('/admin/paslon');
        $response->assertForbidden();
    }

    public function test_admin_can_access_candidate_pair_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/paslon');
        $response->assertOk();
        $response->assertSee('MANAJEMEN PASLON');
        $response->assertSee('TAMBAH PASLON');
    }

    public function test_super_admin_can_access_candidate_pair_management(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/paslon');
        $response->assertOk();
        $response->assertSee('MANAJEMEN PASLON');
    }

    public function test_admin_can_create_candidate_pair_with_valid_data(): void
    {
        $file = UploadedFile::fake()->image('paslon01.jpg', 600, 800);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('candidate_number', 1)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter2->id)
            ->set('vision', 'Mewujudkan BEM PNB yang berintegritas dan progresif.')
            ->set('mission', '1. Meningkatkan advokasi mahasiswa.\n2. Mengembangkan inovasi teknologi.')
            ->set('is_active', true)
            ->set('photo', $file)
            ->call('createCandidatePair')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('candidate_pairs', [
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Mewujudkan BEM PNB yang berintegritas dan progresif.',
            'is_active' => true,
        ]);

        $pair = CandidatePair::where('election_id', $this->election->id)
            ->where('candidate_number', 1)
            ->first();

        $this->assertNotNull($pair);
        $this->assertNotNull($pair->photo);
        Storage::disk('public')->assertExists($pair->photo);

        $this->assertDatabaseHas('candidate_members', [
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        $this->assertDatabaseHas('candidate_members', [
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);
    }

    public function test_candidate_pair_must_belong_to_an_election(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter2->id)
            ->set('vision', 'Visi test')
            ->set('mission', 'Misi test')
            ->call('createCandidatePair')
            ->assertHasNoErrors();

        $pair = CandidatePair::first();
        $this->assertEquals($this->election->id, $pair->election_id);
        $this->assertEquals($this->election->id, $pair->election->id);
    }

    public function test_candidate_number_must_be_unique_within_the_election(): void
    {
        CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi 1',
            'mission' => 'Misi 1',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter2->id)
            ->set('vision', 'Visi duplikat')
            ->set('mission', 'Misi duplikat')
            ->call('createCandidatePair')
            ->assertHasErrors(['candidate_number']);

        $this->assertEquals(1, CandidatePair::where('election_id', $this->election->id)->count());
    }

    public function test_same_candidate_number_allowed_in_different_elections(): void
    {
        $otherElection = Election::create([
            'name' => 'PEMIRA BEM PNB 2025',
            'slug' => 'pemira-bem-pnb-2025',
            'year' => 2025,
            'registration_start_at' => Carbon::now()->subYear(),
            'registration_end_at' => Carbon::now()->subYear()->addDays(5),
            'voting_start_at' => Carbon::now()->subYear()->addDays(10),
            'voting_end_at' => Carbon::now()->subYear()->addDays(11),
        ]);

        CandidatePair::create([
            'election_id' => $otherElection->id,
            'candidate_number' => 1,
            'vision' => 'Visi 2025',
            'mission' => 'Misi 2025',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter2->id)
            ->set('vision', 'Visi 2026')
            ->set('mission', 'Misi 2026')
            ->call('createCandidatePair')
            ->assertHasNoErrors();

        $this->assertEquals(1, CandidatePair::where('election_id', $this->election->id)->count());
        $this->assertEquals(1, CandidatePair::where('election_id', $otherElection->id)->count());
    }

    public function test_ketua_and_wakil_must_come_from_eligible_voters(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', 999999)
            ->set('vice_leader_id', 888888)
            ->set('vision', 'Visi')
            ->set('mission', 'Misi')
            ->call('createCandidatePair')
            ->assertHasErrors(['leader_id', 'vice_leader_id']);

        $this->assertEquals(0, CandidatePair::count());
    }

    public function test_ketua_and_wakil_cannot_be_the_same_student(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter1->id)
            ->set('vision', 'Visi')
            ->set('mission', 'Misi')
            ->call('createCandidatePair')
            ->assertHasErrors(['vice_leader_id']);

        $this->assertEquals(0, CandidatePair::count());
    }

    public function test_student_in_another_candidate_pair_in_same_election_cannot_be_reused(): void
    {
        $pair1 = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi 1',
            'mission' => 'Misi 1',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair1->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair1->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 2)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter3->id)
            ->set('vision', 'Visi 2')
            ->set('mission', 'Misi 2')
            ->call('createCandidatePair')
            ->assertHasErrors(['leader_id']);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 2)
            ->set('leader_id', $this->voter3->id)
            ->set('vice_leader_id', $this->voter2->id)
            ->set('vision', 'Visi 2')
            ->set('mission', 'Misi 2')
            ->call('createCandidatePair')
            ->assertHasErrors(['vice_leader_id']);

        $this->assertEquals(1, CandidatePair::count());
    }

    public function test_candidate_pair_can_be_edited(): void
    {
        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Awal',
            'mission' => 'Misi Awal',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openEditModal', $pair->id)
            ->assertSet('showEditModal', true)
            ->assertSet('candidate_number', 1)
            ->assertSet('leader_id', $this->voter1->id)
            ->set('vision', 'Visi Direvisi')
            ->set('vice_leader_id', $this->voter3->id)
            ->call('updateCandidatePair')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('candidate_pairs', [
            'id' => $pair->id,
            'vision' => 'Visi Direvisi',
        ]);

        $this->assertDatabaseHas('candidate_members', [
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter3->id,
            'position' => 'wakil',
        ]);
    }

    public function test_candidate_pair_without_ballots_can_be_deleted(): void
    {
        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi',
            'mission' => 'Misi',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openDeleteModal', $pair->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteCandidatePair')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('candidate_pairs', [
            'id' => $pair->id,
        ]);

        $this->assertDatabaseMissing('candidate_members', [
            'candidate_pair_id' => $pair->id,
        ]);
    }

    public function test_candidate_pair_with_ballots_cannot_be_deleted(): void
    {
        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi',
            'mission' => 'Misi',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        DB::table('ballots')->insert([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
        ]);

        $this->assertTrue($pair->hasBallots());

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openDeleteModal', $pair->id)
            ->call('deleteCandidatePair')
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('candidate_pairs', [
            'id' => $pair->id,
        ]);
    }

    public function test_candidate_pairs_scoped_to_selected_election(): void
    {
        $otherElection = Election::create([
            'name' => 'PEMIRA 2025',
            'slug' => 'pemira-2025',
            'year' => 2025,
            'registration_start_at' => Carbon::now()->subYear(),
            'registration_end_at' => Carbon::now()->subYear()->addDays(5),
            'voting_start_at' => Carbon::now()->subYear()->addDays(10),
            'voting_end_at' => Carbon::now()->subYear()->addDays(11),
        ]);

        $pair1 = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi 2026',
            'mission' => 'Misi 2026',
            'is_active' => true,
        ]);

        $pairOther = CandidatePair::create([
            'election_id' => $otherElection->id,
            'candidate_number' => 1,
            'vision' => 'Visi 2025',
            'mission' => 'Misi 2025',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->assertSee('Visi 2026')
            ->assertDontSee('Visi 2025');

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $otherElection->id])
            ->assertSee('Visi 2025')
            ->assertDontSee('Visi 2026');
    }

    public function test_candidate_pairs_are_ordered_by_candidate_number_asc(): void
    {
        CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 3,
            'vision' => 'Visi Paslon Tiga',
            'mission' => 'Misi 3',
            'is_active' => true,
        ]);

        CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Paslon Satu',
            'mission' => 'Misi 1',
            'is_active' => true,
        ]);

        CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 2,
            'vision' => 'Visi Paslon Dua',
            'mission' => 'Misi 2',
            'is_active' => true,
        ]);

        $ordered = $this->election->candidatePairs()->pluck('candidate_number')->all();
        $this->assertEquals([1, 2, 3], $ordered);
    }

    public function test_landing_page_retrieves_candidate_pairs_dynamically_from_database(): void
    {
        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Dinamis Kampus Merdeka',
            'mission' => 'Misi Dinamis Kampus',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('PASLON 01');
        $response->assertSee('I Made Arya');
        $response->assertSee('Ni Kadek Ayu');
        $response->assertSee(route('public.candidates.show', $pair));

        $detailResponse = $this->get('/paslon/paslon-01');
        $detailResponse->assertOk();
        $detailResponse->assertSee('Visi Dinamis Kampus Merdeka');
        $detailResponse->assertSee('Misi Dinamis Kampus');
    }

    public function test_landing_page_does_not_contain_hardcoded_dummy_candidates(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('SUCIPTA');
        $response->assertDontSee('SUNGI');
    }

    public function test_landing_page_candidate_count_is_dynamic(): void
    {
        $responseEmpty = $this->get('/');
        $responseEmpty->assertOk();
        $responseEmpty->assertSee('BELUM ADA PASLON TERDAFTAR');

        $pair1 = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Paslon 1',
            'mission' => 'Misi Paslon 1',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair1->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair1->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        $response1 = $this->get('/');
        $response1->assertOk();
        $response1->assertSee('PASLON 01');
        $response1->assertDontSee('PASLON 02');

        $pair2 = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 2,
            'vision' => 'Visi Paslon 2',
            'mission' => 'Misi Paslon 2',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair2->id,
            'eligible_voter_id' => $this->voter3->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair2->id,
            'eligible_voter_id' => $this->voter4->id,
            'position' => 'wakil',
        ]);

        $response2 = $this->get('/');
        $response2->assertOk();
        $response2->assertSee('PASLON 01');
        $response2->assertSee('PASLON 02');
    }

    public function test_eager_loading_avoids_n_plus_one_queries(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $leader = EligibleVoter::create([
                'nim' => "221535401{$i}",
                'name' => "Leader {$i}",
                'date_of_birth' => '2004-05-10',
                'study_program_id' => $this->studyProgram->id,
                'is_eligible' => true,
            ]);

            $vice = EligibleVoter::create([
                'nim' => "221535402{$i}",
                'name' => "Vice {$i}",
                'date_of_birth' => '2004-06-10',
                'study_program_id' => $this->studyProgram->id,
                'is_eligible' => true,
            ]);

            $pair = CandidatePair::create([
                'election_id' => $this->election->id,
                'candidate_number' => $i,
                'vision' => "Visi Paslon {$i}",
                'mission' => "Misi Paslon {$i}",
                'is_active' => true,
            ]);

            CandidateMember::create([
                'election_id' => $this->election->id,
                'candidate_pair_id' => $pair->id,
                'eligible_voter_id' => $leader->id,
                'position' => 'ketua',
            ]);

            CandidateMember::create([
                'election_id' => $this->election->id,
                'candidate_pair_id' => $pair->id,
                'eligible_voter_id' => $vice->id,
                'position' => 'wakil',
            ]);
        }

        DB::enableQueryLog();

        $response = $this->get('/');
        $response->assertOk();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertLessThan(15, $queryCount);
    }

    public function test_edit_paslon_status_active_roundtrip_both_ways(): void
    {
        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Awal',
            'mission' => 'Misi Awal',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        $this->assertTrue($pair->is_active);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class)
            ->set('selectedElectionId', $this->election->id)
            ->call('openEditModal', $pair->id)
            ->assertSet('is_active', true)
            ->set('is_active', false)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertFalse($pair->is_active);
        $this->assertSame(false, $pair->is_active);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('PASLON 01');

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class)
            ->set('selectedElectionId', $this->election->id)
            ->call('openEditModal', $pair->id)
            ->assertSet('is_active', false)
            ->set('is_active', true)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertTrue($pair->is_active);
        $this->assertSame(true, $pair->is_active);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('PASLON 01');

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class)
            ->set('selectedElectionId', $this->election->id)
            ->call('openEditModal', $pair->id)
            ->assertSet('is_active', true)
            ->set('is_active', true)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertTrue($pair->is_active);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class)
            ->set('selectedElectionId', $this->election->id)
            ->call('openEditModal', $pair->id)
            ->set('is_active', false)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertFalse($pair->is_active);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class)
            ->set('selectedElectionId', $this->election->id)
            ->call('openEditModal', $pair->id)
            ->assertSet('is_active', false)
            ->set('is_active', false)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertFalse($pair->is_active);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class)
            ->set('selectedElectionId', $this->election->id)
            ->call('openEditModal', $pair->id)
            ->assertSet('is_active', false)
            ->set('is_active', true)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertTrue($pair->is_active);
    }

    public function test_non_image_file_rejected_for_photo(): void
    {
        $nonImageFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', $this->voter1->id)
            ->set('vice_leader_id', $this->voter2->id)
            ->set('vision', 'Visi test')
            ->set('mission', 'Misi test')
            ->set('photo', $nonImageFile)
            ->call('createCandidatePair')
            ->assertHasErrors(['photo']);

        $this->assertEquals(0, CandidatePair::count());
    }

    public function test_edit_candidate_pair_replaces_photo_and_deletes_old_photo(): void
    {
        $oldFile = UploadedFile::fake()->image('old_photo.jpg', 600, 800);
        $oldPath = $oldFile->store('candidate-photos', 'public');

        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'photo' => $oldPath,
            'vision' => 'Visi Awal',
            'mission' => 'Misi Awal',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('new_cropped_photo.jpg', 960, 600);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openEditModal', $pair->id)
            ->set('photo', $newFile)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertNotNull($pair->photo);
        $this->assertNotEquals($oldPath, $pair->photo);
        Storage::disk('public')->assertExists($pair->photo);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_edit_candidate_pair_can_remove_photo(): void
    {
        $oldFile = UploadedFile::fake()->image('photo_to_remove.jpg', 600, 800);
        $oldPath = $oldFile->store('candidate-photos', 'public');

        $pair = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'photo' => $oldPath,
            'vision' => 'Visi Awal',
            'mission' => 'Misi Awal',
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $this->voter2->id,
            'position' => 'wakil',
        ]);

        Storage::disk('public')->assertExists($oldPath);

        Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openEditModal', $pair->id)
            ->set('removePhoto', true)
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $pair->refresh();
        $this->assertNull($pair->photo);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_admin_can_cancel_pending_new_photo(): void
    {
        $file = UploadedFile::fake()->image('temp_photo.jpg', 600, 800);

        $testable = Livewire::actingAs($this->admin)
            ->test(CandidatePairIndex::class, ['selectedElectionId' => $this->election->id])
            ->call('openCreateModal')
            ->set('photo', $file);

        $this->assertNotNull($testable->get('photo'));

        $testable->call('cancelNewPhoto')
            ->assertSet('photo', null);
    }
}
