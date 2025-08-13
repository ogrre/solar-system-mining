<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirects_unauthenticated_users_to_login(): void
    {
        $request = Request::create('/admin', 'GET');
        $middleware = new AdminMiddleware;

        $response = $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        /** @var \Illuminate\Http\RedirectResponse $redirectResponse */
        $redirectResponse = $response;
        $this->assertStringContainsString('login', $redirectResponse->getTargetUrl());
    }

    public function test_denies_access_to_non_admin_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $request = Request::create('/admin', 'GET');
        $middleware = new AdminMiddleware;

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Access denied. Admin privileges required.');

        $middleware->handle($request, function () {
            return response('OK');
        });
    }

    public function test_allows_access_to_admin_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $request = Request::create('/admin', 'GET');
        $middleware = new AdminMiddleware;

        $response = $middleware->handle($request, function () {
            return response('Admin OK');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Admin OK', $response->getContent());
    }
}
