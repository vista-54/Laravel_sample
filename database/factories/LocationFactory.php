<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Location;
use Faker\Generator as Faker;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'latitude' => '41.56',
        'longitude' => '53.21',
        'params' => 'some data'
    ];
});
