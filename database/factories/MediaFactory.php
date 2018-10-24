<?php

use Faker\Generator as Faker;

$factory->define(App\Media::class, function (Faker $faker) {
    return [
        'width'      => $faker->randomDigitNotNull,
        'height'     => $faker->randomDigitNotNull,
        'caption'    => $faker->text(),
        'file_path'  => 'default_post.jpg',
        'thumb_path' => 'default_post.jpg'
    ];
});
