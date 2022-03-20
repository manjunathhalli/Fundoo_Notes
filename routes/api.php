<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\CollaboratorController;

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
    Route::get('/displayNotes', [NotesController::class, 'displayNotes']);
    Route::post('/displayNoteById', [NotesController::class, 'displayNoteById']);
    Route::post('/updateNoteById', [NotesController::class, 'updateNoteById']);
    Route::post('/deleteNoteById', [NotesController::class, 'deleteNoteById']);
    Route::post('/pinNoteById', [NotesController::class, 'pinNoteById']);
    Route::post('/unpinNoteById', [NotesController::class, 'unpinNoteById']);
    Route::get('/getAllPinnedNotes', [NotesController::class, 'getAllPinnedNotes']);
    Route::post('/archiveNoteById', [NotesController::class, 'archiveNoteById']);
    Route::post('/unarchiveNoteById', [NotesController::class, 'unarchiveNoteById']);
    Route::get('/getAllArchivedNotes', [NotesController::class, 'getAllArchivedNotes']);
    Route::post('/colourNoteById', [NotesController::class, 'colourNoteById']);
    Route::post('/paginationNote', [NotesController::class, 'paginationNote']);
    Route::post('/searchAllNotes', [NotesController::class, 'searchAllNotes']);
    

    Route::post('/createLabel', [LabelController::class, 'createLabel']);
    Route::get('/readAllLabel', [LabelController::class, 'readAllLabel']);
    Route::post('/updateLabel', [LabelController::class, 'updateLabel']);
    Route::post('/deleteLabel', [LabelController::class, 'deleteLabel']);
    Route::post('/displyLabelById', [LabelController::class, 'displyLabelById']);
    Route::post('/addNoteLabel', [LabelController::class, 'addNoteLabel']);
    Route::post('/deleteNoteLabel', [LabelController::class, 'deleteNoteLabel']);
    Route::post('/displayNoteLabel', [LabelController::class, 'displayNoteLabel']);
    

    Route::post('/addCollaboratorByNoteId', [CollaboratorController::class,'addCollaboratorByNoteId']);
    Route::post('/displayNoteByCollaborator', [CollaboratorController::class,'displayNoteByCollaborator']);
    Route::post('/updateNoteByCollaborator', [CollaboratorController::class,'updateNoteByCollaborator']);
    Route::post('/removeCollaborator', [CollaboratorController::class,'removeCollaborator']);
    Route::get('/getAllCollaborators', [CollaboratorController::class,'getAllCollaborators']);
    
});
