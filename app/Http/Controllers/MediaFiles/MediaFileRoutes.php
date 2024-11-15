<?php

use App\Http\Controllers\MediaFiles\MediaFilesController;
use Illuminate\Support\Facades\Route;


Route::middleware('permission:create-media')->post('/upload', [MediaFilesController::class, 'upload']);
Route::middleware('permission:readAll-media')->get('/', [MediaFilesController::class, 'show']);
Route::get('/view/{id}', [MediaFilesController::class, 'view']);
Route::middleware('permission:delete-media')->delete('/', [MediaFilesController::class, 'destroy']);

