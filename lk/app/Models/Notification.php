<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = [
        'id', 'description', 'is_view', 'user_id', 'created_at', 'updated_at'
    ];

    public static function nonView($userId)
    {
        return self::where([
            'is_view' => false,
            'user_id' => $userId
        ]);
    }

    public static function updatedAllView($userId, $isView = true)
    {
        return self::where([
            'user_id' => $userId
        ])->update([
            'is_view' => $isView
        ]);
    }
}
