<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';
    protected $fillable = [
        'id',
        'title',
        'short_description',
        'description',
        'tags',
        'price',
        'images',
        'is_delete'
    ];
}
