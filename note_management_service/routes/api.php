<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('notes', NoteController::class);

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::get('notes/view/all', [NoteController::class, 'indexAll']);
});


