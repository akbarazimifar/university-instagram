<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function likes()
    {
        return $this->belongsToMany(User::class, 'medias_likes', 'media_id', 'user_id')
            ->with('profile')
            ->limit(config('instagram.limit.media.like'));
    }
}
