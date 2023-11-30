<?php

use App\Http\Controllers\Posts\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\auth\AuthController;
use App\Http\Controllers\Client\auth\VerifyEmailController;
use App\Http\Controllers\Client\OrderPost\ClientOrderController;
use App\Http\Controllers\Client\OrderPost\ReviewOrderController;

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
Route::middleware(['auth:client'])->group(function () {
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('client.verification.verify');
});



Route::middleware(["auth:client","verified"])->group(function() {

    Route::controller(AuthController::class)->prefix("auth")->group(function(){
        Route::post("/profile" ,"profile");
        Route::post("/refresh" ,"refresh");
        Route::post("/logout" ,"logout");
    });

    Route::controller(PostController::class)->prefix("post")->group(function(){
        Route::get("/index" ,"index");
    });
    Route::controller(ClientOrderController::class)->prefix("order")->group(function(){
        Route::post("/" ,"addOrder");
        Route::get("/get-my-order" ,"getMyOrder");
    });
    Route::controller(ReviewOrderController::class)->prefix("order/review")->group(function(){
        Route::post("/" ,"store");
    });

});

