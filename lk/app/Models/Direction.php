<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'average_check',
        'cost_price',
        'conversion_contract',
        'conversion_meetings',
        'description',
        'extra',
        'iframe_url',
        'name',
        'categories'
    ];
}
