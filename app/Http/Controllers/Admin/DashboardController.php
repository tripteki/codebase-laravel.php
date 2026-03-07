<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function __invoke(): View
    {
        if (hasTenant()) {
            abort(404);
        }

        if (! (bool) config("pulse.enabled", true)) {
            abort(404);
        }

        return view("livewire.admin.dashboard.dashboard-pulse-panel");
    }
}
