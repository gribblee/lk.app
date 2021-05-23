<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoBid extends Model
{
    use HasFactory;

    protected $table = 'demo_bid';
    protected $fillable = [
        'bid_id',
        'user_id',
        'request'
    ];
}
