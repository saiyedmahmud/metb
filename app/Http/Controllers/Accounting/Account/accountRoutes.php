<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Account\AccountController;


Route::middleware('permission:create-transaction')->post('/', [AccountController::class, 'createSubAccount']);

Route::middleware('permission:readAll-transaction')->get('/', [AccountController::class, 'getAllAccount']);

Route::middleware('permission:readAll-transaction')->get('/exportCSV', [AccountController::class, 'exportAccountingCSV']);

Route::middleware('permission:readSingle-transaction')->get('/{id}', [AccountController::class, 'getSingleAccount']);

Route::middleware('permission:delete-transaction')->patch('/{id}', [AccountController::class, 'deleteSubAccount']);

Route::middleware('permission:update-transaction')->put('/{id}', [AccountController::class, 'updateSubAccount']);
