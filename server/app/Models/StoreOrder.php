<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOrder extends Model
{
    use HasFactory;

    protected $table = 'store_order';

    protected $fillable = [
        'id',
        'store_id',
        'user_id'
    ];

    public function order()
    {
        return $this->hasOne('store', 'store_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('users', 'user_id', 'id');
    }
}
