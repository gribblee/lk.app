<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppToken extends Model
{
    use HasFactory;

    protected $table = "api_token";
    protected $fillable = ['hash', 'user_id', 'count_deals', 'direction_id', 'created_at', 'updated_at'];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }
}
