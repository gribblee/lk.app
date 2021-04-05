<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Region;
use App\Models\Company\Issues;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';

    protected $fillable = ['name', 'description', 'region_id', 'rating', 'user_id', 'address', 'files', 'is_success'];
    protected $hidden = ['user_id'];

    public function issues()
    {
        return $this->hasMany(Issues::class, 'company_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }
}
