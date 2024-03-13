<?php

namespace RichanFongdasen\Repository\Tests;

use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\Repository\Criterias\WithTrashedCriteria;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostCategoryRepository;
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
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seeder->seedAll();

        $this->repository = app(PostRepository::class);
    }

    #[Test]
    public function it_returns_new_model_instance_as_expected()
    {
        $model = $this->repository->newModel();

        $this->assertInstanceOf(Post::class, $model);
        $this->assertFalse($model->exists);
        $this->assertNull($model->getKey());
    }

    #[Test]
    public function it_returns_new_prepared_eloquent_query_object_as_expected()
    {
        $this->repository->select(['id', 'post_category_id', 'user_id', 'title'])
            ->limit(5)
            ->orderBy('created_at', 'desc');

        $query = $this->repository->newQuery();

        $expected = \DB::table('posts')
            ->select(['id', 'post_category_id', 'user_id', 'title'])
            ->limit(5)
            ->orderBy('created_at', 'desc')->toSql();

        $this->assertEquals($expected, $query->toSql());
    }

    #[Test]
    public function it_returns_plain_eloquent_query_object_as_expected()
    {
        $repository = app(PostCategoryRepository::class);
        $repository->select(['id', 'title', 'updated_at'])
            ->limit(5)
            ->orderBy('created_at', 'desc');

        $query = $repository->plainQuery();

        $expected = \DB::table('post_categories')->where('post_categories.deleted_at', null)
            ->toSql();

        $this->assertEquals($expected, $query->toSql());
    }
}
