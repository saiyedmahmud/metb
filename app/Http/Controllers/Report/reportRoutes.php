<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\ReportController;

Route::get('/',[ReportController::class,'getAllReports']);