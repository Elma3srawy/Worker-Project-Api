<?php

use App\Http\Controllers\Client\OrderPost\ClientOrderController;
use App\Http\Controllers\Client\OrderPost\ReviewOrderController;
use App\Http\Controllers\Posts\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Worker\auth\AuthController;
use App\Http\Controllers\Worker\auth\VerifyEmailController;
use App\Http\Controllers\Notifications\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->prefix("auth")->group(function(){
  Route::post("/register" ,"register");
  Route::post("/login" ,"login");
});

  // Verify email routes
Route::middleware(['auth:worker'])->group(function () {

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('worker.verification.verify');
});

Route::middleware(["auth:worker","verified"])->group(function() {

    Route::controller(AuthController::class)->prefix("auth")->group(function(){
        Route::post("/profile" ,"profile");
        Route::post("/refresh" ,"refresh");
        Route::post("/logout" ,"logout");
    });

    Route::controller(PostController::class)->prefix("post")->group(function(){
        Route::get("/index" ,"index");
        Route::post("/store" ,"store");
        Route::post("/update" ,"update");
        Route::get("/filter" ,"postFilter");
    });

    Route::controller(NotificationController::class)->prefix("notifications")->group(function(){
        Route::get("/show-all" ,"showAll");
        Route::post("/mark-all-as-read" ,"markAllAsRead");
        Route::post("/delete-all" ,"destroyAll");
        Route::post("/mark-as-read" ,"markOneAsRead");
    });
    Route::controller(ClientOrderController::class)->prefix("order")->group(function(){
        Route::get("/pending" ,"pendingOrder");
        Route::PUT("/change-status" ,"workerChangeStatus");
    });
    Route::controller(ReviewOrderController::class)->prefix("review")->group(function(){
        Route::get("/get-my-review" ,"postRate");
    });




});
