<?php

namespace RichanFongdasen\Repository\Tests;

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
     */
    public function setUp()
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
            'select * from "posts" where "post_category_id" = ? and "posts"."deleted_at" is null',
            'select * from "posts" where "created_at" > ? and "posts"."deleted_at" is null'
        ];

        foreach ($params as $column => $value) {
            $query = $this->repository->plainQuery();

            ScopeBuilder::apply($query, $column, $value);
            
            $this->assertEquals($expected[$column], $query->toSql());
        }
    }

    /** @test */
    public function it_can_handle_single_scope_application_with_single_value_correctly()
    {
        $expected = 'select * from "posts" where "published" = ? and "posts"."deleted_at" is null';
        $query = $this->repository->plainQuery();

        ScopeBuilder::apply($query, 'published', true);
        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_can_handle_single_scope_application_with_multiple_values_correctly()
    {
        $expected = 'select * from "posts" where "post_category_id" in (?, ?, ?) and "posts"."deleted_at" is null';
        $query = $this->repository->plainQuery();

        ScopeBuilder::apply($query, 'post_category_id', [1, 2, 3]);
        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_can_generate_and_apply_multiple_scopes_correctly()
    {
        $expected = 'select * from "posts" where "post_category_id" in (?, ?, ?) and "published" = ? and "title" like ? and "posts"."deleted_at" is null';
        $conditions = [
            'post_category_id' => [1, 2, 3],
            'published' => true,
            ['title', 'like', '%hello world%']
        ];
        $query = $this->repository->plainQuery();

        ScopeBuilder::generate($query, $conditions);
        $this->assertEquals($expected, $query->toSql());
    }
}
