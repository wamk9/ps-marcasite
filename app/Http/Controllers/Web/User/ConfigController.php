<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function pageIndex()
    {
        return Inertia::render('User/Config/List');
    }
}
