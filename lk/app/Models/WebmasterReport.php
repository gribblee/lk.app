<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebmasterReport extends Model
{
    use HasFactory;

    protected $table = 'webmaster_report';
    protected $fillable = ['id', 'deal_id', 'api_id', 'utm', 'amount'];

    public function api (){
        return $this->hasOne(AppToken::class, 'api_id', 'id');
    }

    public function deal() {
        return $this->hasOne(Deal::class, 'deal_id', 'id');
    }
}
