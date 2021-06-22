<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DreamkasReceipt extends Model
{
    use HasFactory;
    protected $table = 'dreamkas_receipt';
    protected $fillable = ['id', 'receiptId', 'status', 'amount', 'type'];
    protected $hidden = ['user_id']; 

    public function user() {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
}
