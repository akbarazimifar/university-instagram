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
}
