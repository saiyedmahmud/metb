<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HR\RolePermission\RolePermissionController;



Route::middleware('permission:readSingle-rolePermission')->get("/permission", [RolePermissionController::class, 'rolePermissionByRoleId']);

Route::middleware('permission:create-rolePermission')->post("/", [RolePermissionController::class, 'createRolePermission']);
Route::middleware('permission:delete-rolePermission')->delete("/{id}", [RolePermissionController::class, 'deleteSingleRolePermission']);

