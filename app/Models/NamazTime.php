<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NamazTime extends Model
{
    use HasFactory;

    protected $table = 'namazTime';

    protected $fillable = [
        'time',
        'azanTime',
    ];
}
