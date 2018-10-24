<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'users_profiles';

    protected $fillable = [
        'user_id', 'thumb_path', 'file_path', 'width', 'height'
    ];

    protected $hidden = [
        'id',
        'user_id',
    ];

    public $timestamps = false;
}
