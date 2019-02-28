<?php

namespace RichanFongdasen\Repository\Tests\Concerns;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use RichanFongdasen\Repository\Criterias\PaginationCriteria;
use RichanFongdasen\Repository\Criterias\WithTrashedCriteria;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostCategoryRepository;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;
use RichanFongdasen\Repository\Tests\TestCase;

class HasCriteriasTests extends TestCase
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
    public function criterias_can_be_flushed_on_demand()
    {
        $this->repository->pushCriteria([WithTrashedCriteria::class]);
        $this->repository->flushCriteria();

        $this->assertNull($this->repository->getCriteria(WithTrashedCriteria::class));
    }

    /** @test */
    public function it_will_returns_collection_when_getting_criterias_without_specifying_any_class_name()
    {
        $this->repository->pushCriteria([
            WithTrashedCriteria::class,
            PaginationCriteria::class
        ]);
        $criterias = $this->repository->getCriteria();

        $this->assertInstanceOf(Collection::class, $criterias);
        $this->assertEquals(2, $criterias->count());
        $this->assertInstanceOf(WithTrashedCriteria::class, $criterias->first());
        $this->assertInstanceOf(PaginationCriteria::class, $criterias->last());
    }

    /** @test */
    public function it_can_create_criteria_with_the_given_class_name()
    {
        $this->repository->pushCriteria([WithTrashedCriteria::class]);
        $actual = $this->repository->getCriteria(WithTrashedCriteria::class);

        $this->assertInstanceOf(WithTrashedCriteria::class, $actual);
    }

    /** @test */
    public function it_can_push_the_given_criteria_into_the_collection()
    {
        $criteria = new WithTrashedCriteria;
        $this->repository->pushCriteria([$criteria]);
        $actual = $this->repository->getCriteria(WithTrashedCriteria::class);

        $this->assertInstanceOf(WithTrashedCriteria::class, $actual);
    }

    /** @test */
    public function it_will_raise_exception_when_pushing_non_criteria_object()
    {
        $this->prepareException(InvalidArgumentException::class);
        $this->repository->pushCriteria([$this->repository->newModel()]);
    }

    /** @test */
    public function criteria_can_be_removed_on_demand()
    {
        $this->repository->pushCriteria([
            WithTrashedCriteria::class,
            PaginationCriteria::class
        ]);
        $this->repository->removeCriteria(WithTrashedCriteria::class);
        $criterias = $this->repository->getCriteria();

        $this->assertInstanceOf(Collection::class, $criterias);
        $this->assertEquals(1, $criterias->count());
        $this->assertInstanceOf(PaginationCriteria::class, $criterias->first());
    }

    /** @test */
    public function not_on_demand_criteria_will_be_implemented_immediately()
    {
        $repository = app(PostCategoryRepository::class);

        $expected = \DB::table('post_categories')
            ->select(['id', 'title', 'updated_at'])
            ->limit(10)
            ->orderBy('created_at', 'desc')->toSql();

        $repository->pushCriteria([WithTrashedCriteria::class]);
        $repository->select(['id', 'title', 'updated_at'])
            ->limit(10)
            ->orderBy('created_at', 'desc');

        $query = $repository->newQuery();
        
        $this->assertEquals($expected, $query->toSql());
    }
}
