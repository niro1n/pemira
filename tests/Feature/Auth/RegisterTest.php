<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Mail\RegistrationOtpMail;
use App\Models\EligibleVoter;
use App\Models\RegistrationOtp;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
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

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_is_redirected_away_from_register(): void
    {
        $user = User::create([
            'email' => 'voter@example.com',
            'password' => 'Password123!',
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/');
    }

    public function test_valid_nim_and_dob_advances_to_step_two(): void
    {
        EligibleVoter::create([
            'nim' => '220101001',
            'name' => 'Ahmad Fauzi',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101001')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2)
            ->assertSet('studentName', 'Ahmad Fauzi')
            ->assertSet('studentNim', '220101001')
            ->assertSet('studentProdi', 'Teknik Informatika');
    }

    public function test_invalid_nim_fails_validation(): void
    {
        Livewire::test(Register::class)
            ->set('nim', '999999999')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->assertHasErrors(['nim'])
            ->assertSet('currentStep', 1);
    }

    public function test_incorrect_birth_date_fails_validation(): void
    {
        EligibleVoter::create([
            'nim' => '220101002',
            'name' => 'Budi Santoso',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101002')
            ->set('birth_date', '2003-01-01')
            ->call('validateStudent')
            ->assertHasErrors(['nim'])
            ->assertSet('currentStep', 1);
    }

    public function test_ineligible_voter_fails_validation(): void
    {
        EligibleVoter::create([
            'nim' => '220101003',
            'name' => 'Citra Lestari',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => false,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101003')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->assertHasErrors(['nim'])
            ->assertSet('currentStep', 1);
    }

    public function test_already_registered_voter_fails_validation(): void
    {
        $voter = EligibleVoter::create([
            'nim' => '220101004',
            'name' => 'Dewi Anggraini',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $user = User::create([
            'email' => 'dewi@example.com',
            'password' => 'Password123!',
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        VoterAccount::create([
            'user_id' => $user->id,
            'eligible_voter_id' => $voter->id,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101004')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->assertHasErrors(['nim'])
            ->assertSet('currentStep', 1);
    }

    public function test_step_two_validates_email_and_password_and_sends_otp(): void
    {
        Mail::fake();

        EligibleVoter::create([
            'nim' => '220101005',
            'name' => 'Eko Prasetyo',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101005')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'eko@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 3);

        Mail::assertSent(RegistrationOtpMail::class, function ($mail) {
            return $mail->hasTo('eko@example.com');
        });

        $this->assertDatabaseHas('registration_otps', [
            'email' => 'eko@example.com',
            'used_at' => null,
        ]);
    }

    public function test_step_two_rejects_duplicate_email(): void
    {
        User::create([
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        EligibleVoter::create([
            'nim' => '220101006',
            'name' => 'Fajar Nugraha',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101006')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'existing@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData')
            ->assertHasErrors(['email'])
            ->assertSet('currentStep', 2);
    }

    public function test_step_two_rejects_password_mismatch(): void
    {
        EligibleVoter::create([
            'nim' => '220101007',
            'name' => 'Gita Gutawa',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        Livewire::test(Register::class)
            ->set('nim', '220101007')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'gita@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'DifferentPassword123!')
            ->call('submitAccountData')
            ->assertHasErrors(['password'])
            ->assertSet('currentStep', 2);
    }

    public function test_valid_otp_completes_registration_atomically(): void
    {
        Mail::fake();

        $voter = EligibleVoter::create([
            'nim' => '220101008',
            'name' => 'Hadi Wijaya',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101008')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'hadi@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'hadi@example.com')->first();
        $this->assertNotNull($otpRecord);

        $otpRecord->update([
            'otp_hash' => hash('sha256', '654321'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $test->set('otp', ['6', '5', '4', '3', '2', '1'])
            ->call('verifyOtp')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 4);

        $this->assertDatabaseHas('users', [
            'email' => 'hadi@example.com',
            'role' => 'voter',
        ]);

        $user = User::where('email', 'hadi@example.com')->first();
        $this->assertNotNull($user->email_verified_at);

        $this->assertDatabaseHas('voter_accounts', [
            'user_id' => $user->id,
            'eligible_voter_id' => $voter->id,
        ]);

        $this->assertNotNull($otpRecord->fresh()->used_at);
    }

    public function test_invalid_otp_fails_and_increments_attempts(): void
    {
        Mail::fake();

        EligibleVoter::create([
            'nim' => '220101009',
            'name' => 'Indah Permata',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101009')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'indah@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'indah@example.com')->first();
        $this->assertNotNull($otpRecord);

        $otpRecord->update([
            'otp_hash' => hash('sha256', '123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $test->set('otp', ['9', '9', '9', '9', '9', '9'])
            ->call('verifyOtp')
            ->assertHasErrors(['otp'])
            ->assertSet('currentStep', 3);

        $this->assertEquals(1, $otpRecord->fresh()->attempts);
    }

    public function test_otp_locked_after_exceeding_maximum_attempts(): void
    {
        Mail::fake();

        EligibleVoter::create([
            'nim' => '220101012',
            'name' => 'Lukman Hakim',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101012')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'lukman@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'lukman@example.com')->first();
        $otpRecord->update([
            'otp_hash' => hash('sha256', '123456'),
            'attempts' => 5,
            'expires_at' => now()->addMinutes(5),
        ]);

        $test->set('otp', ['1', '2', '3', '4', '5', '6'])
            ->call('verifyOtp')
            ->assertHasErrors(['otp']);
    }

    public function test_expired_otp_is_rejected(): void
    {
        Mail::fake();

        EligibleVoter::create([
            'nim' => '220101010',
            'name' => 'Joko Susilo',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101010')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'joko@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'joko@example.com')->first();
        $this->assertNotNull($otpRecord);

        $otpRecord->update([
            'otp_hash' => hash('sha256', '123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $test->set('otp', ['1', '2', '3', '4', '5', '6'])
            ->call('verifyOtp')
            ->assertHasErrors(['otp']);
    }

    public function test_resend_otp_enforces_cooldown(): void
    {
        Mail::fake();

        EligibleVoter::create([
            'nim' => '220101011',
            'name' => 'Kartika Sari',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101011')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'kartika@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $test->call('resendOtp')
            ->assertHasErrors(['otp']);
    }

    public function test_used_otp_cannot_be_reused(): void
    {
        Mail::fake();

        EligibleVoter::create([
            'nim' => '220101013',
            'name' => 'Maya Sofia',
            'date_of_birth' => '2003-05-15',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);

        $test = Livewire::test(Register::class)
            ->set('nim', '220101013')
            ->set('birth_date', '2003-05-15')
            ->call('validateStudent')
            ->set('email', 'maya@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('submitAccountData');

        $otpRecord = RegistrationOtp::where('email', 'maya@example.com')->first();
        $otpRecord->update([
            'otp_hash' => hash('sha256', '112233'),
            'used_at' => now(),
        ]);

        $test->set('otp', ['1', '1', '2', '2', '3', '3'])
            ->call('verifyOtp')
            ->assertHasErrors(['otp']);
    }
}
