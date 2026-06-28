<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_secure_headers_are_injected(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_session_timeout_middleware_redirects_customer_after_inactivity(): void
    {
        Auth::shouldReceive('guard')
            ->andReturnUsing(function ($guard) {
                if ($guard === 'web') {
                    return new class {
                        public function check() { return true; }
                        public function logout() {}
                    };
                }
                return new class {
                    public function check() { return false; }
                    public function logout() {}
                };
            });

        $user = User::factory()->make(['role' => 'customer']);
        $request = \Illuminate\Http\Request::create('/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $session = new \Illuminate\Session\Store('test', new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler());
        $session->put('last_activity_web', now()->subMinutes(35)->timestamp);
        $request->setLaravelSession($session);

        $middleware = new \App\Http\Middleware\SessionTimeout();

        $response = $middleware->handle($request, function ($req) {
            return response('Next');
        });

        $this->assertTrue($response->isRedirection());
        $this->assertEquals(route('login'), $response->headers->get('Location'));
    }

    public function test_session_timeout_middleware_redirects_admin_after_inactivity(): void
    {
        Auth::shouldReceive('guard')
            ->andReturnUsing(function ($guard) {
                if ($guard === 'admin') {
                    return new class {
                        public function check() { return true; }
                        public function logout() {}
                    };
                }
                return new class {
                    public function check() { return false; }
                    public function logout() {}
                };
            });

        $admin = User::factory()->make(['role' => 'admin']);
        $request = \Illuminate\Http\Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(fn () => $admin);

        $session = new \Illuminate\Session\Store('test', new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler());
        $session->put('last_activity_admin', now()->subMinutes(20)->timestamp);
        $request->setLaravelSession($session);

        $middleware = new \App\Http\Middleware\SessionTimeout();

        $response = $middleware->handle($request, function ($req) {
            return response('Next');
        });

        $this->assertTrue($response->isRedirection());
        $this->assertEquals(route('admin.login'), $response->headers->get('Location'));
    }

    public function test_session_timeout_middleware_allows_active_users(): void
    {
        Auth::shouldReceive('guard')
            ->andReturnUsing(function ($guard) {
                if ($guard === 'web') {
                    return new class {
                        public function check() { return true; }
                        public function logout() {}
                    };
                }
                return new class {
                    public function check() { return false; }
                    public function logout() {}
                };
            });

        $user = User::factory()->make(['role' => 'customer']);
        $request = \Illuminate\Http\Request::create('/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $session = new \Illuminate\Session\Store('test', new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler());
        $session->put('last_activity_web', now()->subMinutes(5)->timestamp);
        $request->setLaravelSession($session);

        $middleware = new \App\Http\Middleware\SessionTimeout();

        $response = $middleware->handle($request, function ($req) {
            return response('Next');
        });

        $this->assertEquals('Next', $response->getContent());
    }

    public function test_api_request_signature_middleware_rejects_missing_signature(): void
    {
        $request = \Illuminate\Http\Request::create('/api/v1/courts', 'GET');

        $middleware = new \App\Http\Middleware\VerifyRequestSignature();

        $response = $middleware->handle($request, function ($req) {
            return response('Next');
        });

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertStringContainsString('Missing security signature headers', $response->getContent());
    }

    public function test_api_request_signature_middleware_accepts_valid_signature(): void
    {
        $secret = config('app.key') ?: 'base64:somekey';

        $timestamp = time();
        $nonce = str()->random(16);
        $body = '';

        $expectedSignature = hash_hmac('sha256', $timestamp . $nonce . $body, $secret);

        $request = \Illuminate\Http\Request::create('/api/v1/courts', 'GET');
        $request->headers->set('X-Timestamp', $timestamp);
        $request->headers->set('X-Nonce', $nonce);
        $request->headers->set('X-Signature', $expectedSignature);

        $middleware = new \App\Http\Middleware\VerifyRequestSignature();

        $response = $middleware->handle($request, function ($req) {
            return response('Next');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Next', $response->getContent());
    }

    public function test_customer_receives_welcome_email_on_registration(): void
    {
        $this->withoutVite();
        \Illuminate\Support\Facades\Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'John Welcomer',
            'email' => 'johnwelcome@example.com',
            'phone' => '09170000000',
            'password' => 'K3!p$92_sQ#z1V',
            'password_confirmation' => 'K3!p$92_sQ#z1V',
        ]);

        $response->assertRedirect();

        $user = User::where('email', 'johnwelcome@example.com')->first();
        $this->assertNotNull($user);

        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \App\Notifications\WelcomeNotification::class
        );
    }
}
