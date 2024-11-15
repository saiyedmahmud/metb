<?php

namespace App\Http\Controllers\Accounting\Transaction;

use App\Http\Controllers\Controller;

use App\Models\Transaction;
use App\Traits\ErrorTrait;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//
class TransactionController extends Controller
{
    use ErrorTrait;

    public function createTransaction(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $request->validate([
            'date' => 'required',
            'particulars' => 'required',
            'debitId' => 'required',
            'creditId' => 'required',
            'amount' => 'required',
        ]);
        try {
            $data = $request->attributes->get("data");
            $date = new DateTime($request->input('date'));
            $formattedDate = $date->format('Y-m-d H:i:s'); // Adjust the format according to your needs
            $request->merge([
                'date' => $formattedDate,
            ]);


            $transaction = Transaction::create($request->all());
            DB::commit();
            return response()->json($transaction, 201);
        } catch (Exception $err) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred during create transaction. Please try again later.', $err->getMessage()], 500);
        }
    }

    public function getAllTransaction(Request $request): JsonResponse
    {
        $data = $request->attributes->get("data");


        if ($request->query('query') === 'info') {
            try {
                $aggregations = Transaction::where('status', "true")
                    ->selectRaw('COUNT(id) as _count, SUM(amount) as _sum')
                    ->first();

                $response = [
                    '_count' => [
                        'id' => $aggregations->_count ?? 0,
                    ],
                    '_sum' => [
                        'amount' => $aggregations->_sum ?? null,
                    ],
                ];
                return response()->json($response, 200);
            } catch (Exception $error) {
                return response()->json(['error' => 'An error occurred during getting transaction. Please try again later.'], 500);
            }
        } else if ($request->query('query') === 'all') {
            try {
                $allTransaction = Transaction::with('debit', 'credit')
                    ->orderBy('id', 'desc')
                    ->get();
                return $this->response($allTransaction->toArray());
            } catch (Exception $error) {
                return $this->badRequest($error->getMessage());
            }
        } else if ($request->query('query') === 'inactive') {
            try {
                $aggregations = Transaction::query()
                    ->selectRaw('COUNT(id) as totalCount, SUM(amount) as totalAmount')
                    ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
                    })
                    ->where('status', "false")
                    ->first();

                $allTransaction = Transaction::query()
                    ->when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                        return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
                            ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
                    })
                    ->where('status', "false")
                    ->orderBy('id', 'desc')
                    ->with('debit', 'credit')
                    ->get();

                return response()->json([
                    'aggregations' => [
                        '_count' => [
                            'id' => $aggregations->totalCount ?? 0,
                        ],
                        '_sum' => [
                            'amount' => $aggregations->totalAmount ?? null,
                        ],
                    ],
                    'allTransaction' => $allTransaction->toArray(),
                ]);
            } catch (Exception $error) {
                return $this->badRequest($error->getMessage());
            }
        } else if ($request->query('query') === 'search') {
            try {
                $pagination = getPagination($request->query());
                $key = trim($request->query('key'));

                $allTransaction = Transaction::where('id', 'LIKE', '%' . $key . '%')
                    ->orWhere('relatedId', 'LIKE', '%' . $key . '%')
                    ->with('debit', 'credit','store')
                    ->orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $allTransactionCount = Transaction::where('id', 'LIKE', '%' . $key . '%')
                    ->count();

                return $this->response([
                    'getAllTransaction' => $allTransaction->toArray(),
                    'totalTransaction' => $allTransactionCount,
                ]);
            } catch (Exception $error) {
                return $this->badRequest($error->getMessage());
            }
        } else {
            try {
                $pagination = getPagination($request->query());

                $aggregations = Transaction::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                    return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
                        ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
                })
                    ->when($request->query('status'), function ($query) use ($request) {
                        return $query->whereIn('status', explode(',', $request->query('status')));
                    })
                    ->when($request->query('debitId'), function ($query) use ($request) {
                        return $query->whereIn('debitId', explode(',', $request->query('debitId')));
                    })
                    ->when($request->query('creditId'), function ($query) use ($request) {
                        return $query->whereIn('creditId', explode(',', $request->query('creditId')));
                    })
                    ->selectRaw('COUNT(id) as totalCount, SUM(amount) as totalAmount')
                    ->get();


                $allTransaction = Transaction::when($request->query('startDate') && $request->query('endDate'), function ($query) use ($request) {
                    return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->query('startDate'))->startOfDay())
                        ->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->query('endDate'))->endOfDay());
                })
                    ->when($request->query('status'), function ($query) use ($request) {
                        return $query->whereIn('status', explode(',', $request->query('status')));
                    })
                    ->when($request->query('debitId'), function ($query) use ($request) {
                        return $query->whereIn('debitId', explode(',', $request->query('debitId')));
                    })
                    ->when($request->query('creditId'), function ($query) use ($request) {
                        return $query->whereIn('creditId', explode(',', $request->query('creditId')));
                    })
                    ->orderBy('id', 'desc')
                    ->with('debit', 'credit','store')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $totalTransaction = $allTransaction->count();

                $converted = $this->arrayKeysToCamelCase($allTransaction->toArray());
                return response()->json([
                    'aggregations' => [
                        '_count' => [
                            'id' => $aggregations[0]->totalCount ?? 0,
                        ],
                        '_sum' => [
                            'amount' => $aggregations[0]->totalAmount ?? null,
                        ],
                    ],
                    'getAllTransaction' => $converted,
                    'totalTransaction' => $totalTransaction,
                ]);
            } catch (Exception $err) {
                return $this->badRequest($err->getMessage());
            }
        }
    }

    // get a single transaction controller method
    public function getSingleTransaction(Request $request, $id): JsonResponse
    {
        $data = $request->attributes->get("data");


        try {
            $singleTransaction = Transaction::where('id', (int)$id)
                ->with('debit:id,name', 'credit:id,name')
                ->first();

            return $this->response($singleTransaction->toArray());
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    // update a single transaction controller method
    public function updateSingleTransaction(Request $request, $id): JsonResponse
    {
        try {
            $date = Carbon::parse($request->input('date'));

            $updatedTransaction = Transaction::where('id', (int)$id)->update([
                'date' => $date,
                'particulars' => $request->input('particulars'),
                'type' => 'transaction',
                'relatedId' => 0,
                'amount' => $this->takeUptoThreeDecimal((float)$request->input('amount')),
            ]);

            if (!$updatedTransaction) {
                return response()->json(['error' => 'Failed To Update Transaction'], 404);
            }
            return response()->json(['message' => 'Transaction updated successfully'], 200);
        } catch (Exception $error) {
            return response()->json(['error' => 'An error occurred during update transaction. Please try again later.'], 500);
        }
    }

    // delete a single transaction controller method
    public function deleteSingleTransaction(Request $request, $id): JsonResponse
    {
        try {
            $deletedTransaction = Transaction::where('id', (int)$id)->update([
                'status' => $request->input('status'),
            ]);

            if (!$deletedTransaction) {
                return $this->notFound('Transaction not found!');
            }
            return $this->success('Transaction status updated successfully');
        } catch (Exception $error) {
            return response()->json(['error' => 'An error occurred during delete transaction. Please try again later.'], 500);
        }
    }
}
