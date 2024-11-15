<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceCategory\InvoiceCategoryController;

Route::middleware('permission:create-invoiceCategory')->post('/',[InvoiceCategoryController::class,'createInvoiceCategory']);
Route::middleware('permission:readAll-invoiceCategory')->get('/',[InvoiceCategoryController::class,'getAllInvoiceCategories']);
Route::middleware('permission:readSingle-invoiceCategory')->get('/{id}',[InvoiceCategoryController::class,'getSingleInvoiceCategory']);
Route::middleware('permission:update-invoiceCategory')->put('/{id}',[InvoiceCategoryController::class,'updateInvoiceCategory']);
Route::middleware('permission:delete-invoiceCategory')->patch('/{id}',[InvoiceCategoryController::class,'deleteInvoiceCategory']);