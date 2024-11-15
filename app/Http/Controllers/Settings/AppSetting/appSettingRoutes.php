<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\AppSetting\AppSettingController;


Route::get('/', [AppSettingController::class, 'getSingleAppSetting']);
Route::middleware("permission:update-setting",)->put("/", [AppSettingController::class, 'updateAppSetting']);
