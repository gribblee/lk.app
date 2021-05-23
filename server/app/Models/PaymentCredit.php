<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCredit extends Model
{
    protected $fillable = [
        'committed',
        'credit_amount',
        'demo',
        'first_name',
        'last_name',
        'middle_name',
        'first_payment',
        'order_id',
        'monthly_payment',
        'order_amount',
        'phone',
        'product',
        'status',
        'term',
        'payment_id',
        'created_at',
    ];

    protected $table = 'payment_credit';

    public function getPayment() {
        return $this->hasOne(Payment::class, 'id', 'payment_id');
    }
}