<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Shop;
use Faker\Generator as Faker;

$factory->define(Shop::class, function (Faker $faker) {
    return [
        'number' => $faker->numberBetween(0, 100000),
        'name' => $faker->name
    ];
});
