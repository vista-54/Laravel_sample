<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Stamps;
use Faker\Generator as Faker;

$factory->define(Stamps::class, function (Faker $faker) {
    return [
        'stamps_number' => 10,
        'background_color' => '#000000',
        'background_image' => null,
        'stamp_color' => '#F00000',
        'unstamp_color' => '#ffffff',
        'stamp_image' => null,
        'unstamp_image' => null,
    ];
});
