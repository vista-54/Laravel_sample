<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Score;
use Faker\Generator as Faker;

$factory->define(Score::class, function (Faker $faker) {
    return [
        'set_email' => 0,
        'set_phone' => 0,
        'set_card' => 0,
        'scan_card' => 0,
    ];
});
