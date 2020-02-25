<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'department' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => Hash::make('111111'), // password
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'timezone' => '2',
        'role' => User::ROLE_MERCHANT
    ];
});
