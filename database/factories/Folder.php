<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Folder;
use Faker\Generator as Faker;

$factory->define(Folder::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'slug' => $faker->uuid,
        'password' => null,
        'shared_at' => null,
    ];
});

$factory->state(Folder::class, 'shared', function (Faker $faker) {
    return [
        'password' => bcrypt($faker->password),
        'shared_at' => $faker->dateTimeBetween('-1 year', 'now'),
    ];
});
