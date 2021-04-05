<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $fillable = [
        'id',
        'images',
        'title',
        'short_description',
        'description',
        'tags',
        'user_id'
    ];
}
