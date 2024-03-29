<?php

namespace RichanFongdasen\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\Repository\Concerns\HasCriterias;
use RichanFongdasen\Repository\Concerns\ManipulateData;
use RichanFongdasen\Repository\Concerns\RetrieveData;

abstract class EloquentRepository
{
    use HasCriterias;
    use ManipulateData;
    use RetrieveData;

    /**
     * Eloquent model object.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Repository class constructor.
     *
     * @param Model $model [description]
     */
    public function __construct($model)
    {
        $this->model = $model;

        $this->bootTraits();
    }

    /**
     * Boot all the traits that being used by this
     * repository.
     *
     * @return void
     */
    protected function bootTraits(): void
    {
        $class = get_class($this);

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot'.class_basename($trait);
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
    }

    /**
     * Get new eloquent model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel()
    {
        return $this->model->newInstance();
    }

    /**
     * Get new prepared eloquent query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery(): Builder
    {
        $query = $this->applyCriterias($this->plainQuery());

        return $this->prepareQuery($query);
    }

    /**
     * Get new plain eloquent query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function plainQuery(): Builder
    {
        return $this->model->newQuery();
    }
}
