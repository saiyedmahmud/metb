<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PageSize\PageSizeController;




Route::middleware('permission:create-pageSize')->post('/',[PageSizeController::class,'createPageSize']);
Route::middleware('permission:readAll-pageSize')->get('/',[PageSizeController::class,'getAllPageSizes']);
Route::middleware('permission:readSingle-pageSize')->get('/{id}',[PageSizeController::class,'getSinglePageSize']);
Route::middleware('permission:update-pageSize')->put('/{id}',[PageSizeController::class,'updatePageSize']);
Route::middleware('permission:delete-pageSize')->patch('/{id}',[PageSizeController::class,'deletePageSize']);
