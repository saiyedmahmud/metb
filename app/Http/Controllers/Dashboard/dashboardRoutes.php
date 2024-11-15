<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;



Route::middleware('permission:readAll-dashboard')->get("/", [DashboardController::class, 'getDashboardData']);
