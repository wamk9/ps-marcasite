<?php

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\ConfigController as AdminConfigController;
use App\Http\Controllers\Api\User\CourseController as UserCourseController;
use App\Http\Controllers\Api\User\PaymentController as UserPaymentController;
use App\Http\Controllers\Api\User\ConfigController as UserConfigController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.refresh');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::post('/email/verify', [AuthController::class, 'confirmEmailVerification'])->name('api.email.verify');
    Route::post('/email/send', [AuthController::class, 'sendEmailVerification'])->name('api.email.send');
    Route::post('/payment/buy/{course_id}', [UserPaymentController::class, 'buyCourse'])->name('api.payment.mercadopago');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'admin'
], function () {
    Route::get('/user', [AdminUserController::class, 'index'])->name('api.admin.user.index');
    Route::get('/user/{id}', [AdminUserController::class, 'show'])->name('api.admin.user.show');
    Route::post('/user', [AdminUserController::class, 'store'])->name('api.admin.user.store');
    Route::put('/user/{id}', [AdminUserController::class, 'update'])->name('api.admin.user.update');
    Route::delete('/user/{id}', [AdminUserController::class, 'delete'])->name('api.admin.user.delete');

    Route::get('/config', [AdminConfigController::class, 'index'])->name('api.admin.config.index');
    Route::put('/config', [AdminConfigController::class, 'update'])->name('api.admin.config.update');


    Route::get('/role', [AdminRoleController::class, 'index'])->name('api.admin.role.index');
    Route::get('/category', [AdminCategoryController::class, 'index'])->name('api.admin.category.index');
    Route::get('/course', [AdminCourseController::class, 'index'])->name('api.admin.course.index');
    Route::get('/course/{id}', [AdminCourseController::class, 'show'])->name('api.admin.course.show');
    Route::post('/course', [AdminCourseController::class, 'store'])->name('api.admin.course.store');
    Route::put('/course/{id}', [AdminCourseController::class, 'update'])->name('api.admin.course.update');
    Route::delete('/course/{id}', [AdminCourseController::class, 'delete'])->name('api.admin.course.delete');
    Route::get('/course/{id}/registered', [AdminCourseController::class, 'showRegistered'])->name('api.admin.course.registered');

    Route::get('/course/{id}/pdf', [AdminCourseController::class, 'exportToPdf'])->name('api.admin.course.export.pdf');
    Route::get('/course/{id}/excel', [AdminCourseController::class, 'exportToExcel'])->name('api.admin.course.export.excel');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function () {
    Route::get('/mercadopago/token', [UserConfigController::class, 'showPublicToken'])->name('api.mercadopago.token');
    Route::get('/payment/{id}', [UserPaymentController::class, 'show'])->name('api.user.payment.show');
    Route::post('/payment', [UserPaymentController::class, 'store'])->name('api.user.payment.store');

    Route::get('/course', [UserCourseController::class, 'index'])->name('api.user.course.index');
    Route::get('/my-course', [UserCourseController::class, 'indexMyCourse'])->name('api.user.my-course.index');
    Route::get('/course/{id}', [UserCourseController::class, 'show'])->name('api.user.course.show');
    Route::post('/course', [UserCourseController::class, 'store'])->name('api.user.course.store');
    Route::put('/course/{id}', [UserCourseController::class, 'update'])->name('api.user.course.update');
    Route::delete('/course/{id}', [UserCourseController::class, 'delete'])->name('api.user.course.delete');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Resource not found'
    ], 404);
});
