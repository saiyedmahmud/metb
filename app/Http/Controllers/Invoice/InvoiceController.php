<?php

namespace App\Http\Controllers\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoiceCategory;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class InvoiceController extends Controller
{

    public function createInvoice(Request $request)
    {
        $data = $request->attributes->get('data');
        $category = InvoiceCategory::find($request->invoiceCategoryId);
        if (!$category) {
            return response()->json(['message' => 'Invoice Category not found'], 404);
        }

        $date = Carbon::parse($request->date)->format('Y-m-d');


        $invoice = Invoice::create([
            'invoiceCategoryId' => $request->invoiceCategoryId,
            'categoryName' => $category->name,
            'date' => $date,
            'amount' => $request->amount,
            'createdBy' => $data['sub'],
            'donnerName' => $request->donnerName ?? null,
        ]);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not created'], 500);
        }

        $converted = arrayKeysToCamelCase($invoice->toArray());

        return response()->json($converted, 200);
    }

    public function getAllInvoices(Request $request)
    {
        try {
            if ($request->query('query') === 'all') {
                $invoices = Invoice::with('category:id,name,type', 'user:id,username')
                ->orderBy('id', 'desc')
                ->get();
                $converted = arrayKeysToCamelCase($invoices);
                return response()->json($converted, 200);
            } else if ($request->query('query') === 'search') {
                $pagination = getPagination($request->query());

                $invoices = Invoice::with('category:id,name,type', 'user:id,username')
                    ->where('amount', 'like', '%' . $request->query('key') . '%')
                    ->orWhere('categoryName', 'like', '%' . $request->query('key') . '%')
                    ->orWhere('donnerName', 'like', '%' . $request->query('key') . '%')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->orderBy('id', 'desc')
                    ->get();

                $total = Invoice::where('amount', 'like', '%' . $request->query('key') . '%')
                    ->orWhere('categoryName', 'like', '%' . $request->query('key') . '%')
                    ->orWhere('donnerName', 'like', '%' . $request->query('key') . '%')
                    ->count();

                $result = [
                    'getAllInvoice' => arrayKeysToCamelCase($invoices),
                    'totalInvoice' => $total
                ];
                return response()->json($result, 200);
            } else if ($request->query()) {
                $pagination = getPagination($request->query());

                $invoices = Invoice::with('category:id,name,type', 'user:id,username')
                    ->when($request->query('amount'), function ($query) use ($request) {
                        return $query->whereIn('amount', explode(',', $request->query('amount')));
                    })
                    ->when($request->query('categoryName'), function ($query) use ($request) {
                        return $query->whereIn('categoryName', explode(',', $request->query('categoryName')));
                    })
                    ->when($request->query('donnerName'), function ($query) use ($request) {
                        return $query->whereIn('donnerName', explode(',', $request->query('donnerName')));
                    })
                    ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
                                       ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
               })
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->orderBy('id', 'desc')
                    ->get();

                $total = Invoice::when($request->query('amount'), function ($query) use ($request) {
                    return $query->whereIn('amount', explode(',', $request->query('amount')));
                })
                    ->when($request->query('donnerName'), function ($query) use ($request) {
                        return $query->whereIn('donnerName', explode(',', $request->query('donnerName')));
                    })
                    ->when($request->query('categoryName'), function ($query) use ($request) {
                        return $query->whereIn('categoryName', explode(',', $request->query('categoryName')));
                    })
                    ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate')))
                                       ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate')));
               })
                    ->count();

                $result = [
                    'getAllInvoice' => arrayKeysToCamelCase($invoices),
                    'totalInvoice' => $total
                ];
                return response()->json($result, 200);
            } else {
                return response()->json(['message' => 'Invalid query'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getSingleInvoice($id)
    {
        $invoice = Invoice::with('category:id,name,type', 'user:id,username')->find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $converted = arrayKeysToCamelCase($invoice->toArray());

        return response()->json($converted, 200);
    }

    public function updateInvoice(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        if (isset($request->date)) {
            $date = Carbon::parse($request->date)->format('Y-m-d');
        }
        if (isset($request->invoiceCategoryId)) {
            $category = InvoiceCategory::find($request->invoiceCategoryId);
            if (!$category) {
                return response()->json(['message' => 'Invoice Category not found'], 404);
            }
        }
        $invoice->invoiceCategoryId = $request->invoiceCategoryId ?? $invoice->invoiceCategoryId;
        $invoice->date = $date;
        $invoice->categoryName = $category->name ?? $invoice->categoryName;
        $invoice->amount = $request->amount ?? $invoice->amount;
        $invoice->donnerName = $request->donnerName ?? $invoice->donnerName;
        $invoice->save();

        return response()->json(['message' => 'Invoice updated successfully'], 200);
    }

    public function deleteInvoice(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoice->status = $request->status;
        $invoice->save();
        return response()->json(
            ['message' => 'Invoice deleted successfully'],
            200
        );
    }
}
