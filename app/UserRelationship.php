<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    protected $table = 'users_relationships';

    public $timestamps = false;
}
