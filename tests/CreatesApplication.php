<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $application = require __DIR__."/../bootstrap/app.php";
        $application->make(Kernel::class)->bootstrap();

        return $application;
    }
}
