<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\File;
use App\Models\Folder;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {
    return [
        'filename' => $faker->uuid,
        'type' => $faker->sentence(1),
        'extension' => $faker->fileExtension,
        'size' => $faker->randomNumber(),
    ];
});

/**
 * With folder state
 */
$factory->state(File::class, 'with_folder',function (Faker $faker) {
    return [
        'filename' => $faker->uuid,
        'type' => $faker->sentence(1),
        'extension' => $faker->fileExtension,
        'size' => $faker->randomNumber(),
        'folder_id' => factory(Folder::class)->create([
            'user_id' => factory(\App\Models\User::class)->create()->id,
        ])->id,
    ];
});

