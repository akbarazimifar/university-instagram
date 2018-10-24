<?php

use Faker\Generator as Faker;

$factory->define(App\UserProfile::class, function (Faker $faker) {
    return [
        'thumb_path' => 'default_profile.jpg',
        'file_path'  => 'default_profile.jpg',
        'width'      => 600,
        'height'     => 600
    ];
});
