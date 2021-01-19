<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'type',
        'name_with_type',
        'federal_district',
        'kladr_id',
        'fias_id'
    ];
}
