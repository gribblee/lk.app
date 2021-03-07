<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use App\Models\Direction;
use App\Models\Region;
use App\Models\User;

class Bid extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $table = 'bids';
    protected $fillable = [
        'regions',
        'direction_id',
        'category_id',
        'user_id',
        'consumption',
        'is_launch',
        'is_notification',
        'is_delete',
        'is_insurance',
        'daily_limit',
        'insurance',
        'created_at'
    ];

    protected $casts = [
        'regions' => 'json'
    ];

    public static function avgConsumption(int $user_id, int $category_id, bool $yearweek = false)
    {
        return Bid::where('bids.is_delete', false)
            ->where('bids.user_id', $user_id)
            ->where('bids.category_id', $category_id)
            ->when($yearweek, function ($q) {
                return $q->join('deals', function ($join) {
                    return $join->on('deals.bid_id', '=', 'bids.id');
                })->whereRaw("deals.created_at >= date_trunc('week', current_date) - '1 week'::interval AND deals.created_at < date_trunc('week', current_date)");
            })
            ->when($yearweek == false, function ($q) {
                return $q->join('deals', function ($join) {
                    return $join->on('deals.bid_id', '=', 'bids.id');
                })->whereRaw("deals.created_at >= date_trunc('week', current_date)");
            })->avg('bids.consumption') ?? 0;
    }

    public static function dealsCount(int $user_id, int $category_id, bool $yearweek = false)
    {
        return Bid::where('user_id', $user_id)
            ->where('category_id', $category_id)
            ->when($yearweek, function ($q) {
                return $q->join('deals', function ($join) {
                    return $join->on('deals.bid_id', '=', 'bids.id');
                })->whereRaw("deals.created_at >= date_trunc('week', current_date) - '1 week'::interval AND deals.created_at < date_trunc('week', current_date)");
            })
            ->when($yearweek == false, function ($q) {
                return $q->join('deals', function ($join) {
                    return $join->on('deals.bid_id', '=', 'bids.id');
                })->whereRaw("deals.created_at >= date_trunc('week', current_date)");
            })->count('deals.id') ?? 0;
    }

    public static function spentDeals(int $user_id, int $category_id, bool $today = true)
    {
        return Bid::where('user_id', $user_id)
            ->where('category_id', $category_id)
            ->when($today, function ($q) {
                return $q->join('deals', function ($join) {
                    return $join->on('deals.bid_id', '=', 'bids.id');
                })->whereRaw("date_trunc('day', deals.created_at) >= date_trunc('day', current_date)");
            })
            ->sum('consumption');
    }

    public static function maxConsumption()
    {
        $bid = self::where([
            'is_delete' => false,
            'is_launch' => true
        ])
            ->orderBy('consumption', 'DESC')
            ->first();
        if ($bid) {
            return $bid->consumption;
        } else {
            return 0;
        }
    }

    public function dealsToday()
    {
        return $this->hasMany(Deal::class)->whereRaw("date_trunc('day', deals.created_at) = date_trunc('day', current_date)");
    }

    /**
     * Start Ver 1.0
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsToJson(Region::class, 'regions', 'id');
    }
    

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direction_id', 'id');
    }
    /**
     * End Ver 1.0
     */
}
