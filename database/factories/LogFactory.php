<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Log;
use Faker\Generator as Faker;

$factory->define(Log::class, function (Faker $faker) {
    return [
        'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 year' , 'now'))->toDateTimeString()
    ];
});
