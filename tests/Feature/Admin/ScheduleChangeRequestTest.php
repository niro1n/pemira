<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Elections\Index as ElectionIndex;
use App\Livewire\Admin\ScheduleRequests\Index as ScheduleRequestIndex;
use App\Models\Election;
use App\Models\ScheduleChangeRequest;
use App\Models\User;
use App\Services\Dashboard\DashboardDataProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ScheduleChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superAdmin;

    private User $voter;

    private Election $election;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'kpr@pemira.test',
            'email_verified_at' => now(),
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create([
            'email' => 'super@pemira.test',
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
            'registration_start_at' => Carbon::now()->subDays(5),
            'registration_end_at' => Carbon::now()->subDays(2),
            'voting_start_at' => Carbon::now()->addDays(1)->setHour(8)->setMinute(0),
            'voting_end_at' => Carbon::now()->addDays(1)->setHour(16)->setMinute(0),
        ]);
    }

    public function test_admin_can_access_schedule_requests_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pengajuan-jadwal');

        $response->assertOk();
        $response->assertSeeLivewire(ScheduleRequestIndex::class);
        $response->assertSee('PENGAJUAN PERUBAHAN JADWAL', false);
    }

    public function test_super_admin_can_access_schedule_requests_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/pengajuan-jadwal');

        $response->assertOk();
        $response->assertSeeLivewire(ScheduleRequestIndex::class);
        $response->assertSee('PERSETUJUAN PERUBAHAN JADWAL', false);
    }

    public function test_voter_cannot_access_schedule_requests_page(): void
    {
        $response = $this->actingAs($this->voter)->get('/admin/pengajuan-jadwal');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/pengajuan-jadwal');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_schedule_change_request_with_valid_data(): void
    {
        $newStart = Carbon::now()->addDays(2)->setHour(9)->setMinute(0)->format('Y-m-d\TH:i');
        $newEnd = Carbon::now()->addDays(2)->setHour(17)->setMinute(0)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openCreateModal', $this->election->id)
            ->assertSet('showCreateModal', true)
            ->assertSet('election_id', $this->election->id)
            ->set('new_voting_start_at', $newStart)
            ->set('new_voting_end_at', $newEnd)
            ->set('reason', 'Penundaan jadwal pemungutan suara karena perbaikan infrastruktur jaringan kampus.')
            ->call('createRequest')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('schedule_change_requests', [
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'status' => 'pending',
            'reason' => 'Penundaan jadwal pemungutan suara karena perbaikan infrastruktur jaringan kampus.',
        ]);

        $this->election->refresh();
        $this->assertNotEquals($newStart, $this->election->voting_start_at->format('Y-m-d\TH:i'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'schedule_change_requested',
            'entity_type' => 'ScheduleChangeRequest',
        ]);
    }

    public function test_duplicate_pending_request_for_same_election_is_rejected(): void
    {
        ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan pending pertama yang belum disetujui.',
            'status' => 'pending',
        ]);

        $newStart = Carbon::now()->addDays(4)->setHour(9)->setMinute(0)->format('Y-m-d\TH:i');
        $newEnd = Carbon::now()->addDays(4)->setHour(17)->setMinute(0)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openCreateModal', $this->election->id)
            ->set('election_id', $this->election->id)
            ->set('new_voting_start_at', $newStart)
            ->set('new_voting_end_at', $newEnd)
            ->set('reason', 'Pengajuan kedua yang harus ditolak karena masih ada pending.')
            ->call('createRequest')
            ->assertHasErrors(['election_id']);
    }

    public function test_schedule_request_validation_rules(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openCreateModal', $this->election->id)
            ->set('election_id', $this->election->id)
            ->set('new_voting_start_at', '2026-10-10T16:00')
            ->set('new_voting_end_at', '2026-10-10T08:00')
            ->set('reason', 'pendek')
            ->call('createRequest')
            ->assertHasErrors(['new_voting_end_at', 'reason']);
    }

    public function test_new_voting_start_cannot_precede_registration_end(): void
    {
        $invalidStart = $this->election->registration_end_at->subHour()->format('Y-m-d\TH:i');
        $validEnd = Carbon::now()->addDays(5)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openCreateModal', $this->election->id)
            ->set('election_id', $this->election->id)
            ->set('new_voting_start_at', $invalidStart)
            ->set('new_voting_end_at', $validEnd)
            ->set('reason', 'Pengajuan jadwal voting yang mendahului pendaftaran.')
            ->call('createRequest')
            ->assertHasErrors(['new_voting_start_at']);
    }

    public function test_admin_can_edit_own_pending_request(): void
    {
        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Alasan lama sebelum dilakukan edit.',
            'status' => 'pending',
        ]);

        $revisedReason = 'Alasan baru yang lebih lengkap dan telah direvisi.';
        $revisedEnd = Carbon::now()->addDays(4)->setHour(18)->setMinute(0)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openEditModal', $request->id)
            ->assertSet('showEditModal', true)
            ->set('reason', $revisedReason)
            ->set('new_voting_end_at', $revisedEnd)
            ->call('updateRequest')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('schedule_change_requests', [
            'id' => $request->id,
            'reason' => $revisedReason,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'schedule_change_updated',
        ]);
    }

    public function test_admin_can_cancel_own_pending_request(): void
    {
        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Permohonan yang akan dibatalkan oleh pengaju.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openDeleteModal', $request->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteRequest')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('schedule_change_requests', [
            'id' => $request->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'schedule_change_deleted',
        ]);
    }

    public function test_super_admin_can_approve_pending_request(): void
    {
        $newStart = Carbon::now()->addDays(5)->setHour(8)->setMinute(0);
        $newEnd = Carbon::now()->addDays(5)->setHour(17)->setMinute(0);

        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => $newStart,
            'new_voting_end_at' => $newEnd,
            'reason' => 'Permintaan perpanjangan jadwal karena kegiatan kampus.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ScheduleRequestIndex::class)
            ->call('openApproveModal', $request->id)
            ->assertSet('showApproveModal', true)
            ->call('approveRequest')
            ->assertSet('showApproveModal', false);

        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($this->superAdmin->id, $request->approved_by);
        $this->assertNotNull($request->approved_at);

        $this->election->refresh();
        $this->assertEquals($newStart->toDateTimeString(), $this->election->voting_start_at->toDateTimeString());
        $this->assertEquals($newEnd->toDateTimeString(), $this->election->voting_end_at->toDateTimeString());

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'schedule_change_approved',
        ]);
    }

    public function test_admin_cannot_approve_request(): void
    {
        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan yang tidak boleh di-approve oleh admin kpr.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openApproveModal', $request->id)
            ->assertForbidden();
    }

    public function test_super_admin_can_reject_pending_request(): void
    {
        $originalStart = $this->election->voting_start_at->copy();
        $originalEnd = $this->election->voting_end_at->copy();

        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan yang akan ditolak oleh super admin.',
            'status' => 'pending',
        ]);

        $rejectNote = 'Waktu yang diajukan berbenturan dengan hari libur nasional.';

        Livewire::actingAs($this->superAdmin)
            ->test(ScheduleRequestIndex::class)
            ->call('openRejectModal', $request->id)
            ->assertSet('showRejectModal', true)
            ->set('review_note', $rejectNote)
            ->call('rejectRequest')
            ->assertHasNoErrors()
            ->assertSet('showRejectModal', false);

        $request->refresh();
        $this->assertEquals('rejected', $request->status);
        $this->assertEquals($this->superAdmin->id, $request->approved_by);
        $this->assertEquals($rejectNote, $request->review_note);

        $this->election->refresh();
        $this->assertEquals($originalStart->toDateTimeString(), $this->election->voting_start_at->toDateTimeString());
        $this->assertEquals($originalEnd->toDateTimeString(), $this->election->voting_end_at->toDateTimeString());

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'schedule_change_rejected',
        ]);
    }

    public function test_admin_cannot_reject_request(): void
    {
        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan yang tidak boleh di-reject oleh admin kpr.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ScheduleRequestIndex::class)
            ->call('openRejectModal', $request->id)
            ->assertForbidden();
    }

    public function test_already_processed_request_cannot_be_approved_again(): void
    {
        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan yang sudah approved sebelumnya.',
            'status' => 'approved',
            'approved_by' => $this->superAdmin->id,
            'approved_at' => Carbon::now(),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ScheduleRequestIndex::class)
            ->call('openApproveModal', $request->id)
            ->assertSet('showApproveModal', false);
    }

    public function test_dashboard_pending_count_reflects_actual_pending_requests(): void
    {
        $provider = app(DashboardDataProvider::class);

        $systemInfo = $provider->getSystemInfo($this->superAdmin);
        $this->assertEquals(0, $systemInfo['pending_schedule_requests']);

        ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan pending penambah counter.',
            'status' => 'pending',
        ]);

        $systemInfo = $provider->getSystemInfo($this->superAdmin);
        $this->assertEquals(1, $systemInfo['pending_schedule_requests']);
    }

    public function test_admin_changing_schedule_when_voting_is_not_yet_active_requires_request(): void
    {
        $newStart = Carbon::now()->addDays(3)->setHour(8)->setMinute(0)->format('Y-m-d\TH:i');
        $newEnd = Carbon::now()->addDays(3)->setHour(18)->setMinute(0)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $this->election->id)
            ->assertSet('isVotingActive', false)
            ->set('voting_start_at', $newStart)
            ->set('voting_end_at', $newEnd)
            ->call('updateElection')
            ->assertHasErrors(['scheduleChangeReason']);

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $this->election->id)
            ->set('voting_start_at', $newStart)
            ->set('voting_end_at', $newEnd)
            ->set('scheduleChangeReason', 'Perubahan jadwal sebelum masa voting dimulai karena sinkronisasi kalender.')
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->election->refresh();
        $this->assertNotEquals($newStart, $this->election->voting_start_at->format('Y-m-d\TH:i'));

        $this->assertDatabaseHas('schedule_change_requests', [
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'status' => 'pending',
            'reason' => 'Perubahan jadwal sebelum masa voting dimulai karena sinkronisasi kalender.',
        ]);
    }

    public function test_admin_cannot_directly_update_schedule_when_voting_is_active(): void
    {
        $now = Carbon::parse('2026-10-15 10:00:00');
        Carbon::setTestNow($now);

        $activeElection = Election::create([
            'name' => 'PEMIRA Aktif',
            'slug' => 'pemira-aktif-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(1),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(4),
        ]);

        $this->assertTrue($activeElection->isVotingActive($now));

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $activeElection->id)
            ->assertSet('isVotingActive', true)
            ->set('voting_end_at', $now->copy()->addHours(8)->format('Y-m-d\TH:i'))
            ->call('updateElection')
            ->assertHasErrors(['scheduleChangeReason']);

        $activeElection->refresh();
        $this->assertEquals($now->copy()->addHours(4)->toDateTimeString(), $activeElection->voting_end_at->toDateTimeString());

        Carbon::setTestNow();
    }

    public function test_admin_changing_active_voting_schedule_with_reason_creates_pending_request_and_preserves_official_dates(): void
    {
        $now = Carbon::parse('2026-10-15 10:00:00');
        Carbon::setTestNow($now);

        $activeElection = Election::create([
            'name' => 'PEMIRA Aktif Workflow',
            'slug' => 'pemira-aktif-workflow-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(1),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(4),
        ]);

        $newEnd = $now->copy()->addHours(8)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $activeElection->id)
            ->assertSet('isVotingActive', true)
            ->set('voting_end_at', $newEnd)
            ->set('scheduleChangeReason', 'Perpanjangan pemungutan suara karena kendala pemadaman listrik.')
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $activeElection->refresh();
        $this->assertEquals($now->copy()->addHours(4)->toDateTimeString(), $activeElection->voting_end_at->toDateTimeString());

        $this->assertDatabaseHas('schedule_change_requests', [
            'election_id' => $activeElection->id,
            'requested_by' => $this->admin->id,
            'status' => 'pending',
            'reason' => 'Perpanjangan pemungutan suara karena kendala pemadaman listrik.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'schedule_change_requested',
            'entity_type' => 'ScheduleChangeRequest',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_editing_active_election_metadata_only_does_not_create_schedule_request(): void
    {
        $now = Carbon::parse('2026-10-15 10:00:00');
        Carbon::setTestNow($now);

        $activeElection = Election::create([
            'name' => 'PEMIRA Nama Awal',
            'slug' => 'pemira-nama-awal-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(1),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(4),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $activeElection->id)
            ->assertSet('isVotingActive', true)
            ->set('name', 'PEMIRA Nama Diperbarui')
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $activeElection->refresh();
        $this->assertEquals('PEMIRA Nama Diperbarui', $activeElection->name);

        $this->assertDatabaseMissing('schedule_change_requests', [
            'election_id' => $activeElection->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_super_admin_can_directly_update_schedule_even_when_voting_is_active(): void
    {
        $now = Carbon::parse('2026-10-15 10:00:00');
        Carbon::setTestNow($now);

        $activeElection = Election::create([
            'name' => 'PEMIRA Super Admin Edit',
            'slug' => 'pemira-super-admin-edit-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDays(5),
            'registration_end_at' => $now->copy()->subDays(1),
            'voting_start_at' => $now->copy()->subHours(2),
            'voting_end_at' => $now->copy()->addHours(4),
        ]);

        $newEnd = $now->copy()->addHours(8)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->superAdmin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $activeElection->id)
            ->assertSet('isVotingActive', true)
            ->set('voting_end_at', $newEnd)
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $activeElection->refresh();
        $this->assertEquals(Carbon::parse($newEnd)->toDateTimeString(), $activeElection->voting_end_at->toDateTimeString());

        Carbon::setTestNow();
    }

    public function test_double_approval_prevents_duplicate_processing(): void
    {
        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => Carbon::now()->addDays(2),
            'new_voting_end_at' => Carbon::now()->addDays(3),
            'reason' => 'Pengajuan untuk uji double approval.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ScheduleRequestIndex::class)
            ->call('openApproveModal', $request->id)
            ->call('approveRequest')
            ->assertHasNoErrors();

        $request->refresh();
        $this->assertEquals('approved', $request->status);

        Livewire::actingAs($this->superAdmin)
            ->test(ScheduleRequestIndex::class)
            ->call('openApproveModal', $request->id)
            ->assertSet('showApproveModal', false);
    }

    public function test_admin_changing_registration_schedule_requires_reason_and_creates_schedule_change_request(): void
    {
        $oldRegStart = $this->election->registration_start_at->copy();
        $oldRegEnd = $this->election->registration_end_at->copy();

        $newRegStart = Carbon::now()->subDays(7)->setHour(8)->setMinute(0)->format('Y-m-d\TH:i');
        $newRegEnd = Carbon::now()->subDays(1)->setHour(20)->setMinute(0)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $this->election->id)
            ->set('registration_start_at', $newRegStart)
            ->set('registration_end_at', $newRegEnd)
            ->call('updateElection')
            ->assertHasErrors(['scheduleChangeReason']);

        Livewire::actingAs($this->admin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $this->election->id)
            ->set('registration_start_at', $newRegStart)
            ->set('registration_end_at', $newRegEnd)
            ->set('scheduleChangeReason', 'Perpanjangan masa pendaftaran pemilih karena kendala verifikasi berkas.')
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->election->refresh();
        $this->assertEquals($oldRegStart->toDateTimeString(), $this->election->registration_start_at->toDateTimeString());
        $this->assertEquals($oldRegEnd->toDateTimeString(), $this->election->registration_end_at->toDateTimeString());

        $request = ScheduleChangeRequest::where('election_id', $this->election->id)->where('status', 'pending')->first();
        $this->assertNotNull($request);
        $this->assertEquals('Perpanjangan masa pendaftaran pemilih karena kendala verifikasi berkas.', $request->reason);
        $this->assertEquals(Carbon::parse($newRegStart)->toDateTimeString(), $request->new_registration_start_at->toDateTimeString());
        $this->assertEquals(Carbon::parse($newRegEnd)->toDateTimeString(), $request->new_registration_end_at->toDateTimeString());
        $this->assertTrue($request->hasRegistrationChange());
    }

    public function test_super_admin_approving_request_with_registration_changes_applies_new_registration_schedule_to_election(): void
    {
        $newRegStart = Carbon::now()->subDays(6)->setHour(8)->setMinute(0);
        $newRegEnd = Carbon::now()->subDays(1)->setHour(23)->setMinute(59);

        $request = ScheduleChangeRequest::create([
            'election_id' => $this->election->id,
            'requested_by' => $this->admin->id,
            'old_registration_start_at' => $this->election->registration_start_at,
            'old_registration_end_at' => $this->election->registration_end_at,
            'new_registration_start_at' => $newRegStart,
            'new_registration_end_at' => $newRegEnd,
            'old_voting_start_at' => $this->election->voting_start_at,
            'old_voting_end_at' => $this->election->voting_end_at,
            'new_voting_start_at' => $this->election->voting_start_at,
            'new_voting_end_at' => $this->election->voting_end_at,
            'reason' => 'Perpanjangan pendaftaran pemilih yang disetujui.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ScheduleRequestIndex::class)
            ->call('openApproveModal', $request->id)
            ->call('approveRequest')
            ->assertHasNoErrors();

        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($this->superAdmin->id, $request->approved_by);

        $this->election->refresh();
        $this->assertEquals($newRegStart->toDateTimeString(), $this->election->registration_start_at->toDateTimeString());
        $this->assertEquals($newRegEnd->toDateTimeString(), $this->election->registration_end_at->toDateTimeString());
    }

    public function test_super_admin_can_directly_update_registration_schedule_without_request(): void
    {
        $newRegStart = Carbon::now()->subDays(8)->setHour(9)->setMinute(0)->format('Y-m-d\TH:i');
        $newRegEnd = Carbon::now()->subDays(1)->setHour(12)->setMinute(0)->format('Y-m-d\TH:i');

        Livewire::actingAs($this->superAdmin)
            ->test(ElectionIndex::class)
            ->call('openEditModal', $this->election->id)
            ->set('registration_start_at', $newRegStart)
            ->set('registration_end_at', $newRegEnd)
            ->call('updateElection')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->election->refresh();
        $this->assertEquals(Carbon::parse($newRegStart)->toDateTimeString(), $this->election->registration_start_at->toDateTimeString());
        $this->assertEquals(Carbon::parse($newRegEnd)->toDateTimeString(), $this->election->registration_end_at->toDateTimeString());

        $this->assertDatabaseMissing('schedule_change_requests', [
            'election_id' => $this->election->id,
        ]);
    }
}
