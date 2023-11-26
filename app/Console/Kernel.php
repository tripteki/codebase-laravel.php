<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    }

    /**
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__."/Commands");
    }
}
