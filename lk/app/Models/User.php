<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'category_id',
        'balance',
        'email_notification',
        'manager_id',
        'contact_id',
        'bonus',
        'with_bonus',
        'role',
        'is_registration',
        'was_online',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'manager_id',
        'is_registration',
        'is_delete',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class, 'user_id', 'id');
    }

    public function notification()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo($this, 'manager_id', 'id');
    }
}
