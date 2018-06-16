<?php

namespace RichanFongdasen\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface Criteria
{
    /**
     * Apply the criteria and manipulate the given
     * eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function manipulate(Builder $query);

    /**
     * Specify whether the criteria will only be implemented
     * on demand, or it should be implemented automatically.
     *
     * @return bool
     */
    public function onDemandOnly();

    /**
     * Set a model object for the criteria.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function setModel(Model $model);
}
