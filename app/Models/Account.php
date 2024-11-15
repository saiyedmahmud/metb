<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;
    protected $table = 'account';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'AccountTypeId',
    ];

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'accountTypeId');
    }

    public function subAccount(): HasMany
    {
        return $this->hasMany(SubAccount::class, 'accountId');
    }

}
