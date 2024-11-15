<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HR\RolePermission\RoleController;



Route::middleware('permission:create-role')->post('/', [RoleController::class, 'createSingleRole']);

Route::middleware('permission:readAll-role')->get('/', [RoleController::class, 'getAllRole']);

Route::middleware('permission:readSingle-role')->get('/{id}', [RoleController::class, 'getSingleRole']);

Route::middleware('permission:update-role')->put('/{id}', [RoleController::class, 'updateSingleRole']);

Route::middleware('permission:delete-role')->patch('/{id}', [RoleController::class, 'deleteSingleRole']);

