<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase as TestCase;

class IndexTest extends TestCase
{
    /**
     * @return void
     */
    public function testMessage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/")->assertSee("Hello World...");
        });
    }
}
