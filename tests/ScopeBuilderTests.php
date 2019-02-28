<?php

namespace RichanFongdasen\Repository\Tests;

use RichanFongdasen\Repository\Criterias\WithTrashedCriteria;
use RichanFongdasen\Repository\ScopeBuilder;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;

class ScopeBuilderTests extends TestCase
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
    public function setUp() :void
    {
        parent::setUp();

        $this->repository = app(PostRepository::class);
    }

    /** @test */
    public function pass_through_the_query_scope_on_numeric_columns()
    {
        $datetime = date('Y-m-d H:i:s', time()-1209600); // two weeks ago
        $params = [
            ['post_category_id', 1],
            ['created_at', '>', $datetime]
        ];

        $expected = [
            \DB::table('posts')->where('post_category_id', 1)->toSql(),
            \DB::table('posts')->where('created_at', '>', $datetime)->toSql()
        ];

        foreach ($params as $column => $value) {
            $query = $this->repository->newQuery();

            ScopeBuilder::apply($query, $column, $value);
            
            $this->assertEquals($expected[$column], $query->toSql());
        }
    }

    /** @test */
    public function it_can_handle_single_scope_application_with_single_value_correctly()
    {
        $expected = \DB::table('posts')->where('published', true)->toSql();
        $query = $this->repository->newQuery();

        ScopeBuilder::apply($query, 'published', true);
        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_can_handle_single_scope_application_with_multiple_values_correctly()
    {
        $expected = \DB::table('posts')
            ->whereIn('post_category_id', [1, 2, 3])->toSql();
        $query = $this->repository->newQuery();

        ScopeBuilder::apply($query, 'post_category_id', [1, 2, 3]);
        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_can_generate_and_apply_multiple_scopes_correctly()
    {
        $expected = \DB::table('posts')
            ->whereIn('post_category_id', [1, 2, 3])
            ->where('published', true)
            ->where('title', 'like', '%hello world%')->toSql();
        $conditions = [
            'post_category_id' => [1, 2, 3],
            'published' => true,
            ['title', 'like', '%hello world%']
        ];
        $query = $this->repository->newQuery();

        ScopeBuilder::generate($query, $conditions);
        $this->assertEquals($expected, $query->toSql());
    }
}
