<?php

namespace Modules\Auth\App\Services;

use Modules\Auth\App\Dtos\UserAuthTransformerDto;
use Modules\Auth\App\Enums\UserAuthTokenScopeEnum;
use Modules\Auth\App\Events\UserAuthLoggedOut;
use Modules\Auth\App\Events\UserAuthRefreshed;
use App\Models\User;
use Modules\User\App\Dtos\UserTransformerDto;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTokenService
{
    /**
     * @param \App\Models\User $user
     * @return \Modules\Auth\App\Dtos\UserAuthTransformerDto
     */
    public function issue(User $user): UserAuthTransformerDto
    {
        $accessTtlMinutes = (int) config("jwt.ttl", 60);
        $refreshTtlMinutes = (int) config("jwt.refresh_ttl", 3600);

        JWTAuth::factory()->setTTL($accessTtlMinutes);
        $accessToken = JWTAuth::claims([
            "scope" => UserAuthTokenScopeEnum::Access->value,
        ])->fromUser($user);

        JWTAuth::factory()->setTTL($refreshTtlMinutes);
        $refreshToken = JWTAuth::claims([
            "scope" => UserAuthTokenScopeEnum::Refresh->value,
        ])->fromUser($user);

        return UserAuthTransformerDto::from([
            "accessTokenTtl" => $accessTtlMinutes * 60,
            "refreshTokenTtl" => $refreshTtlMinutes * 60,
            "accessToken" => $accessToken,
            "refreshToken" => $refreshToken,
        ]);
    }

    /**
     * @return \Modules\Auth\App\Dtos\UserAuthTransformerDto
     */
    public function refresh(): UserAuthTransformerDto
    {
        $token = JWTAuth::parseToken()->getToken();
        $user = JWTAuth::authenticate();
        JWTAuth::invalidate($token);

        event(new UserAuthRefreshed((string) $user->getKey()));

        return $this->issue($user);
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        $user = JWTAuth::parseToken()->authenticate();
        JWTAuth::parseToken()->invalidate(true);

        event(new UserAuthLoggedOut(UserTransformerDto::fromUser($user)));

        return true;
    }
}
