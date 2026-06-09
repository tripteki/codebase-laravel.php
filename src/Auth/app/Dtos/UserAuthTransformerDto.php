<?php

namespace Modules\Auth\App\Dtos;

use Spatie\LaravelData\Data;

class UserAuthTransformerDto extends Data
{
    /**
     * @param int $accessTokenTtl
     * @param int $refreshTokenTtl
     * @param string $accessToken
     * @param string $refreshToken
     * @return void
     */
    public function __construct(
        public int $accessTokenTtl,
        public int $refreshTokenTtl,
        public string $accessToken,
        public string $refreshToken,
    ) {}
}
