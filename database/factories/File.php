<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\File;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {
    return [
        'filename' => $faker->uuid,
        'type' => $faker->sentence(1),
        'extension' => $faker->fileExtension,
        'size' => $faker->randomNumber(),
    ];
});
