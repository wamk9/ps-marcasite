<?php

use App\Http\Controllers\Web\Admin\ConfigController as AdminConfigController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\User\ConfigController as UserConfigController;
use App\Http\Controllers\Web\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Web\User\CourseController as UserCourseController;
use App\Http\Controllers\Web\User\UserController as UserUserController;
use App\Http\Controllers\Web\User\PaymentController as UserPaymentController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/login', 301);

Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'pageLogin'])->name('pages.login');
    Route::get('/logout', [AuthController::class, 'pageLogout'])->name('pages.logout');
    Route::get('/email/verify', [AuthController::class, 'pageEmailVerification'])->name('pages.email.verify');
});

Route::group([
    'middleware' => 'web',
    'prefix' => 'admin'
], function () {
    Route::get('/', [AdminDashboardController::class, 'pageIndex'])->name('pages.admin.dashboard.index');
    Route::get('/user', [AdminUserController::class, 'pageIndex'])->name('pages.admin.user.index');
    Route::get('/course', [AdminCourseController::class, 'pageIndex'])->name('pages.admin.course.index');
    Route::get('/config', [AdminConfigController::class, 'pageIndex'])->name('pages.admin.config.index');
});

Route::group([
    'middleware' => 'web',
    'prefix' => 'user'
], function () {
    Route::get('/', [UserDashboardController::class, 'pageIndex'])->name('pages.user.dashboard.index');

    Route::get('/user', [UserUserController::class, 'pageIndex'])->name('pages.user.user.index');

    Route::get('/course', [UserCourseController::class, 'pageIndex'])->name('pages.user.course.index');
    Route::get('/my-course', [UserCourseController::class, 'pageMyCourse'])->name('pages.user.my-course.index');
    Route::get('/course/{id}', [UserCourseController::class, 'pageShow'])->name('pages.user.course.show');

    Route::get('/config', [UserConfigController::class, 'pageIndex'])->name('pages.user.config.index');

    Route::get('/payment/approved', [UserPaymentController::class, 'pageApproved'])->name('pages.user.payment.approved');
    Route::get('/payment/in-process', [UserPaymentController::class, 'pageInProcess'])->name('pages.user.payment.in_process');
    Route::get('/payment/pending', [UserPaymentController::class, 'pagePending'])->name('pages.user.payment.pending');
    Route::get('/payment/rejected', [UserPaymentController::class, 'pageRejected'])->name('pages.user.payment.rejected');
});

Route::get('export/{courseid}/{filename}', function ($courseId, $filename) {
    $path = storage_path('app/public/export/course/' . $courseId . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    return response()->download($path)->deleteFileAfterSend(true);
    //$mimeType = File::mimeType($path);
    //return Response::make(File::get($path), 200, ['Content-Type' => $mimeType])->deleteFileAfterSend(true);
});


