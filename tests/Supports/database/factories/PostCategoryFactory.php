<?php

use Faker\Generator as Faker;
use RichanFongdasen\Repository\Tests\Supports\Models\PostCategory;

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

$factory->define(PostCategory::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(3)
    ];
});
