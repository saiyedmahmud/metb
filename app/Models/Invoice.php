<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';

    protected $fillable = [
        'invoiceCategoryId',
        'categoryName',
        'date',
        'amount',
        'createdBy',
        'donnerName',
    ];

    public function category()
    {
        return $this->belongsTo(InvoiceCategory::class, 'invoiceCategoryId');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'createdBy');
    }
}
