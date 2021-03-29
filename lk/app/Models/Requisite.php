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
        'is_delete',
        'requisite_payment_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requisite_id', 'id');
    }

    public static function getNonDelete(int $userId)
    {
        return self::where('user_id', $userId)->where('is_delete', false);
    }
}
