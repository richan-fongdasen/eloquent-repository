<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_category_id' => $this->faker->randomElement(range(1, 3)),
            'user_id' => $this->faker->randomElement(range(1, 3)),
            'title' => $this->faker->sentence(8),
            'content' => $this->faker->paragraph(),
            'published' => $this->faker->boolean(70),
        ];
    }
}
