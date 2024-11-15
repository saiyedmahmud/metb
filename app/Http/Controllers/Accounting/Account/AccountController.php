<?php

namespace App\Http\Controllers\Accounting\Account;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ProductVat;
use App\Models\SubAccount;
use App\Traits\ErrorTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    use ErrorTrait;

    //create subAccount
    public function createSubAccount(Request $request): JsonResponse
    {
        DB::beginTransaction();
        if ($request->query('query') === 'deleteMany') {
            try {
                $ids = json_decode($request->getContent(), true);

                $accountData = SubAccount::where('isLocked', 'false')
                    ->whereIn('id', $ids)
                    ->get();
                if (count($accountData) === 0) {
                    return $this->badRequest('No Account Found!');
                }

                $deletedSubAccount = SubAccount::where('isLocked', 'false')
                    ->whereIn('id', $ids)
                    ->delete();

                $deletedCount = [
                    'count' => $deletedSubAccount
                ];

                DB::commit();
                return response()->json($deletedCount, 200);
            } catch (Exception $err) {
                DB::rollBack();
                return response()->json(['error' => 'An error occurred during deleting sub account. Please try again later.'], 500);
            }
        } else if ($request->query('query') === 'archiveMany') {
            try {
                $ids = json_decode($request->getContent(), true);

                $accountData = SubAccount::where('isLocked', 'false')
                    ->whereIn('id', $ids)
                    ->get();
                if (count($accountData) === 0) {
                    return $this->badRequest('No Account Found!');
                }

                $archivedSubAccount = SubAccount::where('isLocked', 'false')
                    ->whereIn('id', $ids)
                    ->update([
                        'status' => 'false'
                    ]);

                $archivedCount = [
                    'count' => $archivedSubAccount
                ];

                DB::commit();
                return response()->json($archivedCount, 200);
            } catch (Exception $err) {
                DB::rollBack();
                return response()->json(['error' => 'An error occurred during archiving sub account. Please try again later.'], 500);
            }
        } else if ($request->query('query') === 'restoreMany') {
            try {
                $ids = json_decode($request->getContent(), true);

                $accountData = SubAccount::where('isLocked', 'false')
                    ->whereIn('id', $ids)
                    ->get();
                if (count($accountData) === 0) {
                    return $this->badRequest('No Account Found!');
                }

                $restoredSubAccount = SubAccount::where('isLocked', 'false')
                    ->whereIn('id', $ids)
                    ->update([
                        'status' => 'true'
                    ]);

                $restoredCount = [
                    'count' => $restoredSubAccount
                ];

                DB::commit();
                return response()->json($restoredCount, 200);
            } catch (Exception $err) {
                DB::rollBack();
                return response()->json(['error' => 'An error occurred during restoring sub account. Please try again later.'], 500);
            }
        } else if ($request->query('query') === 'importCheck') {
            try {

                $systemAccount = [];
                $errorAccount = [];
                $errorDetails = [];

                $createdAccount = [];
                $updatedAccount = [];

                $subAccountData = Account::all();
                $dbAccount = SubAccount::all();
                $allDbAccountData = $dbAccount;

                $requestData = json_decode($request->getContent(), true);

                $allNameList = collect($requestData)->pluck('Name')->toArray();
                $allCodeList = collect($requestData)->pluck('Code')->toArray();

                // check system account
                foreach ($requestData as $key => $item) {
                    $accountData = $allDbAccountData->filter(function ($account) use ($item) {
                        return strtolower($account['name']) === strtolower($item['Name']) && $account['code'] === $item['Code'] && $account['isLocked'] === 'true';
                    })->first();

                    if ($accountData !== null) {
                        array_push($systemAccount, $item);
                        unset($requestData[$key]);
                    }
                }

                // check account type error account
                foreach ($requestData as $key => $item) {
                    $accountTypeData = $subAccountData->filter(function ($account) use ($item) {
                        return strtolower($account['name']) === strtolower($item['Type']);
                    })->first();

                    if (!$accountTypeData) {
                        array_push($errorAccount, $item);
                        array_push($errorDetails, ['line' => $key + 1, 'error' => "This account could not be imported because the Account Type - {$item['Type']} is not a valid Account Type."]);

                        unset($requestData[$key]);
                    }
                }

                $allNameList = collect($requestData)->pluck('Name')->toArray();
                $nameCounts = array_count_values($allNameList);
                $nameUniqueElements = [];
                foreach ($nameCounts as $name => $count) {
                    if ($count > 1) {
                        for ($i = 1; $i < $count; $i++) {
                            $nameUniqueElements[] = $name;
                        }
                    }
                }
                foreach ($requestData as $key => $item) {
                    $name = $item['Name'];

                    if (in_array($name, $nameUniqueElements)) {
                        array_push($errorAccount, $item);
                        array_push($errorDetails, ['line' => $key + 1, 'error' => "This account could not be imported because either the Code or Name was a duplicate of {$item['Code']} - {$item['Name']}"]);

                        unset($requestData[$key]);
                        $index = array_search($name, $nameUniqueElements);
                        if ($index !== false) {
                            array_splice($nameUniqueElements, $index, 1);
                        }
                    }
                }

                $allCodeList = collect($requestData)->pluck('Code')->toArray();
                $codeCounts = array_count_values($allCodeList);
                $codeUniqueElements = [];
                foreach ($codeCounts as $code => $count) {
                    if ($count > 1) {
                        for ($i = 1; $i < $count; $i++) {
                            $codeUniqueElements[] = $code;
                        }
                    }
                }
                foreach ($requestData as $key => $item) {
                    $code = $item['Code'];

                    if (in_array($code, $codeUniqueElements)) {
                        array_push($errorAccount, $item);
                        array_push($errorDetails, ['line' => $key + 1, 'error' => "This account could not be imported because either the Code or Code was a duplicate of {$item['Code']} - {$item['Code']}"]);

                        unset($requestData[$key]);
                        $index = array_search($code, $codeUniqueElements);
                        if ($index !== false) {
                            array_splice($codeUniqueElements, $index, 1);
                        }
                    }
                }

                $remainingNameList = collect($requestData)->pluck('Name')->map(function ($name) {
                    return strtolower($name);
                })->toArray();

                // check updatable and creatable account
                foreach ($requestData as $key => $item) {
                    $nameLower = strtolower($item['Name']);

                    $accountData = $allDbAccountData->first(function ($account) use ($nameLower) {
                        return strtolower($account['name']) === $nameLower && $account['isLocked'] === 'false';
                    });

                    $systemAccountData = $allDbAccountData->first(function ($account) use ($nameLower) {
                        return strtolower($account['name']) === $nameLower && $account['isLocked'] === 'true';
                    });

                    if ($systemAccountData) {
                        array_push($systemAccount, $item);
                        unset($requestData[$key]);
                        continue;
                    }

                    if ($accountData) {
                        array_push($updatedAccount, $item);
                        unset($requestData[$key]);
                        $allDbAccountData = $allDbAccountData->reject(function ($account) use ($nameLower) {
                            return strtolower($account['name']) === $nameLower && $account['isLocked'] === 'false';
                        });
                        continue;
                    } else {
                        array_push($createdAccount, $item);
                        unset($requestData[$key]);
                        continue;
                    }
                }


                $finalResult = [
                    'createdAccount' => $createdAccount,
                    'updatedAccount' => $updatedAccount,
                    'systemAccount' => $systemAccount,
                    'errorAccount' => $errorAccount,
                    'errorDetails' => $errorDetails,
                ];

                DB::commit();
                return $this->response($finalResult, 200);
            } catch (Exception $err) {
                DB::rollBack();

                return response()->json(['error' => 'An error occurred during importing sub account. Please try again later.'], 500);
            }
        }else {
            try {
                $createdSubAccount = SubAccount::create([
                    'name' => $request->input('name'),
                    'code' => $request->input('code') ?? null,
                    'taxId' => $request->input('taxId') ?? null,
                    'description' => $request->input('description') ?? null,
                    'accountId' => $request->input('accountId'),
                ]);
                DB::commit();
                return $this->response($createdSubAccount->toArray(), 201);
            } catch (Exception $err) {
                DB::rollBack();
                return response()->json(['error' => 'An error occurred during creating sub account. Please try again later.'], 500);
            }
        }
    }

    //get all account
    public function getAllAccount(Request $request): JsonResponse
    {
        try {
            $data = $request->attributes->get("data");
            if ($data === null) {
                throw new Exception("Request data is not available.");
            }
            if ($request->query('query') === 'tb') {
                $allAccounts = Account::orderBy('id', 'desc')
                    ->with('accountType', 'subAccount')
                    ->get();


                $accountInfo = [];

                foreach ($allAccounts as $account) {
                    foreach ($account->subAccount as $subAccount) {
                        $totalDebit = $subAccount->debit->where('status', true)->sum('amount');
                        $totalCredit = $subAccount->credit->where('status', true)->sum('amount');
                        $balance = $totalDebit - $totalCredit;

                        $accountInfo[] = [
                            'account' => $account->name,
                            'subAccount' => $subAccount->name,
                            'totalDebit' => $this->takeUptoThreeDecimal($totalDebit),
                            'totalCredit' => $this->takeUptoThreeDecimal($totalCredit),
                            'balance' => $this->takeUptoThreeDecimal($balance),
                        ];
                    }
                }

                $trialBalance = $accountInfo; // Assuming you already have $accountInfo

                $debits = [];
                $credits = [];

                foreach ($trialBalance as $item) {
                    if ($item['balance'] > 0) {
                        $debits[] = $item;
                    }
                    if ($item['balance'] < 0) {
                        $credits[] = $item;
                    }
                }

                // Assuming you have already separated items into $debits and $credits arrays

                $totalDebit = array_reduce($debits, function ($carry, $debit) {
                    return $carry + $debit['balance'];
                }, 0);

                $totalCredit = array_reduce($credits, function ($carry, $credit) {
                    return $carry + $credit['balance'];
                }, 0);

                if (-$totalDebit === $totalCredit) {
                    $match = true;
                } else {
                    $match = false;
                }

                $responseData = [
                    'match' => $match,
                    'totalDebit' => $totalDebit,
                    'totalCredit' => $totalCredit,
                    'debits' => $debits,
                    'credits' => $credits,
                ];

                return $this->response($responseData);
            } elseif ($request->query('query') === 'bs') {
                $allAccount = Account::orderBy('id', 'desc')
                    ->with('accountType', 'subAccount')
                    ->get();


                $accountInfo = [];

                foreach ($allAccount as $account) {
                    foreach ($account->subAccount as $subAccount) {
                        $totalDebit = $subAccount->debit->sum('amount');
                        $totalCredit = $subAccount->credit->sum('amount');
                        $balance = $totalDebit - $totalCredit;


                        // Add the total debit and total credit to each subAccount object
                        $subAccount->totalDebit = $totalDebit;
                        $subAccount->totalCredit = $totalCredit;
                        $subAccount->balance = $balance;

                        // Create an array for the transformed subAccount data
                        $accountInfo[] = [
                            'account' => $account->type,
                            'subAccount' => $subAccount->name,
                            'totalDebit' => $this->takeUptoThreeDecimal($totalDebit),
                            'totalCredit' => $this->takeUptoThreeDecimal($totalCredit),
                            'balance' => $this->takeUptoThreeDecimal($balance),
                        ];
                    }
                }

                $balanceSheet = $accountInfo;
                $assets = [];
                $liabilities = [];
                $equity = [];

                foreach ($balanceSheet as $item) {
                    if ($item['account'] === "Asset" && $item['balance'] !== 0) {
                        $assets[] = $item;
                    }
                    if ($item['account'] === "Liability" && $item['balance'] !== 0) {
                        // Convert negative balance to positive
                        $item['balance'] = -$item['balance'];
                        $liabilities[] = $item;
                    }
                    if ($item['account'] === "Equity" && $item['balance'] !== 0) {
                        // Convert negative balance to positive
                        $item['balance'] = -$item['balance'];
                        $equity[] = $item;
                    }
                }

                $totalAsset = array_reduce($assets, function ($carry, $asset) {
                    return $carry + $asset['balance'];
                }, 0);

                $totalLiability = array_reduce($liabilities, function ($carry, $liability) {
                    return $carry + $liability['balance'];
                }, 0);

                $totalEquity = array_reduce($equity, function ($carry, $equityItem) {
                    return $carry + $equityItem['balance'];
                }, 0);

                if (-$totalAsset === $totalLiability + $totalEquity) {
                    $match = true;
                } else {
                    $match = false;
                }

                $responseData = [
                    'match' => $match,
                    'totalAsset' => $totalAsset,
                    'totalLiability' => $totalLiability,
                    'totalEquity' => $totalEquity,
                    'assets' => $assets,
                    'liabilities' => $liabilities,
                    'equity' => $equity,
                ];

                return $this->response($responseData);
            } elseif ($request->query('query') === 'is') {
                $allAccount = Account::with('accountType', 'subAccount')
                    ->get();
                $accountInfo = [];

                foreach ($allAccount as $account) {
                    foreach ($account->subAccount as $subAccount) {
                        $totalDebit = $subAccount->debit->sum('amount');
                        $totalCredit = $subAccount->credit->sum('amount');
                        $balance = $totalDebit - $totalCredit;

                        // Create an array for the transformed subAccount data
                        $accountInfo[] = [
                            'id' => $subAccount->id,
                            'account' => $account->name,
                            'subAccount' => $subAccount->name,
                            'totalDebit' => $this->takeUptoThreeDecimal($totalDebit),
                            'totalCredit' => $this->takeUptoThreeDecimal($totalCredit),
                            'balance' => $this->takeUptoThreeDecimal($balance),
                        ];
                    }
                }
                $incomeStatement = $accountInfo;
                $revenue = [];
                $expense = [];

                foreach ($incomeStatement as $item) {
                    if ($item['account'] === "Revenue" && $item['balance'] !== 0) {
                        // Convert negative balance to positive
                        $item['balance'] = -$item['balance'];
                        $revenue[] = $item;
                    }
                    if ($item['account'] === "Expense" && $item['balance'] !== 0) {
                        // Convert negative balance to positive
                        $item['balance'] = -$item['balance'];
                        $expense[] = $item;
                    }
                }

                $totalRevenue = array_reduce($revenue, function ($carry, $revenueItem) {
                    return $carry + $revenueItem['balance'];
                }, 0);

                $totalExpense = array_reduce($expense, function ($carry, $expenseItem) {
                    return $carry + $expenseItem['balance'];
                }, 0);

                $profit = $totalRevenue + $totalExpense;

                $responseData = [
                    'totalRevenue' => $totalRevenue,
                    'totalExpense' => $totalExpense,
                    'profit' => $profit,
                    'revenue' => $revenue,
                    'expense' => $expense,
                ];
                return $this->response($responseData);
            } elseif ($request->query('type') === 'sa' && $request->query('query') === 'all') {
                $allSubAccount = SubAccount::where('status', 'true')
                    ->with(['account' => function ($query) {
                        $query->with('accountType')
                            ->orderBy('id', 'desc');
                    }, 'tax'])
                    ->orderBy('id', 'desc')
                    ->get();

                return $this->response($allSubAccount->toArray());
            } elseif ($request->query('type') === 'sa' && $request->query('query') === 'search') {
                $pagination = getPagination($request->query());
                $key = trim($request->query('key'));

                $allSubAccount = SubAccount::where('name', 'LIKE', '%' . $key . '%')
                    ->with(['account' => function ($query) {
                        $query->with('accountType')
                            ->orderBy('id', 'desc');
                    }])
                    ->where('status', 'true')
                    ->orderBy('code', 'asc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                // last balance calculation
                foreach ($allSubAccount as $subAccount) {
                    $assetType = $subAccount['account']['accountType']['name'];

                    $uptoDebit = 0;
                    $uptoCredit = 0;

                    foreach ($subAccount->transaction as $transaction) {
                        $uptoDebit += $transaction->debitAmount;
                        $uptoCredit += $transaction->creditAmount;
                    }

                    if ($assetType === 'asset' || $assetType === 'expense') {
                        $uptoMonthBalance = $uptoDebit -  $uptoCredit;
                    } else {
                        $uptoMonthBalance = $uptoCredit -  $uptoDebit;
                    }

                    $subAccount->lastBalance = $uptoMonthBalance;
                    unset($subAccount->transaction);
                }

                $allSubAccountCount = SubAccount::where('name', 'LIKE', '%' . $key . '%')
                    ->where('status', 'true')
                    ->count();

                return $this->response([
                    'getAllSubAccount' => $allSubAccount->toArray(),
                    'totalSubAccount' => $allSubAccountCount,
                ]);
            } elseif ($request->query('type') === 'sa') {
                $pagination = getPagination($request->query());

                $allSubAccount = SubAccount::when($request->query('status'), function ($query) use ($request) {
                    return $query->whereIn('status', explode(',', $request->query('status')));
                })
                    ->when($request->query('accountTypeId'), function ($query) use ($request) {
                        return $query->whereHas('account.accountType', function ($q) use ($request) {
                            $q->where('id', $request->query('accountTypeId'));
                        });
                    })
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->with('account.accountType', 'tax')
                    ->orderBy('code', 'asc')
                    ->get();

                // last balance calculation
                foreach ($allSubAccount as $subAccount) {
                    $assetType = $subAccount['account']['accountType']['name'];

                    $uptoDebit = 0;
                    $uptoCredit = 0;

                    foreach ($subAccount->transaction as $transaction) {
                        $uptoDebit += $transaction->debitAmount;
                        $uptoCredit += $transaction->creditAmount;
                    }

                    if ($assetType === 'asset' || $assetType === 'expense') {
                        $uptoMonthBalance = $uptoDebit -  $uptoCredit;
                    } else {
                        $uptoMonthBalance = $uptoCredit -  $uptoDebit;
                    }

                    $subAccount->lastBalance = $uptoMonthBalance;


                    unset($subAccount->transaction);
                }

                $allSubAccountCount = SubAccount::when($request->query('status'), function ($query) use ($request) {
                    return $query->whereIn('status', explode(',', $request->query('status')));
                })
                    ->when($request->query('accountTypeId'), function ($query) use ($request) {
                        return $query->whereHas('account.accountType', function ($q) use ($request) {
                            $q->where('id', $request->query('accountTypeId'));
                        });
                    })
                    ->count();

                return $this->response([
                    'getAllSubAccount' => $allSubAccount->toArray(),
                    'totalSubAccount' => $allSubAccountCount,
                ]);
            } elseif ($request->query('query') === 'ma') {

                $allAccount = Account::orderBy('id', 'desc')
                    ->with('accountType')
                    ->get()
                    ->groupBy(function ($account) {
                        return $account->accountType->name;
                    })
                    ->values();
                $converted = $this->arrayKeysToCamelCase($allAccount->toArray());
                return response()->json($converted, 200);
            } else {
                $allAccount = Account::with(['subAccount.credit' => function ($query) {
                    $query->orderBy('id', 'desc');
                }, 'subAccount.debit' => function ($query) {
                    $query->orderBy('id', 'desc');
                }, 'accountType'])->orderBy('id', 'desc')->get();

                return $this->response($allAccount->toArray());
            }
        } catch (Exception $error) {
            return $this->badRequest($error);
        }
    }

    public function getSingleAccount(Request $request, $id): JsonResponse
    {
        try {
            $pagination = getPagination($request->query());
            $currentDate = Carbon::now();

            $uptoMonthDebit = 0;
            $uptoMonthCredit = 0;
            $uptoYearDebit = 0;
            $uptoYearCredit = 0;
            $modifiedTransaction = [];

            $monthlyData = SubAccount::where('id', $id)
                ->with([
                    'account.accountType',
                    'transaction' => function ($query) use ($currentDate) {
                        return $query->whereMonth('date', $currentDate->month)
                            ->whereYear('date', $currentDate->year);
                    }
                ])
                ->first();

            $uptoData = SubAccount::where('id', $id)
                ->with([
                    'account.accountType',
                    'transaction'
                ])
                ->first();

            $assetType = $uptoData->account->accountType->name;

            $singleAccount = SubAccount::with([
                'account.accountType',
                'tax',
                'transaction' => function ($query) use ($pagination) {
                    return $query->skip($pagination['skip'])
                        ->take($pagination['limit'])
                        ->orderBy('date', 'desc')
                        ->with('journal');
                }
            ])
                ->where('id', $id)
                ->first();

            $totalSingleAccount = SubAccount::where('id', $id)
                ->withCount(['transaction'])
                ->first();

            foreach ($monthlyData->transaction as $item) {
                $uptoMonthDebit += $item['debitAmount'];
                $uptoMonthCredit += $item['creditAmount'];
            }

            foreach ($uptoData->transaction as $item) {
                $uptoYearDebit += $item['debitAmount'];
                $uptoYearCredit += $item['creditAmount'];
            }

            foreach ($singleAccount->transaction as $item) {
                $modifiedTransaction[] = [
                    'date' => $item['date'],
                    'description' =>  $item['journal']['narration'],
                    'debitAmount' => $item['debitAmount'],
                    'creditAmount' => $item['creditAmount'],
                ];
            }

            if ($assetType === 'asset' || $assetType === 'expense') {
                $uptoMonthBalance = $uptoMonthDebit - $uptoMonthCredit;
                $uptoYearBalance = $uptoYearDebit - $uptoYearCredit;
            } else {
                $uptoMonthBalance = $uptoMonthCredit - $uptoMonthDebit;
                $uptoYearBalance = $uptoYearCredit - $uptoYearDebit;
            }

            $result = [
                'name' => $singleAccount->name,
                'code' => $singleAccount->code,
                'taxId' => $singleAccount->taxId,
                'accountId' => $singleAccount->accountId,
                'description' => $singleAccount->description,
                'isLocked' => $singleAccount->isLocked,
                'status' => $singleAccount->status,
                'grandData' => [[
                    'code' => $singleAccount->code,
                    'type' => $singleAccount->account->name,
                    'tax' => $singleAccount?->tax?->title,
                    'currentMonth' => $currentDate->format('M-y'),
                    'currentMonthBalance' => $uptoMonthBalance,
                    'lastBalance' => $uptoYearBalance,
                ]],
                'transaction' => $modifiedTransaction,
                'totalTransaction' => $totalSingleAccount->transaction_count
            ];

            return $this->response($result);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    //update the subAccount
    public function updateSubAccount(Request $request, $id): JsonResponse
    {
        try {
            $subAccount = SubAccount::where('id', $id)
                ->first();

            if (!$subAccount) {
                return response()->json(['error' => 'Sub Account not found'], 404);
            }
            if ($subAccount->isLocked === 'true') {
                return response()->json(['error' => 'You Cannot Edit This Account!'], 404);
            }

            $nameValidation = SubAccount::where('name', $request->input('name'))
                ->where('id', '!=', $subAccount->id)
                ->first();
            if ($nameValidation) {
                return response()->json(['error' => 'This Name is already assigned to another Account!'], 404);
            }

            $codeValidation = SubAccount::where('code', $request->input('code'))
                ->where('id', '!=', $subAccount->id)
                ->first();

            if ($codeValidation) {
                return response()->json(['error' => 'This Code is already assigned to another Account!'], 404);
            }

            $subAccount->update($request->all());

            return $this->success('Sub Account updated successfully');
        } catch (Exception $error) {
            return $this->badRequest($error);
        }
    }

    public function deleteSubAccount(Request $request, $id): JsonResponse
    {
        try {
            $account = SubAccount::where('id', $id)
                ->first();

            if (!$account) {
                return response()->json(['error' => 'Sub Account not found'], 404);
            }

            if ($account->isLocked === 'true') {
                return response()->json(['error' => 'You Cannot Delete This Account!'], 404);
            }


            SubAccount::where('id', $id)->delete();
            return $this->success('Sub Account deleted successfully');
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    public function exportAccountingCSV(Request $request)
    {
        try {
            $allSubAccount = SubAccount::where('status', 'true')
                ->with(['account', 'tax'])
                ->get();

            $data = $allSubAccount->toArray();
            $filename = "sub_accounts_" . date('Y-m-d_H-i-s') . ".csv";

            $callback = function () use ($data) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Code', 'Name', 'Tax', 'Type', 'Description']);

                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row['code'],
                        $row['name'],
                        $row['tax']['title'] ?? null,
                        $row['account']['name'],
                        $row['description'],
                    ]);
                }
                fclose($handle);
            };

            return Response::streamDownload($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }
}
