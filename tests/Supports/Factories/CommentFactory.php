<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use RichanFongdasen\Repository\Tests\Supports\Models\Comment;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_id' => $this->faker->randomElement(range(1, 5)),
            'user_id' => $this->faker->randomElement(range(1, 3)),
            'content' => $this->faker->paragraph(),
        ];
    }
}

