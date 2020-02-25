<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Card;
use Faker\Generator as Faker;

$factory->define(Card::class, function (Faker $faker) {
    return [
        'background_color' => '#2a3947',
        'foreground_color' => '#000000',
        'label_color' => '#54b095',
        'points_head' => 'score',
        'points_value' => '${points}',
        'customer_head' => 'CUSTOMER',
        'customer_value' => '${firstName} ${lastName}',
        'flip_head' => 'FLIP PASS',
        'flip_value' => 'Below',
        'loyalty_profile' => '0',
        'loyalty_offers' => '0',
        'loyalty_contact' => '0',
        'loyalty_terms' => '0',
        'loyalty_terms_value' => null,
        'loyalty_message' => '0',
        'icon' => null,
        'background_image' => null,
        'customer_id' => '${customerId}'
    ];
});
