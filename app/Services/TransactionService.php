<?php

namespace App\Services;

use App\Models\Transaction;
use App\Traits\UpToThreeDecimalTrait;

class TransactionService
{
    use UpToThreeDecimalTrait;

    public function Transaction($date, $storeId, $debitId, $creditId, $totalPrice, $message, $InvoiceId, $type): void
    {
        Transaction::create([
            'date' => $date,
            'storeId' => $storeId,
            'debitId' => $debitId,
            'creditId' => $creditId,
            'amount' => $this->takeUptoThreeDecimal((float)$totalPrice),
            'particulars' => "$message #$InvoiceId",
            'type' => $type,
            'relatedId' => $InvoiceId,
        ]);
    }

    //get transaction data
    public function GetTransaction($type, $debitId = null, $creditId = null)
    {
        return Transaction::where('type', $type)
            ->where(function ($query) use ($debitId, $creditId) {
                $query->where('debitId', $debitId)
                    ->orWhere('creditId', $creditId);
            })
            ->selectRaw('COUNT(id) as id, SUM(amount) as amount')
            ->first();
    }

}
