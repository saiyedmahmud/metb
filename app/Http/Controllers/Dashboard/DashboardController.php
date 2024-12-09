<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
           
            $totalDonation = Invoice::whereHas('category', function ($query) {
                $query->where('type', 'donation');
            })->sum('amount');


            $totalExpense = Invoice::whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })->sum('amount');

            
            $lastWeekJummahCollection = Invoice::whereHas('category', function ($query) {
                $query->where('name', 'JUMMAH COLLECTION');
            })->where('date', '>=', Carbon::now()->subDays(7))->sum('amount');

            
            $lastWeekMagribCollection = Invoice::whereHas('category', function ($query) {
                $query->where('name', 'MAGRIB COLLECTION');
            })->where('date', '>=', Carbon::now()->subDays(7))->sum('amount');

            if ($request->query('query') === 'monthly') {
                $months = [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ];

                $monthlyData = Invoice::selectRaw("MONTHNAME(date) as name, 
                        MONTH(date) as month_number,
                        SUM(CASE WHEN category.type = 'donation' THEN amount ELSE 0 END) as income,
                        SUM(CASE WHEN category.type = 'expense' THEN amount ELSE 0 END) as expense")
                    ->join('invoiceCategory as category', 'invoice.invoiceCategoryId', '=', 'category.id')
                    ->whereYear('date', Carbon::now()->year) 
                    ->groupByRaw('MONTHNAME(date), MONTH(date)')
                    ->orderBy('month_number')
                    ->get()
                    ->keyBy('name');

                $reportData = collect($months)->map(function ($month, $index) use ($monthlyData) {
                    return [
                        'name' => $month,
                        'income' => $monthlyData->get($month)->income ?? 0,
                        'expense' => $monthlyData->get($month)->expense ?? 0,
                    ];
                });
            } else if ($request->query('query') === 'weekly') {
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                $weeklyData = Invoice::selectRaw("DAYNAME(date) as name,
            DAYOFWEEK(date) as day_number,
            SUM(CASE WHEN category.type = 'donation' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN category.type = 'expense' THEN amount ELSE 0 END) as expense")
                    ->join('invoiceCategory as category', 'invoice.invoiceCategoryId', '=', 'category.id')
                    ->whereYear('date', Carbon::now()->year) 
                    ->whereRaw('WEEK(date, 1) = WEEK(CURDATE(), 1)') 
                    ->groupByRaw('DAYNAME(date), DAYOFWEEK(date)')
                    ->orderBy('day_number')
                    ->get()
                    ->keyBy('name');

               
                $reportData = collect($days)->map(function ($day) use ($weeklyData) {
                    return [
                        'name' => $day,
                        'income' => $weeklyData->get($day)->income ?? 0,
                        'expense' => $weeklyData->get($day)->expense ?? 0,
                    ];
                });
            }

            
            $result = [
                'totalDonation' => $totalDonation,
                'totalExpense' => $totalExpense,
                'lastWeekJummahCollection' => $lastWeekJummahCollection,
                'lastWeekMagribCollection' => $lastWeekMagribCollection,
                'reportData' => $reportData ?? [],
            ];

            return response()->json($result, 200);
        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }
}
