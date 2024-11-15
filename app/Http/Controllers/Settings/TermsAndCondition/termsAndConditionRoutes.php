<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\TermsAndCondition\TermsAndConditionController;



Route::middleware('permission:create-termsAndCondition')->post("/", [TermsAndConditionController::class, 'createSingletermsAndCondition']);

Route::middleware('permission:readAll-termsAndCondition')->get("/", [TermsAndConditionController::class, 'getAlltermsAndCondition']);

Route::middleware('permission:readSingle-termsAndCondition')->get("/{id}", [TermsAndConditionController::class, 'getSingletermsAndCondition']);

Route::middleware('permission:update-termsAndCondition')->put("/{id}", [TermsAndConditionController::class, 'updateSingletermsAndCondition']);

Route::middleware('permission:delete-termsAndCondition')->patch("/{id}", [TermsAndConditionController::class, 'deleteSingletermsAndCondition']);
