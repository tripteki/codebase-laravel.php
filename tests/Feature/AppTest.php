<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppTest extends TestCase
{
    /**
     * @return void
     */
    public function test_api_version(): void
    {
        $response = $this->getJson("/api/version");

        $response->assertStatus(200)
            ->assertJsonStructure([ "version", ]);
    }

    /**
     * @return void
     */
    public function test_api_status(): void
    {
        $response = $this->getJson("/api/status");

        $response->assertStatus(200)
            ->assertJsonStructure([ "status", "info", ]);
    }
}
