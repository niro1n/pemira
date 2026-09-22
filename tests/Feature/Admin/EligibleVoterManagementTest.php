<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\EligibleVoters\Index as EligibleVoterIndex;
use App\Livewire\Auth\Register as AuthRegister;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use App\Models\VotingParticipation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class EligibleVoterManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superAdmin;

    private User $voterUser;

    private StudyProgram $tiProdi;

    private StudyProgram $akProdi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tiProdi = StudyProgram::create([
            'code' => 'TI',
            'name' => 'Teknologi Informasi',
        ]);

        $this->akProdi = StudyProgram::create([
            'code' => 'AK',
            'name' => 'Akuntansi',
        ]);

        $this->admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create([
            'email_verified_at' => now(),
        ]);

        $this->voterUser = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);
    }

    protected function createElection(array $overrides = []): Election
    {
        return Election::create(array_merge([
            'name' => 'PEMIRA PNB 2026',
            'slug' => 'pemira-pnb-2026',
            'year' => 2026,
            'registration_start_at' => now()->subDays(5),
            'registration_end_at' => now()->subDay(),
            'voting_start_at' => now()->subHours(2),
            'voting_end_at' => now()->addHours(6),
        ], $overrides));
    }

    public function test_admin_can_access_eligible_voters_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/eligible-voters');

        $response->assertOk();
        $response->assertSeeLivewire(EligibleVoterIndex::class);
        $response->assertSee('ELIGIBLE VOTERS');
        $response->assertSee('IMPORT CSV');
        $response->assertSee('+ TAMBAH MAHASISWA');
    }

    public function test_super_admin_can_access_eligible_voters_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/eligible-voters');

        $response->assertOk();
        $response->assertSeeLivewire(EligibleVoterIndex::class);
    }

    public function test_voter_cannot_access_eligible_voters_page(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/admin/eligible-voters');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/eligible-voters');

        $response->assertRedirect('/login');
    }

    public function test_valid_csv_can_be_previewed_and_imported(): void
    {
        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= "2215354001,I Putu Gede Raditya,Teknologi Informasi,2004-03-15\n";
        $csvContent .= "2215644020,Kadek Dimas Prasetya,Akuntansi,10-05-2004\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importStep', 'preview')
            ->assertSet('importSummary.total', 2)
            ->assertSet('importSummary.valid', 2)
            ->assertSet('importSummary.complete', 2)
            ->assertSet('importSummary.incomplete', 0)
            ->assertSet('importSummary.duplicates', 0)
            ->assertSet('importSummary.errors', 0)
            ->call('confirmImport')
            ->assertSet('importStep', 'result')
            ->assertSet('importResult.inserted', 2)
            ->assertSet('importResult.complete', 2)
            ->assertSet('importResult.incomplete', 0);

        $this->assertDatabaseHas('eligible_voters', [
            'nim' => '2215354001',
            'name' => 'I Putu Gede Raditya',
            'study_program_id' => $this->tiProdi->id,
        ]);

        $this->assertDatabaseHas('eligible_voters', [
            'nim' => '2215644020',
            'name' => 'Kadek Dimas Prasetya',
            'study_program_id' => $this->akProdi->id,
        ]);

        $voter1 = EligibleVoter::where('nim', '2215354001')->first();
        $this->assertTrue($voter1->isComplete());
        $this->assertEquals('2004-03-15', $voter1->date_of_birth->format('Y-m-d'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'eligible_voter_import',
        ]);
    }

    public function test_incomplete_csv_with_empty_dob_is_imported_as_incomplete(): void
    {
        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= "2215354010,Siswa Tanpa DOB,Teknologi Informasi,\n";

        $file = UploadedFile::fake()->createWithContent('no_dob.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.total', 1)
            ->assertSet('importSummary.valid', 1)
            ->assertSet('importSummary.complete', 0)
            ->assertSet('importSummary.incomplete', 1)
            ->assertSet('importSummary.errors', 0)
            ->call('confirmImport')
            ->assertSet('importResult.inserted', 1)
            ->assertSet('importResult.incomplete', 1);

        $voter = EligibleVoter::where('nim', '2215354010')->first();
        $this->assertNotNull($voter);
        $this->assertNull($voter->date_of_birth);
        $this->assertFalse($voter->isComplete());
        $this->assertContains('Tanggal Lahir', $voter->missingFields());
    }

    public function test_incomplete_csv_with_empty_name_is_imported_as_incomplete(): void
    {
        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= "2215354011,,Teknologi Informasi,2004-03-15\n";

        $file = UploadedFile::fake()->createWithContent('no_name.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.incomplete', 1)
            ->call('confirmImport')
            ->assertSet('importResult.incomplete', 1);

        $voter = EligibleVoter::where('nim', '2215354011')->first();
        $this->assertNotNull($voter);
        $this->assertNull($voter->name);
        $this->assertFalse($voter->isComplete());
        $this->assertContains('Nama Lengkap', $voter->missingFields());
    }

    public function test_incomplete_csv_with_empty_or_unmapped_jurusan_is_imported_as_incomplete(): void
    {
        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= "2215354012,Siswa Tanpa Jurusan,,2004-03-15\n";
        $csvContent .= "2215354013,Siswa Jurusan Asing,Kedokteran Hewan,2004-03-15\n";

        $file = UploadedFile::fake()->createWithContent('jurusan.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.total', 2)
            ->assertSet('importSummary.incomplete', 2)
            ->assertSet('importSummary.errors', 0)
            ->call('confirmImport');

        $voter1 = EligibleVoter::where('nim', '2215354012')->first();
        $voter2 = EligibleVoter::where('nim', '2215354013')->first();

        $this->assertNull($voter1->study_program_id);
        $this->assertNull($voter2->study_program_id);
        $this->assertFalse($voter1->isComplete());
        $this->assertFalse($voter2->isComplete());
        $this->assertContains('Jurusan / Program Studi', $voter1->missingFields());
        $this->assertContains('Jurusan / Program Studi', $voter2->missingFields());
    }

    public function test_duplicate_nim_does_not_create_duplicate_record(): void
    {
        EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Existing Student',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-01-01',
            'is_eligible' => true,
        ]);

        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= "2215354001,I Putu Gede Raditya,Teknologi Informasi,2004-03-15\n";
        $csvContent .= "2215644020,Kadek Dimas Prasetya,Akuntansi,2004-05-10\n";
        $csvContent .= "2215644020,Kadek Dimas Duplicate In File,Akuntansi,2004-05-10\n";

        $file = UploadedFile::fake()->createWithContent('duplicates.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.total', 3)
            ->assertSet('importSummary.valid', 1)
            ->assertSet('importSummary.duplicates', 2)
            ->call('confirmImport')
            ->assertSet('importResult.inserted', 1)
            ->assertSet('importResult.duplicates', 2);

        $this->assertEquals(2, EligibleVoter::count());
    }

    public function test_invalid_rows_are_rejected(): void
    {
        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= ",Nama Tanpa NIM,Teknologi Informasi,2004-03-15\n";
        $csvContent .= "2215354004,Nama Valid,Teknologi Informasi,invalid-date-format\n";

        $file = UploadedFile::fake()->createWithContent('invalids.csv', $csvContent);

        $component = Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.total', 2)
            ->assertSet('importSummary.valid', 0)
            ->assertSet('importSummary.errors', 2);

        $this->assertCount(2, $component->get('importErrors'));
        $this->assertEquals(0, EligibleVoter::count());
    }

    public function test_import_summary_and_result_are_accurate(): void
    {
        $csvContent = "NIM,Nama,Jurusan,Tanggal Lahir\n";
        $csvContent .= "2215354001,Valid Satu,Teknologi Informasi,2004-03-15\n";
        $csvContent .= "2215354002,,Teknologi Informasi,2004-05-10\n";
        $csvContent .= ",NIM Kosong,Teknologi Informasi,2004-01-01\n";

        $file = UploadedFile::fake()->createWithContent('mixed.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.total', 3)
            ->assertSet('importSummary.valid', 2)
            ->assertSet('importSummary.complete', 1)
            ->assertSet('importSummary.incomplete', 1)
            ->assertSet('importSummary.duplicates', 0)
            ->assertSet('importSummary.errors', 1)
            ->call('confirmImport')
            ->assertSet('importResult.inserted', 2)
            ->assertSet('importResult.complete', 1)
            ->assertSet('importResult.incomplete', 1)
            ->assertSet('importResult.duplicates', 0)
            ->assertSet('importResult.failed', 1);
    }

    public function test_batch_import_large_dataset_in_chunks(): void
    {
        $rows = ['NIM,Nama,Jurusan,Tanggal Lahir'];
        for ($i = 1; $i <= 550; $i++) {
            $nim = sprintf('221535%04d', $i);
            $rows[] = "{$nim},Mahasiswa {$i},Teknologi Informasi,2004-03-15";
        }
        $csvContent = implode("\n", $rows);
        $file = UploadedFile::fake()->createWithContent('large_batch.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('importFile', $file)
            ->call('processUpload')
            ->assertSet('importSummary.total', 550)
            ->assertSet('importSummary.valid', 550)
            ->call('confirmImport')
            ->assertSet('importResult.inserted', 550);

        $this->assertEquals(550, EligibleVoter::count());
    }

    public function test_search_by_nim_and_name_works(): void
    {
        $voter1 = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Putu Gede Raditya',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        $voter2 = EligibleVoter::create([
            'nim' => '2215644020',
            'name' => 'Kadek Dimas Prasetya',
            'study_program_id' => $this->akProdi->id,
            'date_of_birth' => '2004-05-10',
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('search', '2215354001')
            ->assertSee($voter1->name)
            ->assertDontSee($voter2->name)
            ->set('search', 'Dimas')
            ->assertSee($voter2->name)
            ->assertDontSee($voter1->name)
            ->set('search', 'TidakAdaSiswa')
            ->assertSee('DATA TIDAK DITEMUKAN');
    }

    public function test_filter_by_study_program_works(): void
    {
        $voter1 = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Putu Gede Raditya',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        $voter2 = EligibleVoter::create([
            'nim' => '2215644020',
            'name' => 'Kadek Dimas Prasetya',
            'study_program_id' => $this->akProdi->id,
            'date_of_birth' => '2004-05-10',
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('studyProgramFilter', $this->tiProdi->id)
            ->assertSee($voter1->name)
            ->assertDontSee($voter2->name)
            ->set('studyProgramFilter', $this->akProdi->id)
            ->assertSee($voter2->name)
            ->assertDontSee($voter1->name);
    }

    public function test_filter_by_completeness_works(): void
    {
        $completeVoter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Siswa Lengkap',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        $incompleteVoter = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Siswa Parsial',
            'study_program_id' => null,
            'date_of_birth' => null,
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('completenessFilter', 'complete')
            ->assertSee($completeVoter->nim)
            ->assertDontSee($incompleteVoter->nim)
            ->set('completenessFilter', 'incomplete')
            ->assertSee($incompleteVoter->nim)
            ->assertDontSee($completeVoter->nim);
    }

    public function test_filter_by_registration_status_works(): void
    {
        $registeredVoter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Siswa Terdaftar',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $registeredVoter->id,
        ]);

        $unregisteredVoter = EligibleVoter::create([
            'nim' => '2215644020',
            'name' => 'Siswa Belum Terdaftar',
            'study_program_id' => $this->akProdi->id,
            'date_of_birth' => '2004-05-10',
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('registrationFilter', 'registered')
            ->assertSee($registeredVoter->name)
            ->assertDontSee($unregisteredVoter->name)
            ->set('registrationFilter', 'unregistered')
            ->assertSee($unregisteredVoter->name)
            ->assertDontSee($registeredVoter->name);
    }

    public function test_filter_by_voting_status_works(): void
    {
        $election = $this->createElection();

        $voter1 = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Siswa Sudah Voting',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        $voterAccount1 = VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $voter1->id,
        ]);

        VotingParticipation::create([
            'election_id' => $election->id,
            'voter_account_id' => $voterAccount1->id,
            'voted_at' => now(),
        ]);

        $otherUser = User::factory()->create(['role' => 'voter']);

        $voter2 = EligibleVoter::create([
            'nim' => '2215644020',
            'name' => 'Siswa Belum Voting',
            'study_program_id' => $this->akProdi->id,
            'date_of_birth' => '2004-05-10',
            'is_eligible' => true,
        ]);

        VoterAccount::create([
            'user_id' => $otherUser->id,
            'eligible_voter_id' => $voter2->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('votingFilter', 'voted')
            ->assertSee($voter1->name)
            ->assertDontSee($voter2->name)
            ->set('votingFilter', 'not_voted')
            ->assertSee($voter2->name)
            ->assertDontSee($voter1->name);
    }

    public function test_manual_create_eligible_voter_works(): void
    {
        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openCreateModal')
            ->set('nim', '2215354099')
            ->set('name', 'Mahasiswa Manual')
            ->set('study_program_id', $this->tiProdi->id)
            ->set('date_of_birth', '2004-08-20')
            ->call('createEligibleVoter')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('eligible_voters', [
            'nim' => '2215354099',
            'name' => 'Mahasiswa Manual',
            'study_program_id' => $this->tiProdi->id,
        ]);

        $voter = EligibleVoter::where('nim', '2215354099')->first();
        $this->assertEquals('2004-08-20', $voter->date_of_birth->format('Y-m-d'));
        $this->assertTrue($voter->isComplete());

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'eligible_voter_created',
        ]);
    }

    public function test_edit_incomplete_voter_becomes_complete_when_filled(): void
    {
        $incompleteVoter = EligibleVoter::create([
            'nim' => '2215354090',
            'name' => 'Nama Saja',
            'study_program_id' => null,
            'date_of_birth' => null,
            'is_eligible' => true,
        ]);

        $this->assertFalse($incompleteVoter->isComplete());

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openEditModal', $incompleteVoter->id)
            ->set('study_program_id', $this->tiProdi->id)
            ->set('date_of_birth', '2004-03-15')
            ->call('updateEligibleVoter')
            ->assertHasNoErrors();

        $incompleteVoter->refresh();
        $this->assertTrue($incompleteVoter->isComplete());
        $this->assertEquals($this->tiProdi->id, $incompleteVoter->study_program_id);
        $this->assertEquals('2004-03-15', $incompleteVoter->date_of_birth->format('Y-m-d'));
    }

    public function test_edit_eligible_voter_and_nim_lock_rule(): void
    {
        $voterWithoutAccount = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Nama Awal',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openEditModal', $voterWithoutAccount->id)
            ->assertSet('isNimLocked', false)
            ->set('nim', '2215354099')
            ->set('name', 'Nama Diperbarui')
            ->call('updateEligibleVoter')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('eligible_voters', [
            'id' => $voterWithoutAccount->id,
            'nim' => '2215354099',
            'name' => 'Nama Diperbarui',
        ]);

        $voterWithAccount = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Punya Akun',
            'study_program_id' => $this->akProdi->id,
            'date_of_birth' => '2004-05-10',
            'is_eligible' => true,
        ]);

        VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $voterWithAccount->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openEditModal', $voterWithAccount->id)
            ->assertSet('isNimLocked', true)
            ->set('nim', '2215354999')
            ->call('updateEligibleVoter')
            ->assertHasErrors(['nim']);

        $this->assertDatabaseHas('eligible_voters', [
            'id' => $voterWithAccount->id,
            'nim' => '2215354002',
        ]);
    }

    public function test_delete_is_protected_if_voter_account_or_candidate_exists(): void
    {
        $safeVoter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Bisa Dihapus',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openDeleteModal', $safeVoter->id)
            ->assertSet('deleteError', null)
            ->call('deleteEligibleVoter');

        $this->assertDatabaseMissing('eligible_voters', [
            'id' => $safeVoter->id,
        ]);

        $protectedVoter = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Tidak Bisa Dihapus',
            'study_program_id' => $this->akProdi->id,
            'date_of_birth' => '2004-05-10',
            'is_eligible' => true,
        ]);

        VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $protectedVoter->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openDeleteModal', $protectedVoter->id)
            ->assertSet('deleteError', 'Data mahasiswa tidak dapat dihapus karena sudah digunakan dalam data pemilih.')
            ->call('deleteEligibleVoter');

        $this->assertDatabaseHas('eligible_voters', [
            'id' => $protectedVoter->id,
        ]);
    }

    public function test_delete_is_protected_if_candidate_member_exists(): void
    {
        $election = $this->createElection();
        $candidatePair = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 1,
            'vision' => 'Visi Paslon',
            'mission' => 'Misi Paslon',
            'is_active' => true,
        ]);

        $candidateVoter = EligibleVoter::create([
            'nim' => '2215354050',
            'name' => 'Calon Ketua',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $candidatePair->id,
            'eligible_voter_id' => $candidateVoter->id,
            'position' => 'ketua',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openDeleteModal', $candidateVoter->id)
            ->assertSet('deleteError', 'Data mahasiswa tidak dapat dihapus karena sudah digunakan dalam data pemilih.')
            ->call('deleteEligibleVoter');

        $this->assertDatabaseHas('eligible_voters', [
            'id' => $candidateVoter->id,
        ]);
    }

    public function test_detail_modal_loads_voter_data_and_missing_fields(): void
    {
        $voter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Detail Siswa Parsial',
            'study_program_id' => null,
            'date_of_birth' => null,
            'is_eligible' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openDetailModal', $voter->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedEligibleVoter.id', $voter->id)
            ->assertSee('DATA TIDAK LENGKAP')
            ->assertSee('Jurusan / Program Studi')
            ->assertSee('Tanggal Lahir')
            ->call('closeDetailModal')
            ->assertSet('showDetailModal', false);
    }

    public function test_incomplete_voter_without_dob_cannot_register(): void
    {
        EligibleVoter::create([
            'nim' => '2215354088',
            'name' => 'Siswa Tanpa Tanggal Lahir',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => null,
            'is_eligible' => true,
        ]);

        Livewire::test(AuthRegister::class)
            ->set('nim', '2215354088')
            ->set('birth_date', '2004-03-15')
            ->call('validateStudent')
            ->assertHasErrors(['nim'])
            ->assertSet('currentStep', 1);
    }

    public function test_voter_privacy_is_preserved_no_candidate_choice_exposed(): void
    {
        $election = $this->createElection();

        $voter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Mahasiswa Rahasia',
            'study_program_id' => $this->tiProdi->id,
            'date_of_birth' => '2004-03-15',
            'is_eligible' => true,
        ]);

        $account = VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $voter->id,
        ]);

        VotingParticipation::create([
            'election_id' => $election->id,
            'voter_account_id' => $account->id,
            'voted_at' => now(),
        ]);

        $component = Livewire::actingAs($this->admin)->test(EligibleVoterIndex::class);

        $html = $component->html();
        $this->assertStringNotContainsString('ballot', strtolower($html));
        $this->assertStringNotContainsString('candidate_pair_id', $html);
        $this->assertStringNotContainsString('pilihan paslon', strtolower($html));
    }

    public function test_row_numbering_follows_pagination_and_filters(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            EligibleVoter::create([
                'nim' => sprintf('221535%04d', $i),
                'name' => sprintf('Mahasiswa %02d', $i),
                'study_program_id' => $this->tiProdi->id,
                'date_of_birth' => '2004-03-15',
                'is_eligible' => true,
            ]);
        }

        $componentPage1 = Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('perPage', 25);

        $componentPage1->assertSee('#1');
        $componentPage1->assertSee('#25');
        $componentPage1->assertDontSee('#26');

        $componentPage2 = Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('perPage', 25)
            ->call('gotoPage', 2);

        $componentPage2->assertSee('#26');
        $componentPage2->assertSee('#30');

        $componentFiltered = Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('search', 'Mahasiswa 05');

        $componentFiltered->assertSee('#1');
        $componentFiltered->assertSee('2215350005');
    }

    public function test_filter_modal_opens_closes_and_calculates_counter_correctly(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->assertSet('showFilterModal', false)
            ->assertSee('FILTER')
            ->call('openFilterModal')
            ->assertSet('showFilterModal', true)
            ->assertSee('FILTER PEMILIH')
            ->set('completenessFilter', 'complete')
            ->set('registrationFilter', 'registered')
            ->assertSee('FILTER · 2')
            ->call('closeFilterModal')
            ->assertSet('showFilterModal', false);

        $this->assertSame(2, $component->get('activeFilterCount'));
    }

    public function test_clear_filter_resets_individual_filters(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->set('studyProgramFilter', $this->tiProdi->id)
            ->set('completenessFilter', 'incomplete')
            ->set('registrationFilter', 'unregistered')
            ->set('votingFilter', 'not_voted');

        $this->assertSame(4, $component->get('activeFilterCount'));

        $component->call('clearFilter', 'study_program');
        $this->assertNull($component->get('studyProgramFilter'));
        $this->assertSame(3, $component->get('activeFilterCount'));

        $component->call('clearFilter', 'completeness');
        $this->assertSame('all', $component->get('completenessFilter'));
        $this->assertSame(2, $component->get('activeFilterCount'));

        $component->call('clearFilter', 'registration');
        $this->assertSame('all', $component->get('registrationFilter'));
        $this->assertSame(1, $component->get('activeFilterCount'));

        $component->call('clearFilter', 'voting');
        $this->assertSame('all', $component->get('votingFilter'));
        $this->assertSame(0, $component->get('activeFilterCount'));
    }
}
