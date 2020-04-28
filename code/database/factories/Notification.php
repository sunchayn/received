<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Notification;
use Faker\Generator as Faker;

$factory->define(Notification::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'content' => $faker->text,
        'is_seen' => false,
        'is_notified' => false,
    ];
});
