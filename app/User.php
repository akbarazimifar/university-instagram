<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
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

    protected $appends = ['is_followed'];

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
        return $this->hasMany(Media::class)->withCount(['likes']);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class,'user_id','id');
    }

    public function getIsFollowedAttribute()
    {
        return (bool)UserRelationship::where('follower_id', '=', Auth::user()->id)
            ->where('following_id', '=', $this->getAttribute('id'))
            ->where('is_accepted', '=', true)
            ->limit(1)
            ->count();
    }
    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->first();
    }
    public static function checkUserViewPermission(User $user)
    {
        if ($user->profile_status === "PRIVATE" && Auth::user()->id !== $user->id) {
            try {
                UserRelationship::where('follower_id', '=', Auth::user()->id)
                    ->where('following_id', '=', $user->id)
                    ->where('is_accepted', '=', true)
                    ->firstOrFail();
                // user is followed
            } catch (\Exception $e) {
                // user is not followed
                return response([
                    'ok'          => 'false',
                    'description' => 'User is not Followed.'
                ], 403);
            }
        }
    }

    public function OauthAcessToken()
    {
        return $this->hasMany(OauthAccessToken::class);
    }
}
