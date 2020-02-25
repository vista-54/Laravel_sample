<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Offer;
use Faker\Generator as Faker;

$factory->define(Offer::class, function (Faker $faker) {
    $arr = [10, 20, 30, 40, 50];
    $point_cost = array_rand($arr);
    $created_at = $faker->dateTimeBetween('-1 year', 'now');
    return [
        'name' => $faker->title,
        'description' => $faker->text,
        'start_date' => \Carbon\Carbon::parse($created_at)->subWeek()->toDateTimeString(),
        'end_date' => \Carbon\Carbon::parse($created_at)->subWeeks(5)->toDateTimeString(),
        'points_cost' => $arr[$point_cost],
        'customer_limit' => 10,
        'availability_count' => 200,
        'notify' => $faker->title,
        'created_at' => \Carbon\Carbon::now()->subYear()
    ];
});
