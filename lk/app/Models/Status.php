<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $fillable = [ 'id', 'name', 'type', 'order' ];

    const FIRST_STATUS = 1000;
    const NO_DISTRIBUTED = 1004;

    public static function noDistributed()
    {
        return Status::where('type', self::NO_DISTRIBUTED)->first();
    }

    public static function firstStatus()
    {
        return Status::where('type', self::FIRST_STATUS)->first();
    }
}
