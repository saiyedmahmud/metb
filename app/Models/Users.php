<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Users extends Model
{
    use HasFactory;

    //create user model
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $hidden = ['refreshToken', 'password', 'isLogin'];
    protected $fillable = [
        'firstName',
        'lastName',
        'username',
        'email',
        'refreshToken',
        'phone',
        'image',
        'password',
        'roleId',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'roleId');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'createdBy');
    }

}
