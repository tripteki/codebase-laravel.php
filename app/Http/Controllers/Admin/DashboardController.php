<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

class DashboardController
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view("livewire.admin.dashboard.index");
    }
}
