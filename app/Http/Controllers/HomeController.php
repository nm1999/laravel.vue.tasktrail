<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController
{
    public function index()
    {
        return inertia('Home');
    }
}
