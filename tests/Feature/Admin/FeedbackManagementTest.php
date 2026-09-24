<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Feedbacks\Index as FeedbackIndex;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\Feedback;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeedbackManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superAdmin;

    private User $voterUser;

    private StudyProgram $studyProgram;

    private Election $election;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@pemira.test',
            'email_verified_at' => now(),
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create([
            'email' => 'superadmin@pemira.test',
            'email_verified_at' => now(),
        ]);

        $this->voterUser = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknologi Rekayasa Perangkat Lunak',
            'code' => 'TRPL',
        ]);

        $this->election = Election::create([
            'name' => 'PEMIRA 2026',
            'slug' => 'pemira-2026',
            'year' => 2026,
            'registration_start_at' => Carbon::now()->subDays(10),
            'registration_end_at' => Carbon::now()->subDays(2),
            'voting_start_at' => Carbon::now()->subDays(1),
            'voting_end_at' => Carbon::now()->addDays(1),
        ]);
    }

    private function createFeedback(int $rating, ?string $comment = null, ?string $voterName = 'Budi Santoso', ?string $nim = '2215354001', ?Election $election = null): Feedback
    {
        $user = User::factory()->create([
            'role' => 'voter',
            'email' => "voter_{$nim}@pnb.ac.id",
        ]);

        $eligibleVoter = EligibleVoter::create([
            'nim' => $nim,
            'name' => $voterName,
            'date_of_birth' => '2004-05-10',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $voterAccount = VoterAccount::create([
            'user_id' => $user->id,
            'eligible_voter_id' => $eligibleVoter->id,
        ]);

        $targetElection = $election ?? $this->election;

        return Feedback::create([
            'election_id' => $targetElection->id,
            'voter_account_id' => $voterAccount->id,
            'rating' => $rating,
            'comment' => $comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_admin_can_access_feedbacks_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/feedbacks');

        $response->assertOk();
        $response->assertSeeLivewire(FeedbackIndex::class);
        $response->assertSee('MASUKAN PEMILIH', false);
        $response->assertSee('SCOPE: ADMIN KPR (OPERASIONAL)', false);
    }

    public function test_legacy_masukan_pemilih_redirects_to_feedbacks(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/masukan-pemilih');

        $response->assertRedirect(route('admin.feedbacks.index'));
        $response->assertStatus(301);
    }

    public function test_super_admin_can_access_feedbacks_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/feedbacks');

        $response->assertOk();
        $response->assertSeeLivewire(FeedbackIndex::class);
        $response->assertSee('MASUKAN PEMILIH', false);
        $response->assertSee('SCOPE: SUPER ADMIN (FULL KONTROL)', false);
    }

    public function test_voter_cannot_access_feedbacks_page(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/admin/feedbacks');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/feedbacks');

        $response->assertRedirect('/login');
    }

    public function test_feedbacks_list_and_statistics_display_correctly(): void
    {
        $this->createFeedback(5, 'Aplikasi sangat intuitif dan mudah dipahami.', 'Ahmad Rizky', '2215354010');
        $this->createFeedback(4, 'Tampilan bagus, respon cepat.', 'Putu Ayu', '2215354020');
        $this->createFeedback(2, 'Agak lambat saat membuka detail paslon.', 'Made Wardana', '2215354030');
        $this->createFeedback(5, null, 'Ketut Sujana', '2215354040');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->assertSee('Ahmad Rizky')
            ->assertSee('Putu Ayu')
            ->assertSee('Made Wardana')
            ->assertSee('Ketut Sujana')
            ->assertSee('Aplikasi sangat intuitif')
            ->assertSee('Agak lambat saat membuka')
            ->assertSet('statistics.total', 4)
            ->assertSet('statistics.with_comment', 3)
            ->assertSet('statistics.positive', 3)
            ->assertSet('statistics.critical', 1)
            ->assertSet('statistics.average_rating', 4.0);
    }

    public function test_search_filters_feedbacks(): void
    {
        $this->createFeedback(5, 'Desain interface pemilihan sangat rapi', 'Ahmad Rizky', '2215354010');
        $this->createFeedback(3, 'Perlu perbaikan informasi visi misi', 'Putu Ayu', '2215354020');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->set('search', 'interface')
            ->assertSee('Ahmad Rizky')
            ->assertDontSee('Putu Ayu')
            ->set('search', '2215354020')
            ->assertSee('Putu Ayu')
            ->assertDontSee('Ahmad Rizky');
    }

    public function test_filter_by_rating(): void
    {
        $this->createFeedback(5, 'Bintang lima mantap', 'Ahmad Rizky', '2215354010');
        $this->createFeedback(2, 'Bintang dua kurang puas', 'Putu Ayu', '2215354020');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->set('ratingFilter', '5')
            ->assertSee('Ahmad Rizky')
            ->assertDontSee('Putu Ayu')
            ->set('ratingFilter', '2')
            ->assertSee('Putu Ayu')
            ->assertDontSee('Ahmad Rizky');
    }

    public function test_filter_by_type(): void
    {
        $this->createFeedback(5, 'Ada teks ulasan', 'Ahmad Rizky', '2215354010');
        $this->createFeedback(4, null, 'Putu Ayu', '2215354020');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->set('typeFilter', 'with_comment')
            ->assertSee('Ahmad Rizky')
            ->assertDontSee('Putu Ayu')
            ->set('typeFilter', 'rating_only')
            ->assertSee('Putu Ayu')
            ->assertDontSee('Ahmad Rizky');
    }

    public function test_filter_by_election(): void
    {
        $otherElection = Election::create([
            'name' => 'PEMIRA 2025',
            'slug' => 'pemira-2025',
            'year' => 2025,
            'registration_start_at' => Carbon::now()->subYear(),
            'registration_end_at' => Carbon::now()->subYear()->addDays(5),
            'voting_start_at' => Carbon::now()->subYear()->addDays(6),
            'voting_end_at' => Carbon::now()->subYear()->addDays(7),
        ]);

        $this->createFeedback(5, 'Ulasan 2026', 'Ahmad Rizky', '2215354010', $this->election);
        $this->createFeedback(4, 'Ulasan 2025', 'Putu Ayu', '2215354020', $otherElection);

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->set('electionFilter', (string) $this->election->id)
            ->assertSee('Ahmad Rizky')
            ->assertDontSee('Putu Ayu')
            ->set('electionFilter', (string) $otherElection->id)
            ->assertSee('Putu Ayu')
            ->assertDontSee('Ahmad Rizky');
    }

    public function test_active_filter_chips_and_reset(): void
    {
        $this->createFeedback(5, 'Ulasan aktif', 'Ahmad Rizky', '2215354010');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->set('ratingFilter', '5')
            ->set('typeFilter', 'with_comment')
            ->assertSet('activeFilterCount', 2)
            ->call('clearFilter', 'rating')
            ->assertSet('ratingFilter', 'all')
            ->assertSet('activeFilterCount', 1)
            ->call('resetFilters')
            ->assertSet('ratingFilter', 'all')
            ->assertSet('typeFilter', 'all')
            ->assertSet('activeFilterCount', 0);
    }

    public function test_filter_modal_opens_and_closes(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->assertSet('showFilterModal', false)
            ->call('openFilterModal')
            ->assertSet('showFilterModal', true)
            ->assertSee('FILTER MASUKAN PEMILIH')
            ->assertSee('PILIHAN FILTER')
            ->call('closeFilterModal')
            ->assertSet('showFilterModal', false);
    }

    public function test_detail_modal_opens_and_closes(): void
    {
        $feedback = $this->createFeedback(5, 'Komentar detail yang sangat lengkap', 'Ahmad Rizky', '2215354010');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->assertSet('showDetailModal', false)
            ->call('openDetailModal', $feedback->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedFeedbackId', $feedback->id)
            ->assertSee('Komentar detail yang sangat lengkap')
            ->assertSee('Ahmad Rizky')
            ->assertSee('2215354010')
            ->call('closeDetailModal')
            ->assertSet('showDetailModal', false)
            ->assertSet('selectedFeedbackId', null);
    }

    public function test_super_admin_can_delete_feedback_with_audit_log(): void
    {
        $feedback = $this->createFeedback(1, 'Komentar spam tidak pantas', 'Spam User', '2215354099');

        Livewire::actingAs($this->superAdmin)
            ->test(FeedbackIndex::class)
            ->call('openDeleteModal', $feedback->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('selectedFeedbackId', $feedback->id)
            ->call('deleteFeedback')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('feedbacks', [
            'id' => $feedback->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'feedback_deleted',
            'entity_id' => $feedback->id,
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_admin_cannot_delete_feedback(): void
    {
        $feedback = $this->createFeedback(3, 'Komentar biasa', 'Budi Santoso', '2215354055');

        Livewire::actingAs($this->admin)
            ->test(FeedbackIndex::class)
            ->call('openDeleteModal', $feedback->id)
            ->assertForbidden();

        $this->assertDatabaseHas('feedbacks', [
            'id' => $feedback->id,
        ]);
    }
}
