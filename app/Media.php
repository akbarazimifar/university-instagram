<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Media extends Model
{
    protected $table = 'medias';

    protected $fillable = [
        'user_id', 'width', 'height', 'file_path', 'thumb_path', 'caption'
    ];

    protected $hidden = [
        'user_id',
        'updated_at'
    ];

    protected $appends = [
        'is_liked'
    ];

    public function likes()
    {
        return $this->belongsToMany(User::class, 'medias_likes', 'media_id', 'user_id')
            ->with('profile')
            ->limit(config('instagram.limit.media.like'));
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function getIsLikedAttribute()
    {
        return (bool) MediaLike::where('user_id', '=', Auth::id())
            ->where('media_id', '=', $this->getAttribute('id'))
            ->limit(1)
            ->count();
    }
}
