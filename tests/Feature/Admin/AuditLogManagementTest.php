<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AuditLogs\Index as AuditLogIndex;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditLogManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $otherAdmin;

    private User $superAdmin;

    private User $voterUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@pemira.test',
            'email_verified_at' => now(),
        ]);

        $this->otherAdmin = User::factory()->admin()->create([
            'email' => 'kpr2@pemira.test',
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
    }

    public function test_admin_can_access_audit_logs_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/audit-logs');

        $response->assertOk();
        $response->assertSeeLivewire(AuditLogIndex::class);
        $response->assertSee('AUDIT & LOG AKTIVITAS', false);
        $response->assertSee('SCOPE: ADMIN KPR (OPERASIONAL)', false);
    }

    public function test_super_admin_can_access_audit_logs_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/audit-logs');

        $response->assertOk();
        $response->assertSeeLivewire(AuditLogIndex::class);
        $response->assertSee('AUDIT & LOG AKTIVITAS', false);
        $response->assertSee('SCOPE: SUPER ADMIN (FULL)', false);
    }

    public function test_voter_cannot_access_audit_logs_page(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/admin/audit-logs');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/audit-logs');

        $response->assertRedirect('/login');
    }

    public function test_super_admin_can_view_all_audit_logs(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'eligible_voter_import',
            'entity_type' => 'EligibleVoter',
            'description' => 'Admin mengimpor pemilih',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'role_change',
            'entity_type' => 'User',
            'description' => 'Super admin mengubah role pengguna',
            'ip_address' => '10.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'login',
            'entity_type' => 'User',
            'description' => 'Admin melakukan login sistem',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => null,
            'action' => 'system_cleanup',
            'description' => 'Pembersihan berkala sistem',
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AuditLogIndex::class)
            ->assertSee('Admin mengimpor pemilih')
            ->assertSee('Super admin mengubah role pengguna')
            ->assertSee('Admin melakukan login sistem')
            ->assertSee('Pembersihan berkala sistem');
    }

    public function test_regular_admin_can_only_see_operational_logs_in_their_scope(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'eligible_voter_import',
            'entity_type' => 'EligibleVoter',
            'description' => 'Admin mengimpor pemilih angkatan 2026',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->otherAdmin->id,
            'action' => 'create_candidate_pair',
            'entity_type' => 'CandidatePair',
            'entity_id' => 1,
            'description' => 'Menambahkan paslon nomor 1',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'system_config',
            'description' => 'Super admin konfigurasi rahasia',
            'ip_address' => '10.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'role_change',
            'description' => 'Super admin eskalasi hak akses',
            'ip_address' => '10.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'login',
            'description' => 'Admin berhasil masuk ke sistem',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => null,
            'action' => 'system_cleanup',
            'description' => 'Pembersihan background otomatis',
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->assertSee('Admin mengimpor pemilih angkatan 2026')
            ->assertSee('Menambahkan paslon nomor 1')
            ->assertDontSee('Super admin konfigurasi rahasia')
            ->assertDontSee('Super admin eskalasi hak akses')
            ->assertDontSee('Admin berhasil masuk ke sistem')
            ->assertDontSee('Pembersihan background otomatis');
    }

    public function test_regular_admin_cannot_bypass_scope_via_search(): void
    {
        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'system_config',
            'description' => 'Super admin config khusus',
            'ip_address' => '192.168.99.1',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->set('search', 'khusus')
            ->assertDontSee('Super admin config khusus')
            ->set('search', '192.168.99.1')
            ->assertDontSee('Super admin config khusus')
            ->set('search', 'superadmin@pemira.test')
            ->assertDontSee('superadmin@pemira.test');
    }

    public function test_regular_admin_cannot_open_restricted_detail_modal(): void
    {
        $restrictedLog = AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'role_change',
            'description' => 'Super admin sensitive log',
            'ip_address' => '10.0.0.1',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->call('openDetailModal', $restrictedLog->id)
            ->assertSet('showDetailModal', false)
            ->assertSet('selectedLogId', null);
    }

    public function test_super_admin_can_open_restricted_detail_modal(): void
    {
        $restrictedLog = AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'role_change',
            'description' => 'Super admin sensitive log',
            'ip_address' => '10.0.0.1',
            'metadata' => ['target_role' => 'admin'],
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AuditLogIndex::class)
            ->call('openDetailModal', $restrictedLog->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedLogId', $restrictedLog->id)
            ->assertSee('Super admin sensitive log')
            ->assertSee('target role')
            ->assertSee('admin');
    }

    public function test_super_admin_sees_actor_and_role_filters_in_modal(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(AuditLogIndex::class)
            ->call('openFilterModal')
            ->assertSet('showFilterModal', true)
            ->assertSee('Aktor / Pengguna')
            ->assertSee('Role / Peran')
            ->call('closeFilterModal')
            ->assertSet('showFilterModal', false);
    }

    public function test_regular_admin_does_not_see_actor_and_role_filters_in_modal(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->call('openFilterModal')
            ->assertSet('showFilterModal', true)
            ->assertDontSee('Aktor / Pengguna')
            ->assertDontSee('Role / Peran')
            ->assertSee('Tipe Aksi')
            ->assertSee('Modul / Entitas')
            ->call('closeFilterModal')
            ->assertSet('showFilterModal', false);
    }

    public function test_filter_by_module_and_action(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'eligible_voter_import',
            'entity_type' => 'EligibleVoter',
            'description' => 'Import voter CSV',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'create_candidate_pair',
            'entity_type' => 'CandidatePair',
            'description' => 'Paslon create action',
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->set('moduleFilter', 'EligibleVoter')
            ->assertSee('Import voter CSV')
            ->assertDontSee('Paslon create action')
            ->set('actionFilter', 'create_candidate_pair')
            ->set('moduleFilter', 'all')
            ->assertSee('Paslon create action')
            ->assertDontSee('Import voter CSV');
    }

    public function test_filter_by_date_range(): void
    {
        $oldLog = AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'create_candidate_pair',
            'entity_type' => 'CandidatePair',
            'description' => 'Log aktivitas lampau paslon',
            'ip_address' => '127.0.0.1',
        ]);
        $oldLog->created_at = Carbon::now()->subDays(10);
        $oldLog->save();

        $recentLog = AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'create_candidate_pair',
            'entity_type' => 'CandidatePair',
            'description' => 'Log aktivitas hari ini paslon',
            'ip_address' => '127.0.0.1',
        ]);

        $today = Carbon::now('Asia/Makassar')->format('Y-m-d');

        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->set('startDate', $today)
            ->assertSee('Log aktivitas hari ini paslon')
            ->assertDontSee('Log aktivitas lampau paslon');
    }

    public function test_active_filter_chips_and_reset(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AuditLogIndex::class)
            ->set('actionFilter', 'create_candidate_pair')
            ->set('startDate', '2026-09-01')
            ->assertSet('activeFilterCount', 2)
            ->assertSee('Filter Aktif:')
            ->call('clearFilter', 'action')
            ->assertSet('actionFilter', 'all')
            ->assertSet('activeFilterCount', 1)
            ->call('resetFilters')
            ->assertSet('activeFilterCount', 0)
            ->assertDontSee('Filter Aktif:');
    }
}
