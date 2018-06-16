<?php

namespace RichanFongdasen\Repository\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RichanFongdasen\Repository\Contracts\Criteria;

trait HasCriterias
{
    /**
     * Registered criterias collection.
     *
     * @var array
     */
    protected $criterias;

    /**
     * Apply the registered criterias to
     * the given Eloquent Builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCriterias(Builder $query)
    {
        $this->criterias->each(function ($criteria, $key) use ($query) {
            if (!$criteria->onDemandOnly()) {
                $criteria->manipulate($query);
            }

            return $key;
        });

        return $query;
    }

    /**
     * Boot HasCriterias.
     *
     * @return void
     */
    protected function bootHasCriterias()
    {
        $this->flushCriteria();
    }

    /**
     * Build the given criteria class and register
     * the initiated criteria instance.
     *
     * @param mixed $criteria
     *
     * @return void
     */
    protected function buildCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = \App::make($criteria);
        }

        if (!($criteria instanceof Criteria)) {
            throw new InvalidArgumentException();
        }

        $criteria->setModel($this->newModel());

        $this->criterias->put(get_class($criteria), $criteria);
    }

    /**
     * Flush all of registered criterias.
     *
     * @return void
     */
    public function flushCriteria()
    {
        $this->criterias = collect();
    }

    /**
     * Forget the given criteria from collection.
     *
     * @param string $criteria
     *
     * @return void
     */
    protected function forgetCriteria(string $criteria)
    {
        if ($this->criterias->has($criteria)) {
            $this->criterias->forget($criteria);
        }
    }

    /**
     * Get the registered criteria(s).
     *
     * @param string $class
     *
     * @return array
     */
    public function getCriteria(string $class = null)
    {
        if (empty($class)) {
            return $this->criterias;
        }

        if (!$this->criterias->has($class)) {
            return null;
        }

        return $this->criterias->get($class);
    }

    /**
     * Push / register the given criterias.
     *
     * @param array $criterias
     *
     * @return mixed
     */
    public function pushCriteria(array $criterias)
    {
        foreach ($criterias as $criteria) {
            $this->buildCriteria($criteria);
        }

        return $this;
    }

    /**
     * Remove / unregister the given criterias.
     *
     * @param mixed $criterias
     *
     * @return mixed
     */
    public function removeCriteria($criterias)
    {
        $criterias = (array) $criterias;

        foreach ($criterias as $criteria) {
            $this->forgetCriteria($criteria);
        }

        return $this;
    }

    /**
     * Get new eloquent model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract public function newModel();
}
