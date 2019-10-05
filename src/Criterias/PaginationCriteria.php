<?php

namespace RichanFongdasen\Repository\Criterias;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use RichanFongdasen\Repository\Contracts\Criteria;

class PaginationCriteria implements Criteria
{
    /**
     * The current page being "viewed".
     *
     * @var int
     */
    protected $currentPage;

    /**
     * The base path to assign to all URLs.
     *
     * @var string
     */
    protected $currentPath;

    /**
     * Eloquent model associated to the criteria.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The query string variable used to store the page.
     *
     * @var string
     */
    protected $pageName = 'page';

    /**
     * The number of items to be shown per page.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Total records count.
     *
     * @var int
     */
    protected $recordCount;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->currentPath = $request->url();
        $this->currentPage = $this->resolveCurrentPage($request);
    }

    /**
     * Build the LengthAwarePaginator object.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function buildPaginator(Builder $query, int $perPage) :LengthAwarePaginator
    {
        $this->setPerPage($perPage);
        $this->manipulate($query);

        $options = [
            'path'     => $this->currentPath,
            'pageName' => $this->pageName,
        ];

        return new LengthAwarePaginator(
            $query->get(),
            $this->recordCount,
            $this->perPage,
            $this->currentPage,
            $options
        );
    }

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
        $this->recordCount = $query->toBase()->getCountForPagination();

        $query->forPage($this->currentPage, $this->perPage);

        return $query;
    }

    /**
     * Specify whether the criteria will only be implemented
     * on demand, or it should be implemented automatically.
     *
     * @return bool
     */
    public function onDemandOnly() :bool
    {
        return true;
    }

    /**
     * Resolve current page value based on the given
     * Request object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    protected function resolveCurrentPage(Request $request) :int
    {
        $page = (int) $request->input($this->pageName);

        return ($page >= 1) ? $page : 1;
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

    /**
     * Set the number of items to be shown per page.
     *
     * @param int $perPage
     * @return void
     */
    public function setPerPage(int $perPage) :void
    {
        if ($perPage < 1) {
            $perPage = $this->model->getPerPage();
        }
        $this->perPage = $perPage;
    }
}
