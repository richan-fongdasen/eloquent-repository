<?php

namespace RichanFongdasen\Repository;

use Illuminate\Database\Eloquent\Builder;

class ScopeBuilder
{
    /**
     * Apply query scope into the given query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $column
     * @param mixed                                 $value
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function apply(Builder $query, $column, $value)
    {
        if (is_numeric($column) && is_array($value)) {
            return call_user_func_array([$query, 'where'], $value);
        }

        return is_array($value) ? $query->whereIn($column, $value) : $query->where($column, $value);
    }

    /**
     * Generate query scopes based on the given description.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $conditions
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function generate(Builder $query, array $conditions)
    {
        foreach ($conditions as $column => $value) {
            static::apply($query, $column, $value);
        }

        return $query;
    }
}
