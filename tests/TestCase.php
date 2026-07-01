<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use AuthenticatesWithJwt, CreatesApplication, InteractsWithAdminApi, InteractsWithTenancy;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (config("permission.teams")) {
            sync_permissions_team_context();
        }
    }
}
