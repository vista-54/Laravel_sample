<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\ContactsTerm;
use Faker\Generator as Faker;

$factory->define(ContactsTerm::class, function (Faker $faker) {
    return [
        'company_name' => null,
        'address' => null,
        'website' => null,
        'email' => null,
        'phone' => null,
        'conditions' => null,
    ];
});
