<?php

namespace Tests\Feature;

use App\Enums\ElectionPhase;
use App\Livewire\Admin\CandidatePairs\Index;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
        $response->assertSee($election->registration_start_at->format('d/m/Y H:i'));

        Carbon::setTestNow();
    }

    public function test_landing_page_formats_dates_in_indonesian_standard_with_leading_zeros(): void
    {
        $now = Carbon::parse('2026-09-01 07:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA Format Test 2026',
            'slug' => 'pemira-format-test-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::parse('2026-09-09 08:05:00'),
            'registration_end_at' => Carbon::parse('2026-09-19 16:00:00'),
            'voting_start_at' => Carbon::parse('2026-09-25 08:00:00'),
            'voting_end_at' => Carbon::parse('2026-09-25 16:00:00'),
        ]);

        $response = $this->get('/');
        $response->assertOk();

        $response->assertSee('09/09/2026 08:05');
        $response->assertSee('19/09/2026 16:00');
        $response->assertSee('25/09/2026 08:00');
        $response->assertSee('25/09/2026 16:00');

        $response->assertDontSee('09/09/2026 08:05 AM');
        $response->assertDontSee('09/19/2026');
        $response->assertDontSee('09/25/2026');

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

    public function test_landing_page_renders_empty_paslon_state_when_no_candidate_pairs_exist(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(2),
            'registration_end_at' => $now->copy()->addDays(5),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('BELUM ADA PASLON TERDAFTAR');
        $response->assertSee('Daftar pasangan calon untuk pemilihan ini belum ditetapkan oleh panitia KPR.');

        Carbon::setTestNow();
    }

    public function test_landing_page_displays_active_candidate_pairs_from_database(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(2),
            'registration_end_at' => $now->copy()->addDays(5),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $prodiTI = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);

        $prodiTE = StudyProgram::create([
            'name' => 'Teknik Elektro',
            'code' => 'TE',
        ]);

        $ketua = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Made Arya Wicaksana',
            'date_of_birth' => '2004-01-10',
            'study_program_id' => $prodiTI->id,
            'is_eligible' => true,
        ]);

        $wakil = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Ni Kadek Ayu Lestari',
            'date_of_birth' => '2004-02-15',
            'study_program_id' => $prodiTE->id,
            'is_eligible' => true,
        ]);

        $pair = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 1,
            'photo' => null,
            'vision' => 'Mewujudkan BEM PNB yang inklusif, inovatif, dan berintegritas.',
            'mission' => "1. Meningkatkan advokasi mahasiswa.\n2. Mengoptimalkan kegiatan riset dan teknologi.",
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $ketua->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $wakil->id,
            'position' => 'wakil',
        ]);

        $response = $this->get('/');
        $response->assertOk();

        $response->assertSee('PASLON 01');
        $response->assertSee('I Made Arya Wicaksana');
        $response->assertSee('Ni Kadek Ayu Lestari');
        $response->assertSee('Teknologi Informasi');
        $response->assertSee('Teknik Elektro');
        $response->assertSee(route('public.candidates.show', $pair));
        $response->assertDontSee('BELUM ADA PASLON TERDAFTAR');

        $detailResponse = $this->get('/paslon/paslon-01');
        $detailResponse->assertOk();
        $detailResponse->assertSee('Mewujudkan BEM PNB yang inklusif, inovatif, dan berintegritas.');

        Carbon::setTestNow();
    }

    public function test_public_candidate_detail_page_renders_successfully_for_active_candidate(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(2),
            'registration_end_at' => $now->copy()->addDays(5),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $prodi = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);

        $ketua = EligibleVoter::create([
            'nim' => '2215354001',
            'name' => 'I Made Arya Wicaksana',
            'date_of_birth' => '2004-01-10',
            'study_program_id' => $prodi->id,
            'is_eligible' => true,
        ]);

        $wakil = EligibleVoter::create([
            'nim' => '2215354002',
            'name' => 'Ni Kadek Ayu Lestari',
            'date_of_birth' => '2004-02-15',
            'study_program_id' => $prodi->id,
            'is_eligible' => true,
        ]);

        $pair = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 1,
            'photo' => null,
            'vision' => 'Visi Unggulan Paslon 01',
            'mission' => "1. Misi Utama Advokasi\n2. Misi Digitalisasi Kampus",
            'is_active' => true,
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $ketua->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $pair->id,
            'eligible_voter_id' => $wakil->id,
            'position' => 'wakil',
        ]);

        $response = $this->get('/paslon/paslon-01');
        $response->assertOk();

        $response->assertSee('PASLON 01');
        $response->assertSee('KANDIDAT BEM');
        $response->assertSee('I Made Arya Wicaksana');
        $response->assertSee('2215354001');
        $response->assertSee('Ni Kadek Ayu Lestari');
        $response->assertSee('2215354002');
        $response->assertSee('Teknologi Informasi');
        $response->assertSee('Visi Unggulan Paslon 01');
        $response->assertSee('1. Misi Utama Advokasi');
        $response->assertSee('2. Misi Digitalisasi Kampus');
        $response->assertSee('KEMBALI KE PASLON');
        $response->assertSee(route('home').'#paslon');

        $responseNum = $this->get('/paslon/1');
        $responseNum->assertOk();

        Carbon::setTestNow();
    }

    public function test_public_candidate_detail_page_returns_404_for_inactive_or_nonexistent_candidate(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(2),
            'registration_end_at' => $now->copy()->addDays(5),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $inactivePair = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 2,
            'photo' => null,
            'vision' => 'Visi Inaktif',
            'mission' => 'Misi Inaktif',
            'is_active' => false,
        ]);

        $response = $this->get('/paslon/paslon-02');
        $response->assertNotFound();

        $response404 = $this->get('/paslon/paslon-99');
        $response404->assertNotFound();

        Carbon::setTestNow();
    }

    public function test_landing_page_hides_inactive_candidate_pairs(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(2),
            'registration_end_at' => $now->copy()->addDays(5),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $prodi = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);

        $voter1 = EligibleVoter::create([
            'nim' => '2215354099',
            'name' => 'Kandidat Nonaktif Ketua',
            'date_of_birth' => '2004-01-10',
            'study_program_id' => $prodi->id,
            'is_eligible' => true,
        ]);

        $voter2 = EligibleVoter::create([
            'nim' => '2215354098',
            'name' => 'Kandidat Nonaktif Wakil',
            'date_of_birth' => '2004-02-15',
            'study_program_id' => $prodi->id,
            'is_eligible' => true,
        ]);

        $inactivePair = CandidatePair::create([
            'election_id' => $election->id,
            'candidate_number' => 2,
            'photo' => null,
            'vision' => 'Visi Paslon Nonaktif',
            'mission' => 'Misi Paslon Nonaktif',
            'is_active' => false,
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $inactivePair->id,
            'eligible_voter_id' => $voter1->id,
            'position' => 'ketua',
        ]);

        CandidateMember::create([
            'election_id' => $election->id,
            'candidate_pair_id' => $inactivePair->id,
            'eligible_voter_id' => $voter2->id,
            'position' => 'wakil',
        ]);

        $response = $this->get('/');
        $response->assertOk();

        $response->assertDontSee('PASLON 02');
        $response->assertDontSee('Kandidat Nonaktif Ketua');
        $response->assertDontSee('Kandidat Nonaktif Wakil');
        $response->assertSee('BELUM ADA PASLON TERDAFTAR');

        Carbon::setTestNow();
    }

    public function test_admin_paslon_management_actions_directly_synchronize_with_landing_page(): void
    {
        $now = Carbon::parse('2026-09-18 10:00:00');
        Carbon::setTestNow($now);

        $admin = User::factory()->admin()->create();

        $election = Election::create([
            'name' => 'PEMIRA BEM PNB 2026',
            'slug' => 'pemira-bem-pnb-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->addDays(2),
            'registration_end_at' => $now->copy()->addDays(5),
            'voting_start_at' => $now->copy()->addDays(10),
            'voting_end_at' => $now->copy()->addDays(11),
        ]);

        $prodi = StudyProgram::create([
            'name' => 'Teknologi Informasi',
            'code' => 'TI',
        ]);

        $voterKetua = EligibleVoter::create([
            'nim' => '2215354010',
            'name' => 'Ketua Terpilih Test',
            'date_of_birth' => '2004-01-10',
            'study_program_id' => $prodi->id,
            'is_eligible' => true,
        ]);

        $voterWakil = EligibleVoter::create([
            'nim' => '2215354011',
            'name' => 'Wakil Terpilih Test',
            'date_of_birth' => '2004-02-15',
            'study_program_id' => $prodi->id,
            'is_eligible' => true,
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('BELUM ADA PASLON TERDAFTAR');

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('selectedElectionId', $election->id)
            ->call('openCreateModal')
            ->set('candidate_number', 1)
            ->set('leader_id', $voterKetua->id)
            ->set('vice_leader_id', $voterWakil->id)
            ->set('vision', 'Visi Sinergi Kolaboratif 2026')
            ->set('mission', 'Misi Pengabdian Mahasiswa PNB')
            ->call('createCandidatePair')
            ->assertHasNoErrors();

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('PASLON 01');
        $response->assertSee('Ketua Terpilih Test');
        $response->assertSee('Wakil Terpilih Test');
        $response->assertSee(route('public.candidates.show', 'paslon-01'));
        $response->assertDontSee('BELUM ADA PASLON TERDAFTAR');

        $detailResponse = $this->get('/paslon/paslon-01');
        $detailResponse->assertOk();
        $detailResponse->assertSee('Visi Sinergi Kolaboratif 2026');
        $detailResponse->assertSee('Misi Pengabdian Mahasiswa PNB');

        $pair = CandidatePair::where('election_id', $election->id)->first();
        $this->assertNotNull($pair);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('selectedElectionId', $election->id)
            ->call('toggleStatus', $pair->id);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('Ketua Terpilih Test');
        $response->assertSee('BELUM ADA PASLON TERDAFTAR');

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('selectedElectionId', $election->id)
            ->call('toggleStatus', $pair->id)
            ->call('openEditModal', $pair->id)
            ->set('vision', 'Visi Terupdate Transformasi Digital')
            ->call('updateCandidatePair')
            ->assertHasNoErrors();

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Ketua Terpilih Test');

        $detailResponse = $this->get('/paslon/paslon-01');
        $detailResponse->assertOk();
        $detailResponse->assertSee('Visi Terupdate Transformasi Digital');

        Carbon::setTestNow();
    }
}
