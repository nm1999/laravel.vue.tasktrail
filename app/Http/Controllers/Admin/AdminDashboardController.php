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

    public function notifications()
    {
        return inertia('Admin/Notifications/Index');
    }

    public function tasks()
    {
        return inertia('Admin/Task/Index');
    }
    
    public function settings()
    {
        return inertia('Admin/Settings/Index');
    }
}
