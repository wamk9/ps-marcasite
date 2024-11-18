<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function pageIndex()
    {
        return Inertia::render('Admin/Course/List');
    }
}
