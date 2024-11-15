<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCategory extends Model
{
    use HasFactory;

    protected $table = 'invoiceCategory';

    protected $fillable = [
        'name',
        'type',
    ];

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'invoiceCategoryId');
    }
}
