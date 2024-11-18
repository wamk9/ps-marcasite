<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function pageApproved()
    {
        return Inertia::render('User/Payment/Approved');
    }

    public function pageInProcess()
    {
        return Inertia::render('User/Payment/InProcess');
    }

    public function pagePending()
    {
        return Inertia::render('User/Payment/Pending');
    }

    public function pageRejected()
    {
        return Inertia::render('User/Payment/Rejected');
    }
}
