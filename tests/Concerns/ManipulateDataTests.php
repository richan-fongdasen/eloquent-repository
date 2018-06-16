<?php

namespace RichanFongdasen\Repository\Tests\Concerns;

use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Models\PostCategory;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostCategoryRepository;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;
use RichanFongdasen\Repository\Tests\TestCase;

class ManipulateDataTests extends TestCase
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

        $this->seeder->seedUsers();
        $this->seeder->seedPostCategories();

        $this->repository = app(PostRepository::class);
    }

    public function createNewPost()
    {
        return $this->repository->create([
            'post_category_id' => 2,
            'user_id' => 3,
            'title' => 'title test 1',
            'content' => 'content test 1',
            'published' => true
        ]);
    }

    /** @test */
    public function it_can_create_new_model_based_on_the_given_attribute()
    {
        $model = $this->createNewPost();

        $this->assertInstanceOf(Post::class, $model);
        $this->assertEquals(1, $model->getKey());
        $this->assertTrue($model->exists);
        $this->assertTrue($model->wasRecentlyCreated);
    }

    /** @test */
    public function it_can_delete_existing_model_based_on_the_given_primary_key()
    {
        $model = $this->createNewPost();
        $this->repository->delete($model->getKey());

        $this->assertNull($this->repository->find($model->getKey()));
    }

    /** @test */
    public function it_can_perform_first_or_create_operation()
    {
        $this->createNewPost();
        $model = $this->repository->firstOrCreate([
            'post_category_id' => 1,
            'user_id' => 2,
            'title' => 'title test 2',
            'content' => 'content test 2',
        ]);

        $this->assertInstanceOf(Post::class, $model);
        $this->assertEquals(2, $model->getKey());
        $this->assertTrue($model->exists);
        $this->assertTrue($model->wasRecentlyCreated);
    }

    /** @test */
    public function it_can_perform_first_or_new_operation()
    {
        $this->createNewPost();
        $model = $this->repository->firstOrNew([
            'post_category_id' => 1,
            'user_id' => 2,
        ]);

        $this->assertInstanceOf(Post::class, $model);
        $this->assertFalse($model->exists);
    }

    /** @test */
    public function it_can_restore_deleted_model_based_on_the_given_primary_key()
    {
        $repository = app(PostCategoryRepository::class);
        $model = $repository->create(['title' => 'test category']);        

        $repository->delete($model->getKey());
        $repository->restore($model->getKey());

        $restored = $repository->find($model->getKey());

        $this->assertInstanceOf(PostCategory::class, $restored);
        $this->assertEquals($model->getKey(), $restored->getKey());
        $this->assertTrue($restored->exists);
        $this->assertFalse($restored->wasRecentlyCreated);
    }

    /** @test */
    public function it_can_update_created_model()
    {
        $model = $this->createNewPost();
        $this->repository->update($model->getKey(), [
            'title' => 'updated title',
            'content' => 'updated content',
        ]);

        $updated = $this->repository->find($model->getKey());

        $this->assertInstanceOf(Post::class, $updated);
        $this->assertEquals('updated title', $updated->title);
        $this->assertEquals('updated content', $updated->content);
    }

    /** @test */
    public function it_can_perform_update_or_create_operation()
    {
        $this->createNewPost();
        $attributes = [
            'post_category_id' => 1,
            'user_id' => 2,
        ];
        $values = [
            'title' => 'title test 2',
            'content' => 'content test 2',
        ];

        $model = $this->repository->updateOrCreate($attributes, $values);

        $this->assertInstanceOf(Post::class, $model);
        $this->assertEquals(2, $model->getKey());
        $this->assertTrue($model->exists);
        $this->assertTrue($model->wasRecentlyCreated);
    }
}
