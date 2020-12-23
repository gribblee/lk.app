<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payment';
    protected $fillable = [
        'type',
        'status',
        'card',
        'payment_id',
        'users_id',
        'requisite_id',
        'paysum',
        'paybonus',
        'before_balance',
        'after_balance',
        'before_bonus',
        'after_bonus',
        'tcv_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function requisite()
    {
        return $this->hasOne(Requisite::class, 'id', 'requisite_id');
    }
}
