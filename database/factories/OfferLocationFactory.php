<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\OfferLocation;
use Faker\Generator as Faker;

$factory->define(OfferLocation::class, function (Faker $faker) {
    return [
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'params' => null
    ];
});
