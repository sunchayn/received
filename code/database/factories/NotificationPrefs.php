<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationPrefs;
use Faker\Generator as Faker;

$factory->define(NotificationPrefs::class, function (Faker $faker) {
    return [
        'notify_by_sms' => $faker->boolean,
        'notify_by_mail' => $faker->boolean,
    ];
});
