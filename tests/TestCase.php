<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param string $title
     * @param mixed $data
     * @return void
     */
    protected function explore($title, $data)
    {
        $this->expectNotToPerformAssertions();

        fwrite(STDOUT, "(".$title.")"."\n".var_export($data, true)."\n\n");
    }
}
