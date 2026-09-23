<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\EligibleVoter;
use App\Models\RegistrationOtp;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private StudyProgram $studyProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studyProgram = StudyProgram::create([
            'name' => 'Teknik Informatika',
            'code' => 'IF',
        ]);
    }

    public function test_voter_can_login(): void
    {
        $voter = User::factory()->create([
            'email' => 'voter@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'voter@student.pnb.ac.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('voter.dashboard'));

        $this->assertAuthenticatedAs($voter);
        $this->assertTrue($voter->isVoter());
        $this->assertFalse($voter->isAdmin());
        $this->assertFalse($voter->isSuperAdmin());
        $this->assertFalse($voter->canAccessAdminPanel());
    }

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@pnb.ac.id',
            'password' => Hash::make('adminpassword'),
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@pnb.ac.id')
            ->set('password', 'adminpassword')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
        $this->assertFalse($admin->isVoter());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isSuperAdmin());
        $this->assertTrue($admin->canAccessAdminPanel());
    }

    public function test_super_admin_can_login(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'email' => 'superadmin@pnb.ac.id',
            'password' => Hash::make('superadminpassword'),
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'superadmin@pnb.ac.id')
            ->set('password', 'superadminpassword')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($superAdmin);
        $this->assertFalse($superAdmin->isVoter());
        $this->assertFalse($superAdmin->isAdmin());
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($superAdmin->canAccessAdminPanel());
    }

    public function test_voter_cannot_access_admin_area(): void
    {
        $voter = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($voter)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_super_admin_can_access_admin_area(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin');

        $response->assertOk();
    }

    public function test_public_registration_always_creates_voter_role(): void
    {
        Mail::fake();

        $voter = EligibleVoter::create([
            'nim' => '220101099',
            'name' => 'Pemilih Baru',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101099')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'pemilihbaru@student.pnb.ac.id')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'pemilihbaru@student.pnb.ac.id')->first();
        $this->assertNotNull($otpRecord);

        $otpRecord->update([
            'otp_hash' => hash('sha256', '123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $test->set('otp', ['1', '2', '3', '4', '5', '6'])
            ->call('verifyOtp')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 4);

        $user = User::where('email', 'pemilihbaru@student.pnb.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('voter', $user->role);
        $this->assertTrue($user->isVoter());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_registration_with_admin_role_request_does_not_create_admin(): void
    {
        Mail::fake();

        $voter = EligibleVoter::create([
            'nim' => '220101098',
            'name' => 'Hacker Admin',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101098')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'hackeradmin@student.pnb.ac.id')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'hackeradmin@student.pnb.ac.id')->first();
        $otpRecord->update([
            'otp_hash' => hash('sha256', '123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $test->set('otp', ['1', '2', '3', '4', '5', '6'])
            ->call('verifyOtp')
            ->assertHasNoErrors();

        $user = User::where('email', 'hackeradmin@student.pnb.ac.id')->first();
        $this->assertEquals('voter', $user->role);
        $this->assertNotEquals('admin', $user->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_registration_with_super_admin_role_request_does_not_create_super_admin(): void
    {
        Mail::fake();

        $voter = EligibleVoter::create([
            'nim' => '220101097',
            'name' => 'Hacker SuperAdmin',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101097')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'hackersuper@student.pnb.ac.id')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'hackersuper@student.pnb.ac.id')->first();
        $otpRecord->update([
            'otp_hash' => hash('sha256', '123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $test->set('otp', ['1', '2', '3', '4', '5', '6'])
            ->call('verifyOtp')
            ->assertHasNoErrors();

        $user = User::where('email', 'hackersuper@student.pnb.ac.id')->first();
        $this->assertEquals('voter', $user->role);
        $this->assertNotEquals('super_admin', $user->role);
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_unauthenticated_user_cannot_access_admin_area(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_escalate_role_to_super_admin_via_regular_request(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post('/profile', [
            'role' => 'super_admin',
        ]);

        $admin->refresh();
        $this->assertEquals('admin', $admin->role);
        $this->assertFalse($admin->isSuperAdmin());
    }

    public function test_voter_cannot_access_admin_routes(): void
    {
        $voter = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($voter)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse(Gate::forUser($voter)->allows('access-admin-panel'));
        $this->assertFalse(Gate::forUser($voter)->allows('admin-only'));
        $this->assertFalse(Gate::forUser($voter)->allows('super-admin-only'));
        $this->assertTrue(Gate::forUser($voter)->allows('voter-only'));
    }

    public function test_gates_and_panel_authorization(): void
    {
        $voter = User::factory()->create(['role' => 'voter']);
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertFalse($voter->canAccessAdminPanel());
        $this->assertTrue($admin->canAccessAdminPanel());
        $this->assertTrue($superAdmin->canAccessAdminPanel());

        $this->assertFalse(Gate::forUser($voter)->allows('access-admin-panel'));
        $this->assertTrue(Gate::forUser($admin)->allows('access-admin-panel'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('access-admin-panel'));

        $this->assertFalse(Gate::forUser($voter)->allows('admin-only'));
        $this->assertTrue(Gate::forUser($admin)->allows('admin-only'));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('admin-only'));

        $this->assertFalse(Gate::forUser($voter)->allows('super-admin-only'));
        $this->assertFalse(Gate::forUser($admin)->allows('super-admin-only'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('super-admin-only'));
    }

    public function test_authenticated_profile_redirect_behavior(): void
    {
        $voter = User::factory()->create(['role' => 'voter']);
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($voter)->get('/profile')->assertRedirect(route('voter.profile'));
        $this->actingAs($admin)->get('/profile')->assertRedirect('/admin');
        $this->actingAs($superAdmin)->get('/profile')->assertRedirect('/admin');
    }
}
