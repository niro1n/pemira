<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::create([
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

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_remember_me(): void
    {
        $user = User::create([
            'email' => 'voter@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'voter@student.pnb.ac.id')
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('voter.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_user_cannot_login(): void
    {
        User::create([
            'email' => 'unverified@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => null,
        ]);

        Livewire::test(Login::class)
            ->set('email', 'unverified@student.pnb.ac.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::create([
            'email' => 'voter@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'voter@student.pnb.ac.id')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_user_cannot_login_with_unregistered_email(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'unregistered@student.pnb.ac.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_admin_user_is_redirected_to_admin_panel(): void
    {
        $admin = User::create([
            'email' => 'admin@pnb.ac.id',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@pnb.ac.id')
            ->set('password', 'adminpassword')
            ->call('login')
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_super_admin_user_is_redirected_to_admin_panel(): void
    {
        $superAdmin = User::create([
            'email' => 'superadmin@pnb.ac.id',
            'password' => Hash::make('superadminpass'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'superadmin@pnb.ac.id')
            ->set('password', 'superadminpass')
            ->call('login')
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($superAdmin);
    }

    public function test_login_throttling_after_multiple_failed_attempts(): void
    {
        $component = Livewire::test(Login::class);

        for ($i = 0; $i < 5; $i++) {
            $component
                ->set('email', 'voter@student.pnb.ac.id')
                ->set('password', 'wrong-pass')
                ->call('login');
        }

        $component
            ->set('email', 'voter@student.pnb.ac.id')
            ->set('password', 'wrong-pass')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'email' => 'voter@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
