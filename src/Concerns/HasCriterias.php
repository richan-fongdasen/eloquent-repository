<?php

namespace RichanFongdasen\Repository\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RichanFongdasen\Repository\Contracts\Criteria;

trait HasCriterias
{
    /**
     * Registered criteria collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected Collection $criterias;

    /**
     * Apply the registered criteria to
     * the given Eloquent Builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCriterias(Builder $query): Builder
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
    protected function bootHasCriterias(): void
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
    protected function buildCriteria($criteria): void
    {
        if (is_string($criteria)) {
            $criteria = \App::make($criteria);
        }

        if (!($criteria instanceof Criteria)) {
            throw new InvalidArgumentException('Failed to build criteria, passed object is not an instance of criteria.');
        }

        $criteria->setModel($this->newModel());

        $this->criterias->put(get_class($criteria), $criteria);
    }

    /**
     * Flush all the registered criteria.
     *
     * @return void
     */
    public function flushCriteria(): void
    {
        $this->criterias = new Collection();
    }

    /**
     * Forget the given criteria from collection.
     *
     * @param string $criteria
     *
     * @return void
     */
    protected function forgetCriteria(string $criteria): void
    {
        if ($this->criterias->has($criteria)) {
            $this->criterias->forget($criteria);
        }
    }

    /**
     * Get the registered criteria(s).
     *
     * @param string|null $class
     *
     * @return mixed
     */
    public function getCriteria(string $class = null)
    {
        if ($class === null) {
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
     * @return $this
     */
    public function pushCriteria(array $criterias): self
    {
        foreach ($criterias as $criteria) {
            $this->buildCriteria($criteria);
        }

        return $this;
    }

    /**
     * Remove / unregister the given criteria.
     *
     * @param string|array $criterias
     *
     * @return $this
     */
    public function removeCriteria($criterias): self
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
