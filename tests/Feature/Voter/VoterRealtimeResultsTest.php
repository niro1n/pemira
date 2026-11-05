<?php

namespace Tests\Feature\Voter;

use App\Livewire\Admin\SpecialActions\Index as SpecialActionsIndex;
use App\Livewire\Voter\Dashboard;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VoterAccount;
use App\Models\VotingParticipation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class VoterRealtimeResultsTest extends TestCase
{
    use RefreshDatabase;

    private StudyProgram $studyProgram;

    private Election $election;

    private User $voterUser;

    private VoterAccount $voterAccount;

    private EligibleVoter $eligibleVoter;

    private CandidatePair $candidate1;

    private CandidatePair $candidate2;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);

        $this->eligibleVoter = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'Irma Suryani',
            'date_of_birth' => '2004-01-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $this->voterUser = User::create([
            'email' => 'irma@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $this->voterAccount = VoterAccount::create([
            'user_id' => $this->voterUser->id,
            'eligible_voter_id' => $this->eligibleVoter->id,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
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
            'vision' => 'Visi Paslon 1',
            'mission' => 'Misi Paslon 1',
            'is_active' => true,
        ]);

        $evKetua1 = EligibleVoter::create([
            'nim' => '2215354010',
            'name' => 'Calon Ketua 1',
            'date_of_birth' => '2003-02-10',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $evWakil1 = EligibleVoter::create([
            'nim' => '2215354011',
            'name' => 'Calon Wakil 1',
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
            'vision' => 'Visi Paslon 2',
            'mission' => 'Misi Paslon 2',
            'is_active' => true,
        ]);

        $evKetua2 = EligibleVoter::create([
            'nim' => '2215354020',
            'name' => 'Calon Ketua 2',
            'date_of_birth' => '2003-04-10',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $evWakil2 = EligibleVoter::create([
            'nim' => '2215354021',
            'name' => 'Calon Wakil 2',
            'date_of_birth' => '2003-05-12',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate2->id,
            'eligible_voter_id' => $evKetua2->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate2->id,
            'eligible_voter_id' => $evWakil2->id,
            'position' => 'wakil',
        ]);

        SystemSetting::setRealtimeResultsEnabled(true);
    }

    public function test_voter_can_see_realtime_results_on_dashboard_during_voting_phase(): void
    {
        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Hasil Perhitungan Suara Pemira')
            ->assertSee('LIVE COUNT SEMENTARA')
            ->assertSee('PASLON #01')
            ->assertSee('Calon Ketua 1')
            ->assertSee('PASLON #02')
            ->assertSee('Calon Ketua 2')
            ->assertSee('0 suara');
    }

    public function test_voter_who_has_voted_sees_realtime_results_and_confirmation_message(): void
    {
        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        DB::table('ballots')->insert([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate1->id,
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Hasil Perhitungan Suara Pemira')
            ->assertSee('Hak suara Anda telah sah digunakan dan terakumulasi dalam perhitungan di bawah ini secara anonim.')
            ->assertSee('1 suara')
            ->assertSee('100,00%');
    }

    public function test_voter_sees_highest_vote_badge_when_election_is_finished(): void
    {
        $this->election->update([
            'voting_start_at' => now()->subHours(5),
            'voting_end_at' => now()->subHour(),
        ]);

        VotingParticipation::create([
            'election_id' => $this->election->id,
            'voter_account_id' => $this->voterAccount->id,
            'voted_at' => now()->subHours(2),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        DB::table('ballots')->insert([
            'id' => (string) Str::uuid(),
            'election_id' => $this->election->id,
            'candidate_pair_id' => $this->candidate1->id,
        ]);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Hasil Perhitungan Suara Pemira')
            ->assertSee('HASIL AKHIR PERHITUNGAN RESMI')
            ->assertSee('PEROLEHAN SUARA TERTINGGI');
    }

    public function test_voter_does_not_see_results_when_realtime_results_are_disabled(): void
    {
        SystemSetting::setRealtimeResultsEnabled(false);

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertDontSee('Hasil Perhitungan Suara Pemira')
            ->assertDontSee('LIVE COUNT SEMENTARA');
    }

    public function test_super_admin_can_toggle_realtime_results_setting(): void
    {
        $this->assertTrue(SystemSetting::isRealtimeResultsEnabled());

        Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionsIndex::class)
            ->call('toggleRealtimeResults', false)
            ->assertSet('isRealtimeResultsEnabled', false);

        $this->assertFalse(SystemSetting::isRealtimeResultsEnabled());

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertDontSee('Hasil Perhitungan Suara Pemira');

        Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionsIndex::class)
            ->call('toggleRealtimeResults', true)
            ->assertSet('isRealtimeResultsEnabled', true);

        $this->assertTrue(SystemSetting::isRealtimeResultsEnabled());

        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->assertSee('Hasil Perhitungan Suara Pemira');
    }

    public function test_refresh_results_action_works(): void
    {
        Livewire::actingAs($this->voterUser)
            ->test(Dashboard::class)
            ->call('refreshResults')
            ->assertHasNoErrors()
            ->assertSee('Hasil Perhitungan Suara Pemira');
    }
}
