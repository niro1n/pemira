<?php

namespace Tests\Feature\Voter;

use App\Livewire\Voter\Dashboard;
use App\Livewire\Voter\VotingBooth;
use App\Livewire\Voter\VotingSuccess;
use App\Models\AuditLog;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\Feedback;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use App\Models\VotingParticipation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class VoterPortalTest extends TestCase
{
    use RefreshDatabase;

    private StudyProgram $studyProgram;

    private Election $election;

    private User $voterUser;

    private VoterAccount $voterAccount;

    private EligibleVoter $eligibleVoter;

    private CandidatePair $candidate1;

    private CandidatePair $candidate2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknologi Rekayasa Perangkat Lunak',
            'code' => 'TRPL',
        ]);

        $this->eligibleVoter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Made Mahardika',
            'date_of_birth' => '2004-01-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $this->voterUser = User::create([
            'email' => 'mahardika@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $this->voterAccount = VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $this->eligibleVoter->id,
        ]);

        $this->election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => now()->subDays(5),
            'registration_end_at' => now()->subDays(2),
            'voting_start_at' => now()->subHour(),
            'voting_end_at' => now()->addHours(6),
        ]);

        $this->candidate1 = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 1,
            'vision' => 'Mewujudkan BEM yang adaptif dan solutif',
            'mission' => 'Memperkuat aspirasi mahasiswa',
            'is_active' => true,
        ]);

        $evKetua1 = EligibleVoter::create([
            'nim' => '2215354010',
            'name' => 'Ketua Satu',
            'date_of_birth' => '2003-02-10',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);
        $evWakil1 = EligibleVoter::create([
            'nim' => '2215354011',
            'name' => 'Wakil Satu',
            'date_of_birth' => '2003-03-12',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);
        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate1->id,
            'eligible_voter_id' => $evKetua1->id,
            'position' => 'ketua',
        ]);
        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate1->id,
            'eligible_voter_id' => $evWakil1->id,
            'position' => 'wakil',
        ]);

        $this->candidate2 = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 2,
            'vision' => 'PNB Maju dan Kolaboratif',
            'mission' => 'Inovasi teknologi untuk kampus',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_voter_routes(): void
    {
        $this->get('/voter')->assertRedirect('/login');
        $this->get('/voter/voting')->assertRedirect('/login');
        $this->get('/voter/success')->assertRedirect('/login');
        $this->get('/voter/profile')->assertRedirect('/login');
    }

    public function test_admin_and_super_admin_cannot_access_voter_portal(): void
    {
        $admin = User::create([
            'email' => 'admin@pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $superAdmin = User::create([
            'email' => 'superadmin@pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)->get('/voter')->assertForbidden();
        $this->actingAs($superAdmin)->get('/voter')->assertForbidden();
    }

    public function test_voter_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->voterUser)->get('/admin')->assertForbidden();
    }

    public function test_public_profile_redirects_voter_to_voter_profile(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/profile');

        $response->assertRedirect(route('voter.profile'));
    }

    public function test_voter_can_access_dashboard_and_see_dpt_info(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/voter');

        $response->assertOk();
        $response->assertSee('I Made Mahardika');
        $response->assertSee('2215354001');
        $response->assertSee('PEMIRA BEM PNB 2026');
        $response->assertSee('DPT ELIGIBLE');
    }

    public function test_voter_cannot_access_booth_when_not_eligible(): void
    {
        $this->eligibleVoter->update(['is_eligible' => false]);

        Livewire::actingAs($this->voterUser)
            ->test(VotingBooth::class)
            ->assertRedirect(route('voter.dashboard'));
    }

    public function test_voter_cannot_access_booth_when_election_not_in_voting_phase(): void
    {
        $this->election->update([
            'voting_start_at' => now()->addDay(),
            'voting_end_at' => now()->addDays(2),
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(VotingBooth::class)
            ->assertRedirect(route('voter.dashboard'));
    }

    public function test_voter_can_access_voting_booth_when_active_and_eligible(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/voter/voting');

        $response->assertOk();
        $response->assertSee('Bilik Suara Digital');
        $response->assertSee('PILIH PASLON 01');
        $response->assertSee('PILIH PASLON 02');
    }

    public function test_voter_cannot_select_inactive_candidate(): void
    {
        $inactiveCandidate = CandidatePair::create([
            'election_id' => $this->election->id,
            'candidate_number' => 3,
            'vision' => 'Visi Inaktif',
            'mission' => 'Misi Inaktif',
            'is_active' => false,
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(VotingBooth::class)
            ->call('selectCandidate', $inactiveCandidate->id)
            ->assertSet('selectedCandidatePairId', null);
    }

    public function test_voter_cannot_select_candidate_from_another_election(): void
    {
        $otherElection = Election::create([
            'name' => 'Pemira Jurusan Lain',
            'slug' => 'pemira-jurusan-lain',
            'year' => 2026,
            'registration_start_at' => now()->subDays(5),
            'registration_end_at' => now()->subDays(2),
            'voting_start_at' => now()->subHour(),
            'voting_end_at' => now()->addHours(6),
        ]);

        $candidateOther = CandidatePair::create([
            'election_id' => $otherElection->id,
            'candidate_number' => 1,
            'vision' => 'Visi Jurusan Lain',
            'mission' => 'Misi Jurusan Lain',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(VotingBooth::class)
            ->call('selectCandidate', $candidateOther->id)
            ->assertSet('selectedCandidatePairId', null);
    }

    public function test_voter_can_submit_vote_atomically_and_anonymously(): void
    {
        $this->assertEquals(0, VotingParticipation::count());
        $this->assertEquals(0, DB::table('ballots')->count());

        Livewire::actingAs($this->voterUser)
            ->test(VotingBooth::class)
            ->call('selectCandidate', $this->candidate1->id)
            ->assertSet('selectedCandidatePairId', $this->candidate1->id)
            ->call('submitVote')
            ->assertRedirect(route('voter.success'));

        $this->assertEquals(1, VotingParticipation::count());
        $participation = VotingParticipation::first();
        $this->assertEquals($this->election->id, $participation->election_id);
        $this->assertEquals($this->voterAccount->id, $participation->voter_account_id);

        $this->assertEquals(1, DB::table('ballots')->count());
        $ballot = DB::table('ballots')->first();
        $this->assertEquals($this->election->id, $ballot->election_id);
        $this->assertEquals($this->candidate1->id, $ballot->candidate_pair_id);

        $this->assertFalse(Schema::hasColumn('ballots', 'user_id'));
        $this->assertFalse(Schema::hasColumn('ballots', 'voter_account_id'));
        $this->assertFalse(Schema::hasColumn('ballots', 'eligible_voter_id'));

        $auditLog = AuditLog::where('action', 'voting')->first();
        $this->assertNotNull($auditLog);
        $this->assertEquals($this->voterUser->id, $auditLog->user_id);
        $this->assertStringNotContainsString((string) $this->candidate1->id, $auditLog->description);
    }

    public function test_duplicate_vote_is_strictly_rejected(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
        ]);
        DB::table('ballots')->insert([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate1->id,
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(VotingBooth::class)
            ->assertRedirect(route('voter.dashboard'));

        $this->assertEquals(1, VotingParticipation::count());
        $this->assertEquals(1, DB::table('ballots')->count());
    }

    public function test_voter_cannot_access_booth_after_voting(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
        ]);

        $response = $this->actingAs($this->voterUser)->get('/voter/voting');

        $response->assertRedirect(route('voter.dashboard'));
    }

    public function test_voter_cannot_access_success_page_before_voting(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/voter/success');

        $response->assertRedirect(route('voter.dashboard'));
    }

    public function test_voter_can_access_success_page_after_voting(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
        ]);

        $response = $this->actingAs($this->voterUser)->get('/voter/success');

        $response->assertOk();
        $response->assertSee('Suara Anda Berhasil Dicatat!');
        $response->assertSee('Tanda Bukti Partisipasi Pemilih');
        $response->assertSee('I Made Mahardika');

        $response->assertDontSee('Ketua Satu');
        $response->assertDontSee('Paslon 01 Terpilih');
        $response->assertDontSee('Anda memilih Paslon');
    }

    public function test_voter_can_submit_optional_feedback_on_success_page(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
        ]);

        $this->assertEquals(0, Feedback::count());

        Livewire::actingAs($this->voterUser)
            ->test(VotingSuccess::class)
            ->call('setRating', 4)
            ->set('comment', 'Sistem pemilihan digital sangat praktis dan cepat!')
            ->call('submitFeedback')
            ->assertSet('feedbackSubmitted', true);

        $this->assertEquals(1, Feedback::count());
        $feedback = Feedback::first();
        $this->assertEquals(4, $feedback->rating);
        $this->assertEquals('Sistem pemilihan digital sangat praktis dan cepat!', $feedback->comment);
        $this->assertEquals($this->voterAccount->id, $feedback->voter_account_id);
    }

    public function test_voter_can_view_own_profile_with_dpt_info(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
        ]);

        $response = $this->actingAs($this->voterUser)->get('/voter/profile');

        $response->assertOk();
        $response->assertSee('Profil Identitas Pemilih');
        $response->assertSee('I Made Mahardika');
        $response->assertSee('2215354001');
        $response->assertSee('Teknologi Rekayasa Perangkat Lunak');
        $response->assertSee('ELIGIBLE / HAK PILIH AKTIF');
        $response->assertSee('Riwayat Partisipasi Pemilihan');
        $response->assertSee('PEMIRA BEM PNB 2026');
    }

    public function test_dashboard_displays_no_election_state_when_no_active_election(): void
    {
        Election::query()->delete();

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Tidak Ada Pemilihan Aktif')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_upcoming_state_without_voting_cta(): void
    {
        $this->election->update([
            'registration_start_at' => now()->addDays(2),
            'registration_end_at' => now()->addDays(4),
            'voting_start_at' => now()->addDays(5),
            'voting_end_at' => now()->addDays(6),
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('AKAN DATANG')
            ->assertSee('Bilik Suara Belum Dibuka')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_registration_state_without_voting_cta(): void
    {
        $this->election->update([
            'registration_start_at' => now()->subDay(),
            'registration_end_at' => now()->addDays(2),
            'voting_start_at' => now()->addDays(3),
            'voting_end_at' => now()->addDays(4),
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('PENDAFTARAN')
            ->assertSee('Bilik Suara Belum Dibuka')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_finished_state_when_not_voted(): void
    {
        $this->election->update([
            'registration_start_at' => now()->subDays(6),
            'registration_end_at' => now()->subDays(4),
            'voting_start_at' => now()->subDays(3),
            'voting_end_at' => now()->subDay(),
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('SELESAI')
            ->assertSee('Pemilihan Telah Selesai')
            ->assertSee('Masa pemungutan suara telah resmi ditutup')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_finished_state_when_already_voted(): void
    {
        $this->election->update([
            'registration_start_at' => now()->subDays(6),
            'registration_end_at' => now()->subDays(4),
            'voting_start_at' => now()->subDays(3),
            'voting_end_at' => now()->subDay(),
        ]);

        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now()->subDays(2),
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('SELESAI')
            ->assertSee('Pemilihan Telah Selesai')
            ->assertSee('Terima kasih atas partisipasi Anda')
            ->assertSee('Bukti Partisipasi')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_missing_voter_account_state(): void
    {
        $unlinkedVoter = User::create([
            'email' => 'unlinked@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($unlinkedVoter)
            ->test(Dashboard::class)
            ->assertSee('Akun Belum Terhubung ke DPT')
            ->assertSee('DATA BELUM TERHUBUNG')
            ->assertDontSee('Masuk Bilik Suara')
            ->assertDontSee('Status DPT: Tidak Memenuhi Syarat (Non-Eligible)');
    }

    public function test_dashboard_displays_non_eligible_voter_state(): void
    {
        $this->eligibleVoter->update(['is_eligible' => false]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Status DPT: Tidak Memenuhi Syarat (Non-Eligible)')
            ->assertSee('TIDAK ELIGIBLE')
            ->assertDontSee('Masuk Bilik Suara')
            ->assertDontSee('Akun Belum Terhubung ke DPT');
    }

    public function test_dashboard_displays_already_voted_state_and_hides_booth_cta(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Hak Suara Anda Telah Digunakan')
            ->assertSee('Bukti Partisipasi')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_unavailable_state_when_active_candidates_is_zero(): void
    {
        $this->candidate1->update(['is_active' => false]);
        $this->candidate2->update(['is_active' => false]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Tidak ada pasangan calon aktif yang siap dipilih saat ini')
            ->assertDontSee('Masuk Bilik Suara');
    }

    public function test_dashboard_displays_voting_cta_when_all_conditions_met(): void
    {
        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Bilik Suara Sedang Dibuka!')
            ->assertSee('Masuk Bilik Suara');
    }
}
