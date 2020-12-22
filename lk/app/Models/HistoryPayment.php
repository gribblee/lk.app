<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPayment extends Model
{
    use HasFactory;

    protected $table = 'payment_history';
    protected $fillable = [
        'user_id',
        'type_transaction',
        'paysum',
        'paybonus',
        'before_balance',
        'after_balance',
        'before_bonus',
        'after_bonus'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
