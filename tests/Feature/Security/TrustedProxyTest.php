<?php

namespace Tests\Feature\Security;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        TrustProxies::flushState();
        parent::tearDown();
    }

    public function test_untrusted_forwarded_for_header_is_ignored_by_default(): void
    {
        TrustProxies::flushState();

        $this->withServerVariables([
            'REMOTE_ADDR' => '192.168.1.50',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.195',
        ])->get('/up');

        $this->assertEquals('192.168.1.50', request()->ip());
    }

    public function test_trusted_proxy_correctly_resolves_client_ip_when_configured(): void
    {
        TrustProxies::at('192.168.1.50');

        $this->withServerVariables([
            'REMOTE_ADDR' => '192.168.1.50',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.195',
        ])->get('/up');

        $this->assertEquals('203.0.113.195', request()->ip());
    }

    public function test_authentication_rate_limiting_tracks_resolved_client_ip(): void
    {
        $user = User::factory()->create([
            'email' => 'throttletest@student.pnb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'voter',
        ]);

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)
                ->set('email', 'throttletest@student.pnb.ac.id')
                ->set('password', 'wrongpassword')
                ->call('login');
        }

        Livewire::test(Login::class)
            ->set('email', 'throttletest@student.pnb.ac.id')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);
    }
}
