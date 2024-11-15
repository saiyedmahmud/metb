<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubAccount extends Model
{
    use HasFactory;

    protected $table = 'subAccount';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'code',
        'taxId',
        'description',
        'accountId',
        'isLocked',
        'status',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accountId');
    }
}
