<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\PassTemplate;
use Faker\Generator as Faker;

$factory->define(PassTemplate::class, function (Faker $faker) {
    return [
        'background_color' => $faker->rgbColor,
        'foreground_color' => $faker->rgbColor,
        'label_color' => $faker->rgbColor,
        'points_head' => $faker->title,
        'points_value' => $faker->text(100),
        'offer_head' => $faker->title,
        'offer_value' => $faker->text(100),
        'customer_head' => $faker->title,
        'customer_value' => $faker->text(100),
        'flip_head' => $faker->title,
        'flip_value' => $faker->text(100),
        'back_side_head' => $faker->title,
        'back_side_value' => $faker->text(100),
        'icon' => $faker->url,
        'background_image' => $faker->url,
        'stripe_image' => $faker->url,
        'customer_id' => $faker->uuid,
    ];
});
