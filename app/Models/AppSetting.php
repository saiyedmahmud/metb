<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppSetting extends Model
{
    use HasFactory;
    protected $table = 'appSetting';
    protected $primaryKey = 'id';
    protected $fillable = [
        'companyName',
        'dashboardType',
        'tagLine',
        'address',
        'phone',
        'email',
        'website',
        'footer',
        'logo',
        'currencyId',
        'bin',
        'mushak',
        'isSaleCommission',
        'isPos',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currencyId');
    }
}
