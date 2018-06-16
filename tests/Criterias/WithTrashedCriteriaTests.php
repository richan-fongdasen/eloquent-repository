<?php

namespace RichanFongdasen\Repository\Tests\Criterias;

use RichanFongdasen\Repository\Criterias\WithTrashedCriteria;
use RichanFongdasen\Repository\Tests\Supports\Models\User;
use RichanFongdasen\Repository\Tests\Supports\Repositories\PostRepository;
use RichanFongdasen\Repository\Tests\TestCase;

class WithTrashedCriteriaTests extends TestCase
{
    /**
     * The criteria object to test.
     *
     * @var \RichanFongdasen\Repository\Criterias\WithTrashedCriteria
     */
    protected $criteria;

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

        $this->criteria = new WithTrashedCriteria;
        $this->repository = app(PostRepository::class);
    }

    /** @test */
    public function it_can_confirm_if_the_current_affected_model_uses_soft_deletes()
    {
        $user = new User;

        $this->criteria->setModel($user);
        $this->assertFalse($this->criteria->modelHasSoftDeletes());

        $this->criteria->setModel($this->repository->newModel());
        $this->assertTrue($this->criteria->modelHasSoftDeletes());
    }

    /** @test */
    public function it_will_manipulate_query_if_the_model_uses_soft_deletes()
    {
        $expected = 'select * from "posts"';
        $this->criteria->setModel($this->repository->newModel());
        $query = $this->criteria->manipulate($this->repository->newQuery());

        $this->assertEquals($expected, $query->toSql());
    }

    /** @test */
    public function it_wont_manipulate_query_if_the_model_doesnt_use_soft_deletes()
    {
        $user = new User;
        $expected = 'select * from "posts" where "posts"."deleted_at" is null';

        $this->criteria->setModel($user);
        $query = $this->criteria->manipulate($this->repository->newQuery());

        $this->assertEquals($expected, $query->toSql());
    }
}
