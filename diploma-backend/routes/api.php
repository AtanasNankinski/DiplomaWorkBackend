<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReplicaController;

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

//Testing Routes
Route::get('/connection_test', [TestingController::class, "testApiConnection"]);
//Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/create_admin', [AuthController::class, 'createAdmin']);
//Util Routes
Route::post('/inital_account_picture', [AccountController::class, "initialAccountPicture"]);
//Temp Routes
Route::post('/add_replica', [ReplicaController::class, "addReplica"]);
Route::post('/get_replicas', [ReplicaController::class, "getReplicas"]);
Route::post('/edit_replica', [ReplicaController::class, "editReplica"]);
Route::post('/delete_replica', [ReplicaController::class, 'deleteReplica']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/upload_picture', [AccountController::class, "uploadAccountPicture"]);
    Route::put('/update_name', [AccountController::class, "updateProfileName"]);
    Route::get('/get_profile_pic/{id}', [AccountController::class, "getProfilePic"]);
    Route::post('/upload_picture', [AccountController::class, 'uploadProfilePic']);
});