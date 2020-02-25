<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\PassLocation;
use Faker\Generator as Faker;

$factory->define(PassLocation::class, function (Faker $faker) {
    return [
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'params' => null
    ];
});
