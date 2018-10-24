<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'profile_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot',
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
