<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NamazTime\NamazTimeController;

Route::get('/',[NamazTimeController::class,'getAllNamazTimes']);
Route::middleware('permission:create-namazTime')->post('/',[NamazTimeController::class,'createNamazTime']);
Route::middleware('permission:readSingle-namazTime')->get('/{id}',[NamazTimeController::class,'getSingleNamazTime']);
Route::middleware('permission:update-namazTime')->put('/{id}',[NamazTimeController::class,'updateNamazTime']);
Route::middleware('permission:delete-namazTime')->delete('/{id}',[NamazTimeController::class,'deleteNamazTime']);