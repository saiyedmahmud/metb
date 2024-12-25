<?php

namespace App\Http\Controllers\InvoiceCategory;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceCategory;
use Illuminate\Http\Request;

class InvoiceCategoryController extends Controller
{

    public function createInvoiceCategory(Request $request)
    {
        try {
            $invoiceCategory = InvoiceCategory::where('name', $request->name)
                ->where('type', $request->type)
                ->first();
            if ($invoiceCategory) {
                return response()->json(['message' => 'Invoice Category already exists'], 400);
            }

            $invoiceCategory = new InvoiceCategory();
            $invoiceCategory->name = $request->name;
            $invoiceCategory->type = $request->type;
            $invoiceCategory->save();

            return response()->json(['message' => 'Invoice Category created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getAllInvoiceCategories(Request $request)
    {
        try {
            if ($request->query('query') === 'all') {
                    $invoiceCategories = InvoiceCategory::all();
                    $converted = arrayKeysToCamelCase($invoiceCategories);
                    return response()->json($converted, 200);
            } else if ($request->query('query') === 'search') {
                $invoiceCategories = InvoiceCategory::where('name', 'like', '%' . $request->query('key'). '%')
                    ->orWhere('type', 'like', '%' . $request->query('key') . '%')
                    ->get();

                $total = InvoiceCategory::where('name', 'like', '%' . $request->query('key'). '%')
                    ->orWhere('type', 'like', '%' . $request->query('key') . '%')
                    ->count();

                $result = [
                    'getAllInvoiceCategory' => arrayKeysToCamelCase($invoiceCategories),
                    'totalInvoiceCategory' => $total
                ];
                return response()->json($result, 200);
            } else if ($request->query()) {
                $pagination = getPagination($request->query());
                $invoiceCategories = InvoiceCategory::
                    when($request->query('name'), function ($query) use ($request) {
                        return $query->whereIn('name', explode(',', $request->query('name')));
                    })
                    ->when($request->query('type'), function ($query) use ($request) {
                        return $query->whereIn('type', explode(',', $request->query('type')));
                    })
                    ->when($request->query('status'), function ($query) use ($request) {
                        return $query->whereIn('status', explode(',', $request->query('status')));
                    })
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();
                $total = InvoiceCategory::
                    when($request->query('name'), function ($query) use ($request) {
                        return $query->whereIn('name', explode(',', $request->query('name')));
                    })
                    ->when($request->query('type'), function ($query) use ($request) {
                        return $query->whereIn('type', explode(',', $request->query('type')));
                    })

                    ->when($request->query('status'), function ($query) use ($request) {
                        return $query->whereIn('status', explode(',', $request->query('status')));
                    })
                    ->count();

                $result = [
                    'getAllInvoiceCategory' => arrayKeysToCamelCase($invoiceCategories),
                    'totalInvoiceCategory' => $total
                ];
                return response()->json($result, 200);
            } else {
                return response()->json(['message' => 'Invalid query'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getSingleInvoiceCategory($id)
    {
        try {

            $invoiceCategory = InvoiceCategory::find($id);
            if (!$invoiceCategory) {
                return response()->json(['message' => 'Invoice Category not found'], 404);
            }

            $converted = arrayKeysToCamelCase($invoiceCategory->toArray());
            return response()->json($converted, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateInvoiceCategory(Request $request, $id)
    {
        try {
            $invoiceCategory = InvoiceCategory::find($id);
            if (!$invoiceCategory) {
                return response()->json(['message' => 'Invoice Category not found'], 404);
            }

            $invoiceCategory->name = $request->name ?? $invoiceCategory->name;
            $invoiceCategory->type = $request->type ?? $invoiceCategory->type;
            $invoiceCategory->save();

            
            return response()->json(['message' => 'Invoice Category updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteInvoiceCategory(Request $request, $id)
    {
        try {
            $invoiceCategory = InvoiceCategory::find($id);
            if (!$invoiceCategory) {
                return response()->json(['message' => 'Invoice Category not found'], 404);
            }

            $invoiceCategory->status = $request->status;
            $invoiceCategory->save();
            return response()->json(['message' => 'Invoice Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
