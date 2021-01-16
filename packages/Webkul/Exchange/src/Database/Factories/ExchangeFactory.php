<?php

use Faker\Generator as Faker;
use Webkul\Exchange\Models\Exchange;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Exchange::class, function (Faker $faker, array $attributes) {

    return [
        'status'    => 1,
        'position'  => $faker->randomDigit,
        'parent_id' => 1,
    ];
});

$factory->state(Exchange::class, 'inactive', [
    'status' => 0,
]);
