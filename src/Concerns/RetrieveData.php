<?php

namespace RichanFongdasen\Repository\Concerns;

use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use RichanFongdasen\Repository\Criterias\PaginationCriteria;
use RichanFongdasen\Repository\ScopeBuilder;

trait RetrieveData
{
    /**
     * The columns that should be returned.
     *
     * @var array
     */
    protected $columns;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    protected $limit;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    protected $order;

    /**
     * Get all of the models from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->newQuery()->get();
    }

    /**
     * Boot RetrieveData.
     *
     * @return void
     */
    protected function bootRetrieveData()
    {
        $this->columns = ['*'];
        $this->limit = 0;
        $this->order = [];
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
    protected function buildMultipleScope(Builder $query, array $conditions)
    {
        return method_exists($query->getQuery(), 'addArrayOfWheres') ?
            $query->where($conditions) :
            ScopeBuilder::generate($query, $conditions);
    }

    /**
     * Find one or more models by its primary keys.
     *
     * @param mixed $key
     *
     * @return object|null
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
    public function findAllBy($column, $operator, $value = null)
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
    public function findAllWhere(array $conditions)
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
     * @return object|null
     */
    public function findBy($column, $operator, $value = null)
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
     * @return object|null
     */
    public function findWhere(array $conditions)
    {
        return $this->buildMultipleScope($this->newQuery(), $conditions)->first();
    }

    /**
     * Get the paginator criteria.
     *
     * @return \RichanFongdasen\Repository\Criterias\PaginationCriteria
     */
    protected function getPaginatorCriteria()
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
    public function limit($limit)
    {
        $this->limit = (int) $limit;

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
    public function orderBy($column, $direction = 'asc')
    {
        $this->order[$column] = $direction;

        return $this;
    }

    /**
     * Apply the given where clauses into a new query and
     * paginate the query into a length aware paginator.
     *
     * @param array $conditions
     * @param int   $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($conditions = [], $perPage = 0)
    {
        $this->limit(0);

        $query = $this->buildMultipleScope($this->newQuery(), $conditions);

        return $this->getPaginatorCriteria()
            ->buildPaginator($query, (int) $perPage);
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck($column, $key = null)
    {
        $query = $this->newQuery();

        return (method_exists($query, 'lists')) ?
            $query->lists($column, $key) :
            $query->pluck($column, $key);
    }

    /**
     * Prepare a new query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function prepareQuery(Builder $query)
    {
        $query->select($this->columns);

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        foreach ($this->order as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        $this->rebootRetrieveData();

        return $query;
    }

    /**
     * Reboot RetrieveData.
     *
     * @return void
     */
    protected function rebootRetrieveData()
    {
        $this->bootRetrieveData();
    }

    public function select(array $columns = [])
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
    abstract public function getCriteria($class = null);

    /**
     * Get new prepared eloquent query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function newQuery();
}
