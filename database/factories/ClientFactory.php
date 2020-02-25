<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
        'password' => Hash::make(111111),
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'address' => $faker->address,
        'timezone' => $faker->timezone,
        'code' => 123123,
        'created_at' => $faker->dateTimeBetween('-1 year', 'now')
    ];
});
