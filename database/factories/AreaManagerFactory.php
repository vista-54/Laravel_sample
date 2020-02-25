<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\AreaManager;
use Faker\Generator as Faker;

$factory->define(AreaManager::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => Hash::make(111111)
    ];
});
