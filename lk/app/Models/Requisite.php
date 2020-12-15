<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisite extends Model
{
    use HasFactory;

    protected $table = 'requisites';
    protected $fillable = [
        'id',
        'name',
        'ogrn',
        'inn',
        'kpp',
        'bik',
        'bank',
        'ksch',
        'rsch',
        'jour_address',
        'poste_address',
        'director',
        'user_id',
        'is_delete'
    ];

    public static function getNonDelete(int $userId)
    {
        return self::where('user_id', $userId)->where('is_delete', false);
    }
}
