<?php

use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\GetImportedController;
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

Route::middleware('auth.basic')
    ->group(function () {
        Route::post('/upload', [FileUploadController::class, 'upload']);
    });

Route::get('/rows', [GetImportedController::class, 'getRows']);
