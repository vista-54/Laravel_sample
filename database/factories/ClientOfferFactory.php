<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\ClientOffer;
use Faker\Generator as Faker;

$factory->define(ClientOffer::class, function (Faker $faker) {
    return [
        'used' => 0,
        'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 year', 'now'))->toDateTimeString()
    ];
});
