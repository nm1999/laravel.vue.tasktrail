<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController
{
    public function index()
    {
        return inertia('Admin/AdminDashboard');
    }
}
