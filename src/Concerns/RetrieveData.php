<?php

namespace RichanFongdasen\Repository\Concerns;

use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;
use RichanFongdasen\Repository\Criterias\PaginationCriteria;
use RichanFongdasen\Repository\ScopeBuilder;

trait RetrieveData
{
    /**
     * The columns that should be returned.
     *
     * @var array
     */
    protected array $columns;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    protected int $limit;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    protected array $order;

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected array $withRelations;

    /**
     * Get all the models from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        return $this->newQuery()->get();
    }

    /**
     * Boot RetrieveData.
     *
     * @return void
     */
    protected function bootRetrieveData(): void
    {
        $this->columns = ['*'];
        $this->limit = 0;
        $this->order = [];
        $this->withRelations = [];
    }

    /**
     * Build multiple query scopes, based on the
     * given array $conditions. We will use the
     * ScopeBuilder class in Laravel 5.1.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $conditions
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildMultipleScope(Builder $query, array $conditions): Builder
    {
        return method_exists($query->getQuery(), 'addArrayOfWheres') ?
            $query->where($conditions) :
            ScopeBuilder::generate($query, $conditions);
    }

    /**
     * Find one or more models by its primary keys.
     *
     * @param int|string $key
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null|mixed
     */
    public function find($key)
    {
        return $this->newQuery()->find($key);
    }

    /**
     * Find all matched models by using a basic where clause
     * in the query.
     *
     * @param string      $column
     * @param string      $operator
     * @param string|null $value
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllBy(string $column, string $operator, string $value = null): Collection
    {
        return $this->newQuery()
            ->where($column, $operator, $value)
            ->get();
    }

    /**
     * Find all matched models by using multiple where clause
     * in the query.
     *
     * @param array $conditions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllWhere(array $conditions): Collection
    {
        return $this->buildMultipleScope($this->newQuery(), $conditions)
            ->get();
    }

    /**
     * Find one matched model by using a basic where clause
     * in the query.
     *
     * @param string      $column
     * @param string      $operator
     * @param string|null $value
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findBy(string $column, string $operator, ?string $value = null): ?Model
    {
        return $this->newQuery()
            ->where($column, $operator, $value)
            ->first();
    }

    /**
     * Find one matched model by using multiple where clause
     * in the query.
     *
     * @param array $conditions
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findWhere(array $conditions): ?Model
    {
        return $this->buildMultipleScope($this->newQuery(), $conditions)->first();
    }

    /**
     * Get the paginator criteria.
     *
     * @throws ErrorException
     *
     * @return \RichanFongdasen\Repository\Criterias\PaginationCriteria
     */
    protected function getPaginatorCriteria(): PaginationCriteria
    {
        $paginator = $this->getCriteria(PaginationCriteria::class);
        if (!$paginator) {
            throw new ErrorException('PaginationCriteria is required for this operation');
        }

        return $paginator;
    }

    /**
     * Set the maximum number of records to return.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add one or multiple "order by" clauses to the query.
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->order[$column] = $direction;

        return $this;
    }

    /**
     * Add a set of model relationship to retrieve from database.
     *
     * @param array $relations
     *
     * @return $this
     */
    public function with(array $relations): self
    {
        $this->withRelations = array_merge($this->withRelations, $relations);

        return $this;
    }

    /**
     * Apply the given where clauses into a new query and
     * paginate the query into a length aware paginator.
     *
     * @param array $conditions
     * @param int   $perPage
     *
     * @throws ErrorException
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(array $conditions = [], int $perPage = 0): LengthAwarePaginator
    {
        $this->limit(0);

        $query = $this->buildMultipleScope($this->newQuery(), $conditions);

        return $this->getPaginatorCriteria()
            ->buildPaginator($query, $perPage);
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(string $column, ?string $key = null): BaseCollection
    {
        $query = $this->newQuery();

        return $query->pluck($column, $key);
    }

    /**
     * Prepare a new query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function prepareQuery(Builder $query): Builder
    {
        if (count($this->withRelations) > 0) {
            $query->with($this->withRelations);
        }

        $query->select($this->columns);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        foreach ($this->order as $column => $direction) {
            $query->orderBy((string) $column, (string) $direction);
        }

        $this->rebootRetrieveData();

        return $query;
    }

    /**
     * Reboot RetrieveData.
     *
     * @return void
     */
    protected function rebootRetrieveData(): void
    {
        $this->bootRetrieveData();
    }

    /**
     * Define a set of columns to select from database.
     *
     * @param array $columns
     *
     * @return $this
     */
    public function select(array $columns = []): self
    {
        if (empty($columns)) {
            $columns = ['*'];
        }
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get the registered criteria(s).
     *
     * @param string|null $class
     *
     * @return mixed
     */
    abstract public function getCriteria(string $class = null);

    /**
     * Get new prepared eloquent query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function newQuery(): Builder;
}
