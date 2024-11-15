<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Files\FilesController;



Route::post('/', [FilesController::class, 'create']);
Route::get('/', [FilesController::class, 'index']);
Route::get('/{id}', [FilesController::class, 'show']);


