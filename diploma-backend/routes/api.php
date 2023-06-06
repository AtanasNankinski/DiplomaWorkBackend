<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\AccountController;

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

//Testing routes
Route::get('/connection_test', [TestingController::class, "testApiConnection"]);
//Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/create_admin', [AuthController::class, 'createAdmin']);
Route::post('/inital_account_picture', [AccountController::class, "initialAccountPicture"]);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/upload_picture', [AccountController::class, "uploadAccountPicture"]);
    Route::put('/update_name', [AccountController::class, "updateProfileName"]);
    Route::get('/get_profile_pic/{id}', [AccountController::class, "getProfilePic"]);
    Route::post('/upload_picture', [AccountController::class, 'uploadProfilePic']);
});