<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\LoyaltyProgram;
use Faker\Generator as Faker;

$factory->define(LoyaltyProgram::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->text,
        'country' => $faker->country,
        'language' => $faker->languageCode,
        'link' => $faker->url,
        'currency' => $faker->currencyCode,
        'currency_value' => 12
    ];
});
