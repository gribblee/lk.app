<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disput extends Model
{
    use HasFactory;

    protected $table = 'disput';

    const STATUS_START = 500; //Спор создан
    const STATUS_CONFIRMED = 501; //Закрыт в пользу клиента 
    const STATUS_REJECT = 502; //Закрыт в пользу сервиса

    public function deal()
    {
        return $this->belongsTo(Deals::class);
    }

    public function disputType()
    {
        return $this->hasOne(DisputType::class);
    }

    public static function noClose()
    {
        return self::where('status', self::STATUS_START)->get();
    }
}
