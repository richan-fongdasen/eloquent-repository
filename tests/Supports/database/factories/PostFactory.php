<?php

use Faker\Generator as Faker;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;

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

$factory->define(Post::class, function (Faker $faker) {
    return [
        'post_category_id' => $faker->randomElement(range(1, 3)),
        'user_id' => $faker->randomElement(range(1, 3)),
        'title' => $faker->sentence(8),
        'content' => $faker->paragraph(),
        'published' => $faker->randomElement([true, false]),
    ];
});
