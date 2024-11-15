<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\CartOrder;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // public function getDashboardData(Request $request): JsonResponse
    // {
    //     try {
    //         $data = $request->attributes->get('data');

    //         //            if ($data['role'] === 'super-admin') {
    //         //
    //         //                $appData = AppSetting::first();
    //         //                if (!$appData) {
    //         //                    return response()->json(['error' => 'App settings not found'], 404);
    //         //                }
    //         //                if (!in_array($appData->dashboardType, ['inventory', 'e-commerce', 'both'])) {
    //         //                    return response()->json(['error' => 'Invalid dashboard type'], 400);
    //         //                }
    //         //
    //         //                if ($appData->dashboardType === 'inventory') {
    //         //
    //         //                    $allSaleInvoices = SaleInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(paidAmount) as paidAmount, SUM(dueAmount) as dueAmount, SUM(profit) as profit, date')
    //         //                        ->get();
    //         //
    //         //
    //         //                    $totalSaleInvoice = SaleInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount,SUM(paidAmount) as paidAmount, SUM(dueAmount) as dueAmount, SUM(profit) as profit, date')
    //         //                        ->count();
    //         //
    //         //                    $allPurchaseInvoice = PurchaseInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount,SUM(dueAmount) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //         //                        ->get();
    //         //
    //         //                    $totalPurchaseInvoice = PurchaseInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount,SUM(dueAmount) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //         //                        ->count();
    //         //
    //         //                    //total sale and total purchase amount is calculated by subtracting total discount from total amount (saiyed)
    //         //                    $cartInfo = [
    //         //                        'totalSaleInvoice' => $totalSaleInvoice,
    //         //                        'totalSaleAmount' => $allSaleInvoices->sum('totalAmount'),
    //         //                        'totalSaleDue' => $allSaleInvoices->sum('dueAmount'),
    //         //                        'totalPurchaseInvoice' => $totalPurchaseInvoice,
    //         //                        'totalPurchaseAmount' => $allPurchaseInvoice->sum('totalAmount'),
    //         //                        'totalPurchaseDue' => $allPurchaseInvoice->sum('dueAmount')
    //         //                    ];
    //         //                    return response()->json($cartInfo, 200);
    //         //
    //         //                } else if ($appData->dashboardType === 'e-commerce') {
    //         //                    $startDate = $request->query('startDate') . ' 00:00:00';
    //         //                    $endDate = $request->query('endDate') . ' 23:59:59';
    //         //
    //         //
    //         //                    $allOrders = CartOrder::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(due) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //         //                        ->get();
    //         //
    //         //
    //         //                    $allPendingOrders = CartOrder::where('orderStatus', 'PENDING')
    //         //                        ->whereBetween('date', [$startDate, $endDate])
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->count();
    //         //                    $allReceivedOrders = CartOrder::where('orderStatus', 'RECEIVED')
    //         //                        ->whereBetween('date', [$startDate, $endDate])
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->count();
    //         //
    //         //
    //         //                    $cartInfo = [
    //         //                        "totalOrder" => $allOrders->count(),
    //         //                        "totalPendingOrder" => $allPendingOrders,
    //         //                        "totalReceivedOrder" => $allReceivedOrders,
    //         //                        "totalSaleAmount" => $allOrders->sum('totalAmount')
    //         //                    ];
    //         //                    return response()->json($cartInfo, 200);
    //         //                } else if ($appData->dashboardType === 'both') {
    //         //
    //         //                    $allSaleInvoice = SaleInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(paidAmount) as paidAmount, SUM(dueAmount) as dueAmount, SUM(profit) as profit, date')
    //         //                        ->get();
    //         //
    //         //                    $allPurchaseInvoice = PurchaseInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(dueAmount) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //         //                        ->get();
    //         //
    //         //                    $totalPurchaseInvoice = PurchaseInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(dueAmount) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //         //                        ->count();
    //         //
    //         //                    $totalSaleInvoice = SaleInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(paidAmount) as paidAmount, SUM(dueAmount) as dueAmount, SUM(profit) as profit, date')
    //         //                        ->count();
    //         //
    //         //                    $cartOrderSaleInfo = CartOrder::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //         //                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //         //                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //         //                    })
    //         //                        ->groupBy('date')
    //         //                        ->orderBy('date', 'desc')
    //         //                        ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(due) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //         //                        ->get();
    //         //
    //         //                    $cardInfo = [
    //         //                        'totalPurchaseInvoice' => $totalPurchaseInvoice,
    //         //                        'totalPurchaseAmount' => $allPurchaseInvoice->sum('totalAmount'),
    //         //                        'totalPurchaseDue' => $allPurchaseInvoice->sum('dueAmount'),
    //         //                        'totalSaleInvoice' => $totalSaleInvoice + $cartOrderSaleInfo->count(),
    //         //                        'totalSaleAmount' => $allSaleInvoice->sum('totalAmount') + $cartOrderSaleInfo->sum('totalAmount'),
    //         //                        'totalSaleDue' => $allSaleInvoice->sum('dueAmount') + $cartOrderSaleInfo->sum('dueAmount'),
    //         //                    ];
    //         //
    //         //                    return response()->json($cardInfo, 200);
    //         //                } else {
    //         //                    return response()->json(['error' => 'Invalid dashboard type'], 400);
    //         //                }
    //         //            }
    //         //            else {
    //         $appData = AppSetting::first();
    //         if (!$appData) {
    //             return response()->json(['error' => 'App settings not found'], 404);
    //         }
    //         if (!in_array($appData->dashboardType, ['inventory', 'e-commerce', 'both'])) {
    //             return response()->json(['error' => 'Invalid dashboard type'], 400);
    //         }

    //         if ($appData->dashboardType === 'inventory') {
    //             $storeId = $data['storeId'];

    //             if ($data['role'] !== 'super-admin') {
    //                 $storeValidation = $this->userStoreAuth($request, $storeId);
    //                 if (!$storeValidation) {
    //                     return $this->unauthorized("unauthorized access!");
    //                 }
    //             }
    //             if ($request->query('storeId') && $data['role'] !== 'super-admin') {
    //                 $arrayOfStoreId = explode(',', $request->query('storeId'));
    //                 foreach ($arrayOfStoreId as $itemId) {
    //                     $storeValidation = $this->userStoreAuth($request, $itemId);
    //                     if (!$storeValidation) {
    //                         return $this->unauthorized("unauthorized access!");
    //                     }
    //                 }
    //             }

    //             // === === === === === === === === === === === === === === === //
    //             // === === === calculation of sale === === === //
    //             // === === === === === === === === === === === === === === === //

    //             $allSaleInvoices = SaleInvoice::when($data['role'] !== 'super-admin', function ($query) use ($storeId, $request) {
    //                 if ($request->query('storeId')) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 } else {
    //                     return $query->where('storeId', $storeId);
    //                 }
    //             })
    //                 ->when($data['role'] === 'super-admin' && $request->query('storeId'), function ($query) use ($request) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 })
    //                 ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                     return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
    //                         ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
    //                 })
    //                 ->orderBy('date', 'desc')
    //                 ->get();

    //             $totalSaleInvoice = SaleInvoice::when($data['role'] !== 'super-admin', function ($query) use ($storeId, $request) {
    //                 if ($request->query('storeId')) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 } else {
    //                     return $query->where('storeId', $storeId);
    //                 }
    //             })
    //                 ->when($data['role'] === 'super-admin' && $request->query('storeId'), function ($query) use ($request) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 })
    //                 ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                     return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
    //                         ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
    //                 })
    //                 ->count();

    //             $saleInvoicesIds = $allSaleInvoices->pluck('id')->toArray();
    //             // modify data to actual data of sale invoice's current value by adjusting with transactions and returns

    //             // transaction of the total amount
    //             $totalSaleCommissionAmount = Transaction::where('type', 'saleCommission')
    //                 ->whereIn('relatedId', $saleInvoicesIds)
    //                 ->where(function ($query) {
    //                     $query->where('debitId', 4);
    //                 })
    //                 ->with('debit:id,name', 'credit:id,name')
    //                 ->get();

    //             // transaction of the total amount
    //             $totalSaleCommissionAmountOfReturn = Transaction::where('type', 'saleCommission_return')
    //                 ->whereIn('relatedId', $saleInvoicesIds)
    //                 ->where(function ($query) {
    //                     $query->where('creditId', 4);
    //                 })
    //                 ->with('debit:id,name', 'credit:id,name')
    //                 ->get();

    //             // transaction of the total amount
    //             $totalSaleNetAmount = Transaction::where('type', 'sale')
    //                 ->whereIn('relatedId', $saleInvoicesIds)
    //                 ->where(function ($query) {
    //                     $query->where('debitId', 4);
    //                 })
    //                 ->get();

    //             // calculate with sales commission
    //             $totalAmount = ($totalSaleNetAmount->sum('amount') + ($totalSaleCommissionAmount ? $totalSaleCommissionAmount->sum('amount') : 0) - ($totalSaleCommissionAmountOfReturn ? $totalSaleCommissionAmountOfReturn->sum('amount') : 0));

    //             // transaction of the paidAmount
    //             $totalSalePaidAmount = Transaction::where('type', 'sale')
    //                 ->whereIn('relatedId', $saleInvoicesIds)
    //                 ->where(function ($query) {
    //                     $query->orWhere('creditId', 4);
    //                 })
    //                 ->get();

    //             // transaction of the total amount
    //             $totalSaleAmountOfReturn = Transaction::where('type', 'sale_return')
    //                 ->whereIn('relatedId', $saleInvoicesIds)
    //                 ->where(function ($query) {
    //                     $query->where('creditId', 4);
    //                 })
    //                 ->get();

    //             // transaction of the total instant return
    //             $totalSaleInstantReturnAmount = Transaction::where('type', 'sale_return')
    //                 ->whereIn('relatedId', $saleInvoicesIds)
    //                 ->where(function ($query) {
    //                     $query->where('debitId', 4);
    //                 })
    //                 ->get();

    //             // calculate grand total due amount
    //             $totalSaleDueAmount = (($totalAmount - $totalSaleAmountOfReturn->sum('amount')) - $totalSalePaidAmount->sum('amount')) + $totalSaleInstantReturnAmount->sum('amount');


    //             // === === === === === === === === === === === === === === === //
    //             // === === === calculation of purchase === === === //
    //             // === === === === === === === === === === === === === === === //

    //             $allPurchaseInvoice = PurchaseInvoice::when($data['role'] !== 'super-admin', function ($query) use ($storeId, $request) {
    //                 if ($request->query('storeId')) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 } else {
    //                     return $query->where('storeId', $storeId);
    //                 }
    //             })
    //                 ->when($data['role'] === 'super-admin' && $request->query('storeId'), function ($query) use ($request) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 })
    //                 ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                     return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
    //                         ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
    //                 })
    //                 ->orderBy('date', 'desc')
    //                 ->get();

    //             $totalPurchaseInvoice = PurchaseInvoice::when($data['role'] !== 'super-admin', function ($query) use ($storeId, $request) {
    //                 if ($request->query('storeId')) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 } else {
    //                     return $query->where('storeId', $storeId);
    //                 }
    //             })
    //                 ->when($data['role'] === 'super-admin' && $request->query('storeId'), function ($query) use ($request) {
    //                     return $query->whereIn('storeId', explode(',', $request->query('storeId')));
    //                 })
    //                 ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                     return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
    //                         ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
    //                 })
    //                 ->count();

    //             $purchaseInvoiceIds = $allPurchaseInvoice->pluck('id')->toArray();

    //             // transaction of the total amount
    //             $totalPurchaseAmount = Transaction::where('type', 'purchase')
    //                 ->whereIn('relatedId', $purchaseInvoiceIds)
    //                 ->where(function ($query) {
    //                     $query->where('creditId', 5);
    //                 })
    //                 ->get();

    //             // dd($allPurchaseInvoice);

    //             // transaction of the paidAmount
    //             $totalPurchasePaidAmount = Transaction::where('type', 'purchase')
    //                 ->whereIn('relatedId', $purchaseInvoiceIds)
    //                 ->where(function ($query) {
    //                     $query->orWhere('debitId', 5);
    //                 })
    //                 ->get();

    //             // transaction of the total amount
    //             $totalPurchaseAmountOfReturn = Transaction::where('type', 'purchase_return')
    //                 ->whereIn('relatedId', $purchaseInvoiceIds)
    //                 ->where(function ($query) {
    //                     $query->where('debitId', 5);
    //                 })
    //                 ->get();

    //             // transaction of the total instant return
    //             $totalInstantPurchaseReturnAmount = Transaction::where('type', 'purchase_return')
    //                 ->whereIn('relatedId', $purchaseInvoiceIds)
    //                 ->where(function ($query) {
    //                     $query->where('creditId', 5);
    //                 })
    //                 ->get();

    //             // calculate grand total due amount
    //             $totalPurchaseDueAmount = (($totalPurchaseAmount->sum('amount') - $totalPurchaseAmountOfReturn->sum('amount')) - $totalPurchasePaidAmount->sum('amount')) + $totalInstantPurchaseReturnAmount->sum('amount');

    //             $cartInfo = [
    //                 'totalSaleInvoice' => $totalSaleInvoice,
    //                 'totalSaleAmount' => $totalAmount + ($totalSaleCommissionAmountOfReturn ? $totalSaleCommissionAmountOfReturn->sum('amount') : 0),
    //                 'totalSaleCommission' => ($totalSaleCommissionAmount ? $totalSaleCommissionAmount->sum('amount') : 0) - ($totalSaleCommissionAmountOfReturn ? $totalSaleCommissionAmountOfReturn->sum('amount') : 0),
    //                 'totalSaleDue' => $totalSaleDueAmount,
    //                 'totalPurchaseInvoice' => $totalPurchaseInvoice,
    //                 'totalPurchaseAmount' => ($totalPurchaseAmount ? $totalPurchaseAmount->sum('amount') : 0),
    //                 'totalPurchaseDue' => $totalPurchaseDueAmount
    //             ];
    //             return response()->json($cartInfo, 200);
    //         } else if ($appData->dashboardType === 'e-commerce') {
    //             $startDate = $request->query('startDate') . ' 00:00:00';
    //             $endDate = $request->query('endDate') . ' 23:59:59';


    //             $allOrders = CartOrder::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                 return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //                     ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //             })
    //                 ->groupBy('date')
    //                 ->orderBy('date', 'desc')
    //                 ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(due) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //                 ->get();


    //             $allPendingOrders = CartOrder::where('orderStatus', 'PENDING')
    //                 ->whereBetween('date', [$startDate, $endDate])
    //                 ->orderBy('date', 'desc')
    //                 ->count();
    //             $allReceivedOrders = CartOrder::where('orderStatus', 'RECEIVED')
    //                 ->whereBetween('date', [$startDate, $endDate])
    //                 ->orderBy('date', 'desc')
    //                 ->count();


    //             $cartInfo = [
    //                 "totalOrder" => $allOrders->count(),
    //                 "totalPendingOrder" => $allPendingOrders,
    //                 "totalReceivedOrder" => $allReceivedOrders,
    //                 "totalSaleAmount" => $allOrders->sum('totalAmount')
    //             ];
    //             return response()->json($cartInfo, 200);
    //         } else if ($appData->dashboardType === 'both') {

    //             $allSaleInvoice = SaleInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                 return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //                     ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //             })
    //                 ->groupBy('date')
    //                 ->orderBy('date', 'desc')
    //                 ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(paidAmount) as paidAmount, SUM(dueAmount) as dueAmount, SUM(profit) as profit, date')
    //                 ->get();

    //             $allPurchaseInvoice = PurchaseInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                 return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //                     ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //             })
    //                 ->groupBy('date')
    //                 ->orderBy('date', 'desc')
    //                 ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(dueAmount) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //                 ->get();

    //             $totalPurchaseInvoice = PurchaseInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                 return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //                     ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //             })
    //                 ->groupBy('date')
    //                 ->orderBy('date', 'desc')
    //                 ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(dueAmount) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //                 ->count();

    //             $totalSaleInvoice = SaleInvoice::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                 return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //                     ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //             })
    //                 ->groupBy('date')
    //                 ->orderBy('date', 'desc')
    //                 ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(paidAmount) as paidAmount, SUM(dueAmount) as dueAmount, SUM(profit) as profit, date')
    //                 ->count();

    //             $cartOrderSaleInfo = CartOrder::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
    //                 return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
    //                     ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
    //             })
    //                 ->groupBy('date')
    //                 ->orderBy('date', 'desc')
    //                 ->selectRaw('COUNT(id) as countedId, SUM(totalAmount) as totalAmount, SUM(due) as dueAmount, SUM(paidAmount) as paidAmount, date')
    //                 ->get();

    //             $cardInfo = [
    //                 'totalPurchaseInvoice' => $totalPurchaseInvoice,
    //                 'totalPurchaseAmount' => $allPurchaseInvoice->sum('totalAmount'),
    //                 'totalPurchaseDue' => $allPurchaseInvoice->sum('dueAmount'),
    //                 'totalSaleInvoice' => $totalSaleInvoice + $cartOrderSaleInfo->count(),
    //                 'totalSaleAmount' => $allSaleInvoice->sum('totalAmount') + $cartOrderSaleInfo->sum('totalAmount'),
    //                 'totalSaleDue' => $allSaleInvoice->sum('dueAmount') + $cartOrderSaleInfo->sum('dueAmount'),
    //             ];

    //             return response()->json($cardInfo, 200);
    //         } else {
    //             return response()->json(['error' => 'Invalid dashboard type'], 400);
    //         }
    //         // }
    //     } catch (Exception $err) {
    //         return response()->json(['error' => $err->getMessage()], 500);
    //     }
    // }
}
