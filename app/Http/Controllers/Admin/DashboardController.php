<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    /**
     * Display the admin dashboard.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        return Inertia::render("admin/dashboard/index");
    }
}
