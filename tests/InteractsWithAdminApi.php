<?php

namespace Tests;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Auth;
use Modules\User\App\Enums\UserEnum;

trait InteractsWithAdminApi
{
    /**
     * @return void
     */
    protected function withoutAdminApiMiddleware(): void
    {
        $this->withoutMiddleware([
            ThrottleRequests::class,
            RedirectIfAuthenticated::class,
        ]);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function centralAdminApi(string $path = ""): string
    {
        $prefix = admin_api_prefix(null);
        $normalized = $path === "" ? "" : (str_starts_with($path, "/") ? $path : "/{$path}");

        return "{$prefix}{$normalized}";
    }

    /**
     * @return User
     */
    protected function resolveCentralAdmin(): User
    {
        return User::query()->where(
            "email",
            UserEnum::SUPERUSER->value."@".config("app.email_server"),
        )->firstOrFail();
    }

    /**
     * @param User $user
     * @param string $password
     * @return string
     */
    protected function loginToken(User $user, string $password = "Password123!"): string
    {
        $this->flushHeaders();

        if (Auth::guard("api")->check()) {
            Auth::guard("api")->logout();
        }

        $payload = [
            "identifierKey" => "email",
            "identifierValue" => $user->email,
            "password" => $password,
        ];

        if (filled($user->tenant_id)) {
            $payload["tenant"] = (string) $user->tenant_id;
        }

        $login = $this->postJson("/api/v1/auth/login", $payload);
        $login->assertStatus(201);

        return (string) $login->json("accessToken");
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $token
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authJson(string $method, string $uri, string $token, array $data = [], array $headers = [])
    {
        if (Auth::guard("api")->check()) {
            Auth::guard("api")->logout();
        }

        return $this->json($method, $uri, $data, array_merge([
            "Authorization" => "Bearer {$token}",
            "Accept" => "application/json",
        ], $headers));
    }

    /**
     * @param string $uri
     * @param string $token
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authGet(string $uri, string $token)
    {
        return $this->authJson("GET", $uri, $token);
    }

    /**
     * @param string $uri
     * @param string $token
     * @param array<string, mixed> $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authPost(string $uri, string $token, array $data = [])
    {
        if (Auth::guard("api")->check()) {
            Auth::guard("api")->logout();
        }

        return $this->withHeader("Authorization", "Bearer {$token}")
            ->post($uri, $data);
    }

    /**
     * @param string $uri
     * @param string $token
     * @param array<string, mixed> $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authPostJson(string $uri, string $token, array $data = [])
    {
        return $this->authJson("POST", $uri, $token, $data);
    }

    /**
     * @param string $uri
     * @param string $token
     * @param array<string, mixed> $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authPut(string $uri, string $token, array $data = [])
    {
        return $this->authJson("PUT", $uri, $token, $data);
    }

    /**
     * @param string $uri
     * @param string $token
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authDelete(string $uri, string $token)
    {
        return $this->authJson("DELETE", $uri, $token);
    }
}
