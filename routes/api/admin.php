<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\auth\AuthController;
use App\Http\Controllers\Posts\PostController;
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

Route::middleware(["auth"])->group(function() {

    Route::controller(AuthController::class)->prefix("auth")->group(function(){
        Route::post("/profile" ,"profile");
        Route::post("/refresh" ,"refresh");
        Route::post("/logout" ,"logout");
    });

    Route::controller(PostController::class)->prefix("post")->group(function(){
        Route::get("/index" ,"index");
        Route::post("/store" ,"store");
        Route::post("/update" ,"update");
        Route::post("/delete" ,"destroy");
        Route::post("/change-status" ,"ChangeStatusPost");
    });

    Route::controller(NotificationController::class)->prefix("notifications")->group(function(){
        Route::get("/show-all" ,"showAll");
        Route::post("/mark-all-as-read" ,"markAllAsRead");
        Route::post("/delete-all" ,"destroyAll");
        Route::post("/mark-as-read" ,"markOneAsRead");
    });


});

