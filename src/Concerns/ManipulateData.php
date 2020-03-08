<?php

namespace RichanFongdasen\Repository\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ManipulateData
{
    /**
     * Save a new model and return the instance.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes): Model
    {
        return $this->newModel()->create($attributes);
    }

    /**
     * Find and delete a model from the database
     * based on the given primary key.
     *
     * @param mixed $key
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete($key): bool
    {
        return $this->plainQuery()->findOrFail($key)->delete();
    }

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes)
    {
        return $this->plainQuery()->firstOrCreate($attributes);
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function firstOrNew(array $attributes)
    {
        return $this->plainQuery()->firstOrNew($attributes);
    }

    /**
     * Find and restore a soft-deleted model instance
     * based on the given primary key.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function restore($key): bool
    {
        return $this->plainQuery()
            ->withTrashed()
            ->findOrFail($key)
            ->restore();
    }

    /**
     * Find and update a model in the database
     * based on the given primary key.
     *
     * @param mixed $key
     * @param array $attributes
     *
     * @throws ModelNotFoundException
     *
     * @return bool
     */
    public function update($key, array $attributes): bool
    {
        $model = $this->plainQuery()
            ->find($key);

        if (!($model instanceof Model)) {
            throw new ModelNotFoundException('Failed to retrieve model '.get_class($this->model).' with key: '.$key);
        }

        return $model->update($attributes);
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $attributes, array $values)
    {
        return $this->plainQuery()->updateOrCreate($attributes, $values);
    }

    /**
     * Get new eloquent model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract public function newModel(): Model;

    /**
     * Get new plain eloquent query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function plainQuery(): Builder;
}
