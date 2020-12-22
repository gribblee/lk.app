<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthSMSToken extends Model
{
    use HasFactory;

    // protected $timestamps = false;
    protected $table = 'users_token';

    protected $fillable = [
        'passphrase',
        'token',
        'user_id',
        'is_verified',
        'response',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
