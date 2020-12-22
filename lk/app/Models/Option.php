<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $table = 'options';
    protected $fillable = [
        'id',
        'name',
        'value'
    ];

    public static function getKeyValue()
    {
        $keyValue = [];
        foreach(self::all() as $option)
        {
            $keyValue[$option['name']] = $option['value'];
        }
        return $keyValue;
    }
}
