<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UsersController;
use App\Http\Controllers\User\RefreshTokenController;



Route::post('/login', [UsersController::class, 'login']);
Route::post('/logout', [UsersController::class, 'logout']);
Route::post('/register', [UsersController::class, 'register']);
//refresh token routes
Route::get('/refresh-token', [RefreshTokenController::class, 'validationRefreshToken']);

Route::middleware("permission:readAll-user")->get('/', [UsersController::class, 'getAllUser']);
Route::middleware("permission:readSingle-user")->get('/{id}', [UsersController::class, 'getSingleUser']);
Route::middleware("permission:update-user")->put("/{id}", [UsersController::class, 'updateSingleUser']);
Route::middleware("permission:delete-user")->patch("/{id}", [UsersController::class, 'deleteUser']);


