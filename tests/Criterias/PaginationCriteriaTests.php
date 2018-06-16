<?php

namespace RichanFongdasen\Repository\Tests\Criterias;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use RichanFongdasen\Repository\Criterias\PaginationCriteria;
use RichanFongdasen\Repository\Tests\Supports\Models\PostCategory;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;
use RichanFongdasen\Repository\Tests\TestCase;

class PaginationCriteriaTests extends TestCase
{
    /**
     * Base paginated url.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Current page.
     *
     * @var int
     */
    protected $page;

    /**
     * A mocked Request object
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->request = \Mockery::mock(Request::class);
    }

    /**
     * Prepare the mocked request to accept some
     * method calls.
     *
     * @return void
     */
    public function prepareMockedRequest()
    {
        $this->baseUrl = 'http://localhost:8000/news/index';
        $this->page = rand(1, 10);

        $this->request->shouldReceive('url')
            ->times(1)
            ->andReturn($this->baseUrl);

        $this->request->shouldReceive('input')
            ->with('page')
            ->times(1)
            ->andReturn($this->page);
    }

    /** @test */
    public function it_can_be_initiated_using_the_given_request_object()
    {
        $this->prepareMockedRequest();

        $criteria = new PaginationCriteria($this->request);

        $this->assertEquals($this->baseUrl, $this->getPropertyValue($criteria, 'currentPath'));
        $this->assertEquals($this->page, $this->getPropertyValue($criteria, 'currentPage'));
    }

    /** @test */
    public function it_can_normalize_invalid_request_page_value()
    {
        $baseUrl = 'http://localhost:8000/news/index';
        $page = 'invalidIntegerValue';

        $this->request->shouldReceive('url')
            ->times(1)
            ->andReturn($baseUrl);

        $this->request->shouldReceive('input')
            ->with('page')
            ->times(1)
            ->andReturn($page);

        $criteria = new PaginationCriteria($this->request);

        $this->assertEquals(1, $this->getPropertyValue($criteria, 'currentPage'));
    }

    /** @test */
    public function it_can_set_per_page_value()
    {
        $this->prepareMockedRequest();

        $criteria = new PaginationCriteria($this->request);

        $criteria->setPerPage(30);
        $this->assertEquals(30, $this->getPropertyValue($criteria, 'perPage'));
    }

    /** @test */
    public function it_can_normalize_invalid_per_page_value()
    {
        $this->prepareMockedRequest();

        $criteria = new PaginationCriteria($this->request);
        $criteria->setModel(new PostCategory);

        $criteria->setPerPage(-10);
        $this->assertEquals(15, $this->getPropertyValue($criteria, 'perPage'));
    }

    /** @test */
    public function it_can_build_paginator_as_expected()
    {
        $this->seeder->seedAll();

        $total = \DB::table('posts')->count();
        $repository = app(PostRepository::class);

        $this->prepareMockedRequest();
        $criteria = new PaginationCriteria($this->request);
        $criteria->setModel($repository->newModel());
        $paginator = $criteria->buildPaginator($repository->newQuery(), 3);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals($total, $paginator->total());
        $this->assertEquals(3, $paginator->perPage());
    }
}
