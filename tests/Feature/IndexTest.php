<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * @return void
     */
    public function testMessage()
    {
        $this->get("/")->assertStatus(200)->assertJson(["message" => "Hello World..."]);
    }
}
