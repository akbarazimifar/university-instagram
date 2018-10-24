<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'password', 'profile_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email', 'password', 'remember_token', 'pivot', 'created_at', 'updated_at'
    ];

    public function followings()
    {
        return $this->belongsToMany(User::class, 'users_relationships', 'follower_id', 'following_id')
            ->with('profile');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'users_relationships', 'following_id', 'follower_id')
            ->with('profile');
    }

    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
