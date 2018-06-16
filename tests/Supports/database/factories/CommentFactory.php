<?php

use Faker\Generator as Faker;
use RichanFongdasen\Repository\Tests\Supports\Models\Comment;

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

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'post_id' => $faker->randomElement(range(1, 5)),
        'user_id' => $faker->randomElement(range(1, 3)),
        'content' => $faker->paragraph(),
    ];
});
