<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function pageLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function pageLogout()
    {
        return Inertia::render('Auth/Logout');
    }

    public function pageEmailVerification()
    {
        return Inertia::render('Auth/EmailVerification');
    }
}
