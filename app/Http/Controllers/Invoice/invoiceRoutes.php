<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Invoice\InvoiceController;

Route::middleware('permission:create-invoice')->post('/',[InvoiceController::class,'createInvoice']);
Route::middleware('permission:readAll-invoice')->get('/',[InvoiceController::class,'getAllInvoices']);
Route::middleware('permission:readSingle-invoice')->get('/{id}',[InvoiceController::class,'getSingleInvoice']);
Route::middleware('permission:update-invoice')->put('/{id}',[InvoiceController::class,'updateInvoice']);
Route::middleware('permission:delete-invoice')->patch('/{id}',[InvoiceController::class,'deleteInvoice']);
