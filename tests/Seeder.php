<?php

namespace RichanFongdasen\Repository\Tests;

use RichanFongdasen\Repository\Tests\Supports\Models\Comment;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Models\PostCategory;
use RichanFongdasen\Repository\Tests\Supports\Models\User;

class Seeder
{
    /**
     * Seed all of data required for testing.
     *
     * @return void
     */
    public function seedAll()
    {
        $this->seedUsers();
        $this->seedPostCategories();
        $this->seedPosts();
        $this->seedComments();
    }

    /**
     * Seed only comments data for testing.
     *
     * @return void
     */
    public function seedComments()
    {
        Comment::factory(50)->create();
    }

    /**
     * Seed only post categories data for testing.
     *
     * @return void
     */
    public function seedPostCategories()
    {
        PostCategory::factory(3)->create();
    }

    /**
     * Seed only posts data for testing.
     *
     * @return void
     */
    public function seedPosts()
    {
        Post::factory(27)->create();
    }

    /**
     * Seed only users data for testing.
     *
     * @return void
     */
    public function seedUsers()
    {
        User::factory(3)->create();
    }
}
