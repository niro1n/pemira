<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Dashboard\DashboardDataProvider;
use App\Services\Dashboard\DashboardDataProviderInterface;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            DashboardDataProviderInterface::class,
            DashboardDataProvider::class,
        );
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
        $this->configurePasswordReset();
    }

    protected function configurePasswordReset(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Atur Ulang Kata Sandi — PEMIRA 2026')
                ->view('emails.reset-password', [
                    'url' => $url,
                    'name' => $notifiable->voterAccount?->eligibleVoter?->name ?? $notifiable->email,
                ]);
        });
    }

    protected function configureGates(): void
    {
        Gate::define('access-admin-panel', fn (User $user) => $user->canAccessAdminPanel());
        Gate::define('super-admin-only', fn (User $user) => $user->isSuperAdmin());
        Gate::define('admin-only', fn (User $user) => $user->isAdmin());
        Gate::define('voter-only', fn (User $user) => $user->isVoter());
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
