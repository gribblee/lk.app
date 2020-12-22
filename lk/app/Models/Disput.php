<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disput extends Model
{
    use HasFactory;

    protected $table = 'disput';

    public static function noClose() {
        return self::where('close', 0)->get();
    }
}
