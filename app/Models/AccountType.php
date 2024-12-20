<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    use HasFactory;
    protected $table = 'accountType';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'status',
    ];

    public function account(): HasMany
    {
        return $this->hasMany(Account::class, 'accountTypeId');
    }
}
