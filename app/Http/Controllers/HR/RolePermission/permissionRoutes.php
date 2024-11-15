<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HR\RolePermission\PermissionController;



Route::get('/', [PermissionController::class, 'getAllPermission']);

