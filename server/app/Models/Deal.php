<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\DealFile;
use App\Models\Direction;
use App\Models\Region;
use App\Models\Disput;
use App\Models\Status;

class Deal extends Model
{
    use HasFactory;

    protected $table = 'deals';
    protected $fillable = [
        'is_view',
        'amount',
        'amount_bonus',
        'name',
        'phone',
        'email',
        'is_manager_view',
        'utm',
        'region_id',
        'direction_id',
        'bid_id',
        'status_id',
        'token_id',
        'request',
        'is_insurance',
        'user_id',
        'was_online'
    ];

    public static function getCount(string $role = '', int $user_id = 0, int $user_category = 0)
    {
        return self::where('is_view', false)
            ->when($role == 'ROLE_USER', function ($q) use ($user_id, $user_category) {
                return $q->whereHas('bids', function ($q) use ($user_id, $user_category) {
                    return $q->where('user_id', $user_id)->where('category_id', $user_category);
                });
            })
            ->when($role == 'ROLE_MANAGER', function ($q) use ($user_id, $user_category) {
                return $q->whereHas('bids.user', function ($qb) use ($user_id, $user_category) {
                    return $qb->where('manager_id', $user_id)->where('category_id', $user_category);
                });
            })
            ->when($role == 'ROLE_ADMIN', function ($q) use ($user_id, $user_category) {
                return $q->whereHas('bids', function ($qb) use ($user_id, $user_category) {
                    return $qb->where('category_id', $user_category);
                });
            })
            ->whereHas('status', function ($q) {
                return $q->whereNotIn('type', [1003]);
            })
            ->where('is_delete', false)
            ->count();
    }

    public static function noDistributed()
    {
        return self::where('is_delete', false)
            ->whereHas('status', function ($q) {
                $q->where('type', 1004);
            })->get();
    }

    /**
     * Start Ver 1.0
     */

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direction_id', 'id');
    }

    public function disput()
    {
        return $this->hasOne(Disput::class);
    }

    public function bids()
    {
        return $this->belongsTo(Bid::class, 'bid_id', 'id')
            ->with('direction');
    }

    public function user()
    {
        return $this->belongsTo(Bid::class)
            ->with('user');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function deal_files()
    {
        return $this->hasMany(DealFile::class);
    }

    /**
     * End Ver 1.0
     */
}
