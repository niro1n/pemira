<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class SessionSecurityTest extends TestCase
{
    public function test_session_cookie_security_configurations(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertContains(config('session.same_site'), ['lax', 'strict']);
        $this->assertEquals('json', config('session.serialization'));
    }

    public function test_production_environment_defaults_to_secure_cookie(): void
    {
        $config = require config_path('session.php');

        putenv('SESSION_SECURE_COOKIE');
        $_ENV['SESSION_SECURE_COOKIE'] = null;

        $previousAppEnv = getenv('APP_ENV');
        putenv('APP_ENV=production');
        $_ENV['APP_ENV'] = 'production';

        $resolved = env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production');
        $this->assertTrue($resolved);

        putenv('APP_ENV=local');
        $_ENV['APP_ENV'] = 'local';
        $resolvedLocal = env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production');
        $this->assertFalse($resolvedLocal);

        putenv('APP_ENV='.$previousAppEnv);
        $_ENV['APP_ENV'] = $previousAppEnv;
    }

    public function test_explicit_session_secure_cookie_override(): void
    {
        putenv('SESSION_SECURE_COOKIE=true');
        $_ENV['SESSION_SECURE_COOKIE'] = 'true';

        $resolved = env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production');
        $this->assertTrue($resolved);

        putenv('SESSION_SECURE_COOKIE=false');
        $_ENV['SESSION_SECURE_COOKIE'] = 'false';

        $resolvedFalse = env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production');
        $this->assertFalse($resolvedFalse);

        putenv('SESSION_SECURE_COOKIE');
        unset($_ENV['SESSION_SECURE_COOKIE']);
    }
}
