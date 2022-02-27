<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MorphImage\Models\Entities\Image;
use WalkerChiu\MorphImage\Models\Entities\ImageLang;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'morph_type' => 'WalkerChiu\MallShelf\Models\Entities\Stock',
        'morph_id'   => 1,
        'serial'     => $faker->isbn10,
        'identifier' => $faker->slug
    ];
});

$factory->define(ImageLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
