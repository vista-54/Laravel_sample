<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Pass;
use Faker\Generator as Faker;

$factory->define(Pass::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->text,
        'created_at' => \Carbon\Carbon::now()->subYear()
    ];
});
