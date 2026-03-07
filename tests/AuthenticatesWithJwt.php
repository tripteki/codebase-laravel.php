<?php

namespace Tests;

use App\Models\User;

trait AuthenticatesWithJwt
{
    /**
     * @param \App\Models\User $user
     * @param string $password
     * @return $this
     */
    protected function actingAsJwt(User $user, string $password = "password"): static
    {
        $login = $this->postJson("/api/v1/auth/login", [
            "identifierKey" => "email",
            "identifierValue" => $user->email,
            "password" => $password,
        ]);

        $login->assertStatus(201);

        return $this->withHeader("Authorization", "Bearer ".$login->json("accessToken"));
    }
}
