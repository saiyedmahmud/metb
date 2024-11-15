<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Transaction\TransactionController;



Route::middleware('permission:create-transaction')->post('/', [TransactionController::class, 'createTransaction']);

Route::middleware('permission:readAll-transaction')->get('/', [TransactionController::class, 'getAllTransaction']);

Route::middleware('permission:readSingle-transaction')->get('/{id}', [TransactionController::class, 'getSingleTransaction']);

Route::middleware('permission:update-transaction')->put('/{id}', [TransactionController::class, 'updateSingleTransaction']);

Route::middleware('permission:delete-transaction')->patch('/{id}', [TransactionController::class, 'deleteSingleTransaction']);
