<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    protected $table = 'users_relationships';

    protected $fillable = [
        'follower_id', 'following_id', 'is_accepted'
    ];

    public $timestamps = true;
}
