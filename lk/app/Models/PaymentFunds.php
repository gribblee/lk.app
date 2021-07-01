<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFunds extends Model
{
    use HasFactory;

    protected $table = 'payment_funds';

    protected $fillable = [];
    protected $hidden = [];

    public function user() {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
}
