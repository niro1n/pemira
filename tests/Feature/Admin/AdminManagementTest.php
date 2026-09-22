<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Admins\Index as AdminIndex;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminManagementTest extends TestCase
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
    }

    public function test_super_admin_can_access_admin_management_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/admins');

        $response->assertOk();
        $response->assertSeeLivewire(AdminIndex::class);
        $response->assertSee('MANAJEMEN ADMIN');
        $response->assertSee('HAK AKSES SUPER ADMIN');
    }

    public function test_regular_admin_cannot_access_admin_management_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/admins');

        $response->assertForbidden();
    }

    public function test_voter_cannot_access_admin_management_page(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/admin/admins');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/admins');

        $response->assertRedirect('/login');
    }

    public function test_admin_list_and_statistics_display_correctly(): void
    {
        User::create([
            'email' => 'kpr.operator@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->assertSee('superadmin@pemira.test')
            ->assertSee('admin@pemira.test')
            ->assertSee('kpr.operator@pemira.test')
            ->assertSet('statistics.total', 3)
            ->assertSet('statistics.admin_count', 2)
            ->assertSet('statistics.super_admin_count', 1)
            ->assertSet('statistics.active_count', 2)
            ->assertSet('statistics.inactive_count', 1);
    }

    public function test_search_admin_by_email(): void
    {
        User::create([
            'email' => 'khusus.cari@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->set('search', 'khusus.cari')
            ->assertSee('khusus.cari@pemira.test')
            ->assertDontSee('admin@pemira.test');
    }

    public function test_filter_admin_by_role_and_status(): void
    {
        $inactiveAdmin = User::create([
            'email' => 'operator.nonaktif@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->set('roleFilter', 'admin')
            ->assertSee('admin@pemira.test')
            ->assertSee('operator.nonaktif@pemira.test')
            ->assertDontSee('superadmin@pemira.test')
            ->set('statusFilter', 'inactive')
            ->assertSee('operator.nonaktif@pemira.test')
            ->assertDontSee('admin@pemira.test');
    }

    public function test_super_admin_can_create_admin_with_audit_log(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('email', 'baru.admin@pemira.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('isActive', true)
            ->call('createAdmin')
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('users', [
            'email' => 'baru.admin@pemira.test',
            'role' => 'admin',
        ]);

        $createdUser = User::where('email', 'baru.admin@pemira.test')->first();
        $this->assertNotNull($createdUser->email_verified_at);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_created',
            'entity_type' => 'User',
            'entity_id' => $createdUser->id,
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_create_admin_validation_rules(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openCreateModal')
            ->set('email', 'bukan-email')
            ->set('password', '123')
            ->set('password_confirmation', '456')
            ->call('createAdmin')
            ->assertHasErrors(['email', 'password']);
    }

    public function test_super_admin_can_edit_admin_with_audit_log(): void
    {
        $target = User::create([
            'email' => 'target.edit@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openEditModal', $target->id)
            ->assertSet('showEditModal', true)
            ->assertSet('email', 'target.edit@pemira.test')
            ->set('email', 'target.updated@pemira.test')
            ->call('updateAdmin')
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'email' => 'target.updated@pemira.test',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_updated',
            'entity_type' => 'User',
            'entity_id' => $target->id,
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_super_admin_can_toggle_admin_status_with_audit_log(): void
    {
        $target = User::create([
            'email' => 'target.toggle@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openToggleStatusModal', $target->id)
            ->assertSet('showToggleStatusModal', true)
            ->call('toggleStatus')
            ->assertSet('showToggleStatusModal', false);

        $target->refresh();
        $this->assertNull($target->email_verified_at);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_status_changed',
            'entity_type' => 'User',
            'entity_id' => $target->id,
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_super_admin_cannot_deactivate_own_account(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openToggleStatusModal', $this->superAdmin->id)
            ->assertSet('showToggleStatusModal', false);

        $this->superAdmin->refresh();
        $this->assertNotNull($this->superAdmin->email_verified_at);
    }

    public function test_super_admin_can_delete_admin_with_audit_log(): void
    {
        $target = User::create([
            'email' => 'target.delete@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openDeleteModal', $target->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteAdmin')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('users', [
            'id' => $target->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_deleted',
            'entity_type' => 'User',
            'entity_id' => $target->id,
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_super_admin_cannot_delete_own_account(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openDeleteModal', $this->superAdmin->id)
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdmin->id,
        ]);
    }

    public function test_super_admin_cannot_delete_another_super_admin(): void
    {
        $anotherSuperAdmin = User::create([
            'email' => 'super2@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openDeleteModal', $anotherSuperAdmin->id)
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('users', [
            'id' => $anotherSuperAdmin->id,
        ]);
    }

    public function test_detail_modal_shows_admin_info_and_audit_logs(): void
    {
        $target = User::create([
            'email' => 'target.detail@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $target->id,
            'action' => 'test_action',
            'description' => 'Aktivitas log pengujian admin',
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openDetailModal', $target->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('target.detail@pemira.test')
            ->assertSee('Aktivitas log pengujian admin')
            ->call('closeDetailModal')
            ->assertSet('showDetailModal', false);
    }
}
