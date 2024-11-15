<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\Currency\CurrencyController;




Route::middleware(['permission:create-currency'])->post("/", [CurrencyController::class, 'createSingleCurrency']);

Route::middleware('permission:readAll-currency')->get("/", [CurrencyController::class, 'getAllCurrency']);

Route::middleware('permission:readSingle-currency')->get("/{id}", [CurrencyController::class, 'getSingleCurrency']);

Route::middleware(['permission:update-currency'])->put("/{id}", [CurrencyController::class, 'updateSingleCurrency']);

Route::middleware('permission:delete-currency')->patch("/{id}", [CurrencyController::class, 'deleteSingleCurrency']);
