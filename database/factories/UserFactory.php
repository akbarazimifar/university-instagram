<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        // 'email_verified_at' => now(),
        'password' => Hash::make('123456'),
        'remember_token' => str_random(10),
    ];
});
