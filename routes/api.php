<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NotesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/forgotpassword', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/resetpassword', [ForgotPasswordController::class, 'resetpassword']);

    Route::post('/createNotes', [NotesController::class, 'createNotes']);
    Route::get('/displayNoteById', [NotesController::class, 'displayNoteById']);
    Route::post('/updateNoteById', [NotesController::class, 'updateNoteById']);
    Route::post('/deleteNoteById', [NotesController::class, 'deleteNoteById']);
});
