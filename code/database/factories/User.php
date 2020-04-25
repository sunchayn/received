<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'username' => $faker->uuid,
        'email' => $faker->email,
        'password' => bcrypt($faker->password),
        'phone_number' => $faker->unique()->randomNumber(6),
        'country_code' => 216,
        'verification_id' => null,
        'ongoing_two_fa' => false,
        'verified_at' => $faker->dateTimeBetween('-1 year', 'now'),
    ];
});

// Not verified state
// --
$factory->state(User::class, 'not_verified', function ($faker) {
    return [
        'verification_id' => '123456',
        'verified_at' => null,
    ];
});

// Needs 2fa state
// --
$factory->state(User::class, 'needs_2fa', function ($faker) {
    return [
        'ongoing_two_fa' => true,
    ];
});
