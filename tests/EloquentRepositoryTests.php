<?php

namespace RichanFongdasen\Repository\Tests;

use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;

class EloquentRepositoryTests extends TestCase
{
    /**
     * Repository object to test.
     *
     * @var \RichanFongdasen\Repository\EloquentRepository
     */
    protected $repository;

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->seeder->seedAll();

        $this->repository = app(PostRepository::class);
    }

    /** @test */
    public function it_returns_new_model_instance_as_expected()
    {
        $model = $this->repository->newModel();

        $this->assertInstanceOf(Post::class, $model);
        $this->assertFalse($model->exists);
        $this->assertNull($model->getKey());
    }

    /** @test */
    public function it_returns_new_prepared_eloquent_query_object_as_expected()
    {
        $this->repository->select(['id', 'post_category_id', 'user_id', 'title'])
            ->limit(5)
            ->orderBy('created_at', 'desc');

        $query = $this->repository->newQuery();
        
        $expected = 'select "id", "post_category_id", "user_id", "title" from "posts" where "posts"."deleted_at" is null order by "created_at" desc limit 5';

        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_returns_plain_eloquent_query_object_as_expected()
    {
        $this->repository->select(['id', 'post_category_id', 'user_id', 'title'])
            ->limit(5)
            ->orderBy('created_at', 'desc');

        $query = $this->repository->plainQuery();
        
        $expected = 'select * from "posts" where "posts"."deleted_at" is null';

        $this->assertEquals($expected, $query->toSql());
    }
}
