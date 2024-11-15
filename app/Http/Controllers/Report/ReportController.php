<?php

namespace App\Http\Controllers\Report;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getAllReports(Request $request)
    {
        try {
            if ($request->query('query') === 'report') {
                // Generate category-wise report
                $report = Invoice::selectRaw('categoryName, sum(amount) as totalAmount')
                    ->groupBy('categoryName')
                    ->get();
            
                // Calculate total donation
                $totalDonation = Invoice::whereHas('category', function ($query) {
                    $query->where('type', 'donation');
                })->sum('amount');
            
                // Calculate total expense
                $totalExpense = Invoice::whereHas('category', function ($query) {
                    $query->where('type', 'expense');
                })->sum('amount');
            
                // Prepare the result
                $result = [
                    'report' => $report,
                    'totalDonation' => $totalDonation,
                    'totalExpense' => $totalExpense
                ];
            
                // Convert array keys to camelCase (if necessary)
                $converted = arrayKeysToCamelCase($result);
            
                // Return the response
                return response()->json($converted, 200);
            }else if($request->query()){

                $pagination = getPagination($request->query());
                $report = Invoice::when($request->query('categoryName'), function ($query) use ($request) {
                        return $query->whereIn('categoryName', explode(',', $request->query('categoryName')));
                    })
                    ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                             return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
                                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
                    })
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();
                
                $totalDonation = $report->where('category.type', 'donation')->sum('amount');
                $totalExpense = $report->where('category.type', 'expense')->sum('amount');

                $total = Invoice::when($request->query('categoryName'), function ($query) use ($request) {
                        return $query->whereIn('categoryName', explode(',', $request->query('categoryName')));
                    })
                    ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                             return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
                                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
                    })
                    ->count();

                $result = [
                    'getAllReport' => arrayKeysToCamelCase($report->toArray()),
                    'totalReport' => $total,
                    'totalDonation' => $totalDonation,
                    'totalExpense' => $totalExpense
                ];
                return response()->json($result, 200);
            }
            
            return response()->json(['message' => 'Invalid query parameter'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
