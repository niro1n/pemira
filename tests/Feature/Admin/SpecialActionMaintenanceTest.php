<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SpecialActions\Index as SpecialActionIndex;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SpecialActionMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $voterUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'email' => 'superadmin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'email' => 'admin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->voterUser = User::create([
            'email' => 'voter@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        SystemSetting::setMaintenanceMode(false);
    }

    public function test_super_admin_can_access_special_actions_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/special-actions');

        $response->assertOk();
        $response->assertSeeLivewire(SpecialActionIndex::class);
        $response->assertSee('TINDAKAN KHUSUS');
        $response->assertSee('Maintenance Mode');
        $response->assertSee('Status Sistem');
    }

    public function test_legacy_tindakan_khusus_redirects_to_special_actions(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/tindakan-khusus');

        $response->assertRedirect(route('admin.special-actions.index'));
        $response->assertStatus(301);
    }

    public function test_direct_tindakan_khusus_route_redirects_to_admin_special_actions(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/tindakan-khusus');

        $response->assertRedirect(route('admin.special-actions.index'));
    }

    public function test_regular_admin_cannot_access_special_actions_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/special-actions');

        $response->assertForbidden();
    }

    public function test_voter_cannot_access_special_actions_page(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/admin/special-actions');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_accessing_special_actions(): void
    {
        $response = $this->get('/admin/special-actions');

        $response->assertRedirect('/login');
    }

    public function test_sidebar_shows_special_actions_only_to_super_admin(): void
    {
        $superAdminResponse = $this->actingAs($this->superAdmin)->get('/admin');
        $superAdminResponse->assertOk();
        $superAdminResponse->assertSee('Tindakan Khusus');

        $adminResponse = $this->actingAs($this->admin)->get('/admin');
        $adminResponse->assertOk();
        $adminResponse->assertDontSee('Tindakan Khusus');
    }

    public function test_super_admin_can_toggle_maintenance_mode_on_with_confirmation_and_audit_log(): void
    {
        $component = Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionIndex::class)
            ->assertSet('isMaintenanceMode', false)
            ->assertSet('showConfirmModal', false)
            ->call('requestToggle', true)
            ->assertSet('targetState', true)
            ->assertSet('showConfirmModal', true)
            ->call('cancelToggle')
            ->assertSet('showConfirmModal', false);

        $this->assertFalse(SystemSetting::isMaintenanceMode());

        $component->call('requestToggle', true)
            ->call('confirmToggle')
            ->assertSet('isMaintenanceMode', true)
            ->assertSet('showConfirmModal', false)
            ->assertSee('Mode pemeliharaan berhasil diaktifkan');

        $this->assertTrue(SystemSetting::isMaintenanceMode());

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'maintenance_mode_enabled',
            'entity_type' => 'SystemSetting',
        ]);
    }

    public function test_super_admin_can_toggle_maintenance_mode_off(): void
    {
        SystemSetting::setMaintenanceMode(true);

        Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionIndex::class)
            ->assertSet('isMaintenanceMode', true)
            ->call('requestToggle', false)
            ->call('confirmToggle')
            ->assertSet('isMaintenanceMode', false)
            ->assertSee('Mode pemeliharaan berhasil dinonaktifkan');

        $this->assertFalse(SystemSetting::isMaintenanceMode());

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'maintenance_mode_disabled',
            'entity_type' => 'SystemSetting',
        ]);
    }

    public function test_maintenance_mode_persists_across_queries(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $this->assertTrue(SystemSetting::isMaintenanceMode());
        $this->assertSame('1', SystemSetting::get('maintenance_mode'));

        SystemSetting::setMaintenanceMode(false);

        $this->assertFalse(SystemSetting::isMaintenanceMode());
        $this->assertSame('0', SystemSetting::get('maintenance_mode'));
    }

    public function test_non_super_admin_cannot_execute_toggle_action(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SpecialActionIndex::class)
            ->assertForbidden();

        Livewire::actingAs($this->voterUser)
            ->test(SpecialActionIndex::class)
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_maintenance_page_when_maintenance_is_active(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $response = $this->get('/');

        $response->assertRedirect(route('maintenance'));

        $maintenanceResponse = $this->get('/maintenance');
        $maintenanceResponse->assertStatus(503);
        $maintenanceResponse->assertSee('Website Sedang Dalam Pemeliharaan');
        $maintenanceResponse->assertSee('Kami sedang melakukan pemeliharaan sistem. Silakan kembali beberapa saat lagi.');
        $maintenanceResponse->assertDontSee('/admin');
        $maintenanceResponse->assertDontSee('Tindakan Khusus');
    }

    public function test_voter_is_redirected_to_maintenance_page_when_maintenance_is_active(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $response = $this->actingAs($this->voterUser)->get('/');

        $response->assertRedirect(route('maintenance'));
    }

    public function test_regular_admin_is_redirected_to_maintenance_page_when_maintenance_is_active(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertRedirect(route('maintenance'));
    }

    public function test_super_admin_can_still_access_dashboard_and_special_actions_during_maintenance(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $dashboardResponse = $this->actingAs($this->superAdmin)->get('/admin');
        $dashboardResponse->assertOk();

        $specialActionsResponse = $this->actingAs($this->superAdmin)->get('/admin/special-actions');
        $specialActionsResponse->assertOk();
        $specialActionsResponse->assertSee('AKTIF');
    }

    public function test_visiting_maintenance_page_when_maintenance_is_off_redirects_to_home(): void
    {
        SystemSetting::setMaintenanceMode(false);

        $response = $this->get('/maintenance');

        $response->assertRedirect(route('home'));
    }

    public function test_login_and_logout_routes_remain_accessible_during_maintenance(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $loginResponse = $this->get('/login');
        $loginResponse->assertOk();

        $logoutResponse = $this->actingAs($this->superAdmin)->post('/logout');
        $logoutResponse->assertRedirect('/');
    }

    public function test_livewire_request_from_non_super_admin_is_blocked_during_maintenance(): void
    {
        SystemSetting::setMaintenanceMode(true);

        $response = $this->actingAs($this->voterUser)
            ->postJson(route('default-livewire.update'), [], [
                'Referer' => 'http://localhost/admin/candidate-pairs',
                'X-Livewire' => 'true',
            ]);

        $response->assertStatus(503);
    }
}
