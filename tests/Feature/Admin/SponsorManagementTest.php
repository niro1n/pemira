<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Sponsors\Index as SponsorIndex;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SponsorManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superAdmin;

    private User $voterUser;

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
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/sponsors');

        $response->assertRedirect('/login');
    }

    public function test_voter_cannot_access_sponsors_page(): void
    {
        $response = $this->actingAs($this->voterUser)->get('/admin/sponsors');

        $response->assertForbidden();
    }

    public function test_admin_can_access_sponsors_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/sponsors');

        $response->assertOk();
        $response->assertSeeLivewire(SponsorIndex::class);
        $response->assertSee('MANAJEMEN SPONSOR', false);
    }

    public function test_super_admin_can_access_sponsors_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/sponsors');

        $response->assertOk();
        $response->assertSeeLivewire(SponsorIndex::class);
        $response->assertSee('MANAJEMEN SPONSOR', false);
    }

    public function test_admin_can_create_sponsor_with_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('sponsor_logo.png', 500, 500);

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openCreateModal')
            ->set('name', 'PT Teknologi Nusantara')
            ->set('logo', $file)
            ->set('website_url', 'https://teknologi.co.id')
            ->set('sort_order', 1)
            ->set('is_active', true)
            ->call('createSponsor')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('sponsors', [
            'name' => 'PT Teknologi Nusantara',
            'website_url' => 'https://teknologi.co.id',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $sponsor = Sponsor::where('name', 'PT Teknologi Nusantara')->first();
        $this->assertNotNull($sponsor);
        $this->assertTrue(Storage::disk('public')->exists($sponsor->logo));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'create_sponsor',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $sponsor->id,
        ]);
    }

    public function test_non_image_file_is_rejected_on_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Invalid Sponsor')
            ->set('logo', $file)
            ->call('createSponsor')
            ->assertHasErrors(['logo']);

        $this->assertDatabaseCount('sponsors', 0);
    }

    public function test_validation_rules_for_sponsor_creation(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openCreateModal')
            ->set('name', '')
            ->set('logo', null)
            ->set('website_url', 'not-a-valid-url')
            ->set('sort_order', -1)
            ->call('createSponsor')
            ->assertHasErrors(['name', 'logo', 'website_url', 'sort_order']);
    }

    public function test_admin_can_update_sponsor_and_cleanup_old_logo(): void
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->image('old_logo.png', 400, 400);
        $oldPath = $oldFile->store('sponsors', 'public');

        $sponsor = Sponsor::create([
            'name' => 'Old Sponsor Name',
            'logo' => $oldPath,
            'website_url' => null,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($oldPath));

        $newFile = UploadedFile::fake()->image('new_logo.png', 600, 600);

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openEditModal', $sponsor->id)
            ->set('name', 'Updated Sponsor Name')
            ->set('logo', $newFile)
            ->set('website_url', 'https://updated.com')
            ->set('sort_order', 5)
            ->call('updateSponsor')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $sponsor->refresh();
        $this->assertSame('Updated Sponsor Name', $sponsor->name);
        $this->assertSame('https://updated.com', $sponsor->website_url);
        $this->assertSame(5, $sponsor->sort_order);

        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertTrue(Storage::disk('public')->exists($sponsor->logo));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'update_sponsor',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $sponsor->id,
        ]);
    }

    public function test_admin_can_update_sponsor_without_changing_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('preserved_logo.png', 400, 400);
        $path = $file->store('sponsors', 'public');

        $sponsor = Sponsor::create([
            'name' => 'Preserved Name',
            'logo' => $path,
            'website_url' => null,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openEditModal', $sponsor->id)
            ->set('name', 'Modified Name Only')
            ->call('updateSponsor')
            ->assertHasNoErrors();

        $sponsor->refresh();
        $this->assertSame('Modified Name Only', $sponsor->name);
        $this->assertSame($path, $sponsor->logo);
        $this->assertTrue(Storage::disk('public')->exists($path));
    }

    public function test_admin_can_delete_sponsor_and_cleanup_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('to_delete.png', 300, 300);
        $path = $file->store('sponsors', 'public');

        $sponsor = Sponsor::create([
            'name' => 'Sponsor To Delete',
            'logo' => $path,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($path));

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openDeleteModal', $sponsor->id)
            ->call('deleteSponsor')
            ->assertHasNoErrors()
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('sponsors', [
            'id' => $sponsor->id,
        ]);

        $this->assertFalse(Storage::disk('public')->exists($path));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'delete_sponsor',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $sponsor->id,
        ]);
    }

    public function test_admin_can_toggle_sponsor_status(): void
    {
        $sponsor = Sponsor::create([
            'name' => 'Toggle Sponsor',
            'logo' => 'sponsors/toggle.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('toggleStatus', $sponsor->id);

        $this->assertFalse($sponsor->fresh()->is_active);

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('toggleStatus', $sponsor->id);

        $this->assertTrue($sponsor->fresh()->is_active);
    }

    public function test_active_sponsors_displayed_on_landing_page(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('sponsor.png', 200, 200);
        $path = $file->store('sponsors', 'public');

        Sponsor::create([
            'name' => 'Mitra Aktif Resmi',
            'logo' => $path,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Sponsor::create([
            'name' => 'Mitra Nonaktif Rahasia',
            'logo' => $path,
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Mitra Aktif Resmi');
        $response->assertDontSee('Mitra Nonaktif Rahasia');
    }

    public function test_sponsors_displayed_in_correct_sort_order(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('sponsor.png', 200, 200);
        $path = $file->store('sponsors', 'public');

        Sponsor::create([
            'name' => 'Sponsor Urutan Kedua',
            'logo' => $path,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        Sponsor::create([
            'name' => 'Sponsor Urutan Pertama',
            'logo' => $path,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder([
            'Sponsor Urutan Pertama',
            'Sponsor Urutan Kedua',
        ]);
    }

    public function test_website_url_is_saved_and_rendered_safely(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('sponsor.png', 200, 200);
        $path = $file->store('sponsors', 'public');

        Sponsor::create([
            'name' => 'Sponsor Dengan Website',
            'logo' => $path,
            'website_url' => 'https://mitra-resmi.co.id',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Sponsor::create([
            'name' => 'Sponsor Tanpa Website',
            'logo' => $path,
            'website_url' => null,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('href="https://mitra-resmi.co.id"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Sponsor Dengan Website');
        $response->assertSee('Sponsor Tanpa Website');
    }

    public function test_empty_sponsors_shows_partnership_cta_on_landing_page(): void
    {
        Sponsor::query()->delete();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="sponsors"', false);
        $response->assertSee('TERBUKA UNTUK KERJA SAMA & SPONSORSHIP', false);
        $response->assertSee('HUBUNGI TIM HUMAS');
        $response->assertSee('HUBUNGI HUMAS');
        $response->assertSee('KETUA PANITIA');
    }

    public function test_admin_can_open_and_close_detail_modal(): void
    {
        $sponsor = Sponsor::create([
            'name' => 'Detail Sponsor Test',
            'logo' => 'sponsors/detail.png',
            'website_url' => 'https://detail.com',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SponsorIndex::class)
            ->call('openDetailModal', $sponsor->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('Detail Sponsor Test')
            ->call('closeDetailModal')
            ->assertSet('showDetailModal', false);
    }
}
