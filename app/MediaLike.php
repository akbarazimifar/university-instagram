<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaLike extends Model
{
    protected $table = 'medias_likes';

    protected $fillable = [
        'user_id', 'media_id'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function media()
    {
        return $this->hasOne(Media::class,'id','media_id');
    }
}
