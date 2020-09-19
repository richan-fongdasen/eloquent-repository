<?php

namespace RichanFongdasen\Repository\Tests\Concerns;

use ErrorException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RichanFongdasen\Repository\Criterias\PaginationCriteria;
use RichanFongdasen\Repository\Criterias\WithTrashedCriteria;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostCategoryRepository;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;
use RichanFongdasen\Repository\Tests\TestCase;

class RetrieveDataTests extends TestCase
{
    /**
     * Post Category Repository Object.
     *
     * @var \RichanFongdasen\Repository\Tests\Supports\Repositories\PostCategoryRepository
     */
    protected $postCategory;

    /**
     * Post Repository Object.
     *
     * @var \RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository
     */
    protected $post;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->seeder->seedAll();

        $this->postCategory = app(PostCategoryRepository::class);
        $this->post = app(PostRepository::class);
    }

    /** @test */
    public function it_returns_all_of_database_records_available()
    {
        $collection = $this->postCategory->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(3, $collection->count());
    }

    /** @test */
    public function it_can_find_a_single_model_by_the_given_primary_key()
    {
        $post = $this->post->find(18);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertTrue($post->exists);
        $this->assertEquals(18, $post->getKey());
    }

    /** @test */
    public function it_can_find_multiple_models_by_the_given_primary_keys()
    {
        $keys = [3, 9, 15];
        $collection = $this->post->find($keys);

        $this->assertInstanceOf(Collection::class, $collection);

        $index = 0;
        foreach ($collection as $post) {
            $this->assertInstanceOf(Post::class, $post);
            $this->assertTrue($post->exists);
            $this->assertEquals($keys[$index], $post->getKey());

            $index++;
        }
    }

    /** @test */
    public function it_can_find_multiple_models_based_on_the_given_column_and_value()
    {
        $collection = $this->post->select()->findAllBy('post_category_id', 2);
        $count = \DB::table('posts')->where('post_category_id', 2)->count();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($count, $collection->count());
    }

    /** @test */
    public function it_can_find_multiple_models_by_the_given_query_scopes()
    {
        $collection = $this->post->limit(-10)
            ->findAllWhere([
                'published' => true,
                ['post_category_id', '>', 1]
            ]);

        $count = \DB::table('posts')->where('published', true)
            ->where('post_category_id', '>', 1)->count();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($count, $collection->count());
    }

    /** @test */
    public function it_can_find_a_single_model_by_the_given_column_and_value()
    {
        $actual = $this->post->select(['id', 'post_category_id', 'user_id', 'title'])
            ->orderBy('created_at', 'desc')
            ->findBy('published', true);

        $expected = \DB::table('posts')->where('published', true)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->assertInstanceOf(Post::class, $actual);
        $this->assertEquals($expected->id, $actual->id);
        $this->assertEquals($expected->post_category_id, $actual->post_category_id);
        $this->assertEquals($expected->user_id, $actual->user_id);
        $this->assertEquals($expected->title, $actual->title);

        $this->assertNull($actual->content);
        $this->assertNull($actual->published);
        $this->assertNull($actual->created_at);
        $this->assertNull($actual->modified_at);
    }

    /** @test */
    public function it_can_find_a_single_model_by_the_given_query_scopes()
    {
        $actual = $this->post->select(['id', 'post_category_id', 'user_id', 'title'])
            ->orderBy('created_at', 'desc')
            ->findWhere([
                'published' => true,
                ['post_category_id', '>', 1]
            ]);

        $expected = \DB::table('posts')
            ->where('published', true)
            ->where('post_category_id', '>', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->assertInstanceOf(Post::class, $actual);
        $this->assertEquals($expected->id, $actual->id);
        $this->assertEquals($expected->post_category_id, $actual->post_category_id);
        $this->assertEquals($expected->user_id, $actual->user_id);
        $this->assertEquals($expected->title, $actual->title);

        $this->assertNull($actual->content);
        $this->assertNull($actual->published);
        $this->assertNull($actual->created_at);
        $this->assertNull($actual->modified_at);
    }

    /** @test */
    public function it_can_normalize_null_sort_direction()
    {
        $expected = \DB::table('posts')->orderBy('post_category_id', 'asc')->toSql();

        $query = $this->post->orderBy('post_category_id')->newQuery();

        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_returns_an_array_list_as_expected()
    {
        $collection = $this->postCategory->pluck('title', 'id');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertTrue($collection->has(1));
        $this->assertTrue($collection->has(2));
        $this->assertTrue($collection->has(3));
        $this->assertEquals(3, $collection->count());
    }

    /** @test */
    public function it_raises_exception_when_paginating_query_without_using_pagination_criteria()
    {
        $this->expectException(ErrorException::class);
        $paginator = $this->post->paginate(['published' => true]);
    }

    /** @test */
    public function it_can_build_paginated_records_based_on_the_given_query_scopes()
    {
        $paginator = $this->post->pushCriteria([PaginationCriteria::class])
            ->paginate(['published' => true]);
        $count = \DB::table('posts')->where('published', true)->count();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals($count, $paginator->total());
        $this->assertEquals(2, $paginator->perPage());
    }

    /** @test */
    public function it_can_retrieve_row_and_some_of_its_relationship_from_database()
    {
        $actual = $this->post->with(['postCategory'])->find(1)->toArray();

        $this->assertArrayHasKey('post_category', $actual);
        $this->assertEquals(data_get($actual, 'post_category_id'), data_get($actual, 'post_category.id'));
    }
}
