<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Invite;
use Faker\Generator as Faker;

$factory->define(Invite::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'confirmed' => rand(0, 1),
        'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 year' , 'now'))->toDateTimeString()
    ];
});
