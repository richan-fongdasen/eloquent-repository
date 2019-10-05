<?php

namespace RichanFongdasen\Repository\Criterias;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RichanFongdasen\Repository\Contracts\Criteria;

class WithTrashedCriteria implements Criteria
{
    /**
     * Eloquent model associated to the criteria.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Apply the criteria and manipulate the given
     * eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function manipulate(Builder $query) :Builder
    {
        if ($this->modelHasSoftDeletes()) {
            return $query->withTrashed();
        }

        return $query;
    }

    /**
     * Confirm if the current model uses SoftDeletes
     * trait.
     *
     * @return bool
     */
    public function modelHasSoftDeletes() :bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->model), true);
    }

    /**
     * Specify whether the criteria will only be implemented
     * on demand, or it should be implemented automatically.
     *
     * @return bool
     */
    public function onDemandOnly() :bool
    {
        return false;
    }

    /**
     * Set a model object for the criteria.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function setModel(Model $model) :void
    {
        $this->model = $model;
    }
}
