<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'users_profiles';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id', 'thumb_path', 'file_path', 'width', 'height'
    ];

    protected $hidden = [
        'id',
        'user_id',
    ];

    public $timestamps = false;
}
