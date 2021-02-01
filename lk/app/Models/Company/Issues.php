<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Direction;

class Issues extends Model
{
    use HasFactory;

    protected $table = 'company_issues';

    protected $fillable = ['title', 'description', 'priceFrom', 'priceTo', 'company_id', 'direction_id'];

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direction_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
