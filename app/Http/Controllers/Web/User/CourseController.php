<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function pageIndex()
    {
        return Inertia::render('User/Course/List');
    }

    public function pageShow()
    {
        return Inertia::render('User/Course/Show');
    }

    public function pageMyCourse()
    {
        return Inertia::render('User/Course/MyCourse');
    }
}
