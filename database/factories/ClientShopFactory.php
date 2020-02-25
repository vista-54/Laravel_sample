<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\ClientShop;
use Faker\Generator as Faker;

$factory->define(ClientShop::class, function (Faker $faker) {
    return [
        'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 month', 'now'))->toDateTimeString()
    ];
});
