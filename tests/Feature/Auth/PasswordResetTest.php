<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
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

    public function test_guest_can_render_forgot_password_screen(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
    }

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'mahasiswa@student.pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'mahasiswa@student.pnb.ac.id')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', 'Jika alamat email tersebut terdaftar di sistem kami, kami telah mengirimkan instruksi dan tautan untuk mengatur ulang kata sandi.');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_email_is_sent_using_laravel_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'voter@student.pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'voter@student.pnb.ac.id')
            ->call('sendResetLink');

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $mail = $notification->toMail($user);

            return $mail->subject === 'Atur Ulang Kata Sandi — PEMIRA 2026';
        });
    }

    public function test_forgot_password_response_does_not_leak_user_existence(): void
    {
        Notification::fake();

        $registeredUser = User::factory()->create([
            'email' => 'registered@student.pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        $component1 = Livewire::test(ForgotPassword::class)
            ->set('email', 'registered@student.pnb.ac.id')
            ->call('sendResetLink')
            ->assertHasNoErrors();

        $component2 = Livewire::test(ForgotPassword::class)
            ->set('email', 'unregistered@student.pnb.ac.id')
            ->call('sendResetLink')
            ->assertHasNoErrors();

        $this->assertEquals($component1->get('statusMessage'), $component2->get('statusMessage'));
        Notification::assertNotSentTo($registeredUser, ResetPasswordNotification::class, function () {
            return false;
        });
    }

    public function test_user_can_render_reset_password_screen_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        $response = $this->get('/reset-password/'.$token.'?email=user@student.pnb.ac.id');

        $response->assertOk();
    }

    public function test_new_password_must_be_confirmed_and_valid(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'Short1!')
            ->set('password_confirmation', 'Mismatch!')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }

    public function test_new_password_is_saved_as_hash(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->fresh()->password));
    }

    public function test_old_password_can_no_longer_be_used_to_login(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword');

        Livewire::test(Login::class)
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'OldPassword123!')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_new_password_can_be_used_to_login(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword');

        Livewire::test(Login::class)
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_token_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasNoErrors();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'AnotherPassword123!')
            ->set('password_confirmation', 'AnotherPassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        Livewire::test(ResetPassword::class, ['token' => 'invalid-token-123456'])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }

    public function test_expired_reset_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'user@student.pnb.ac.id',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        DB::table('password_reset_tokens')->where('email', $user->email)->update([
            'created_at' => now()->subHours(2),
        ]);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'user@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }

    public function test_password_reset_does_not_change_role(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($admin);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'admin@pnb.ac.id')
            ->set('password', 'NewAdminPass123!')
            ->set('password_confirmation', 'NewAdminPass123!')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->assertEquals('admin', $admin->fresh()->role);
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_password_reset_does_not_change_email_verified_at(): void
    {
        $originalVerificationDate = now()->subDays(5)->startOfSecond();

        $user = User::factory()->create([
            'email' => 'voter@student.pnb.ac.id',
            'email_verified_at' => $originalVerificationDate,
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'voter@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->assertEquals(
            $originalVerificationDate->timestamp,
            $user->fresh()->email_verified_at->timestamp
        );
    }

    public function test_password_reset_does_not_create_or_modify_voter_accounts(): void
    {
        $voter = EligibleVoter::create([
            'nim' => '220101050',
            'name' => 'Voter Account Test',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $user = User::factory()->create([
            'email' => 'voteraccount@student.pnb.ac.id',
            'email_verified_at' => now(),
        ]);

        $voterAccount = VoterAccount::create([
            'user_id' => $user->id,
            'eligible_voter_id' => $voter->id,
        ]);

        $initialAccountsCount = VoterAccount::count();

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'voteraccount@student.pnb.ac.id')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->assertEquals($initialAccountsCount, VoterAccount::count());
        $this->assertEquals($voterAccount->id, $user->fresh()->voterAccount->id);
        $this->assertEquals($voter->id, $user->fresh()->voterAccount->eligible_voter_id);
    }

    public function test_rate_limiting_works_for_repeated_forgot_password_requests(): void
    {
        Notification::fake();

        $component = Livewire::test(ForgotPassword::class);

        for ($i = 0; $i < 3; $i++) {
            $component
                ->set('email', 'spam@student.pnb.ac.id')
                ->call('sendResetLink');
        }

        $component
            ->set('email', 'spam@student.pnb.ac.id')
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    }
}
