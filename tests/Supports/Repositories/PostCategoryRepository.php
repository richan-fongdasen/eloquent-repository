<?php

namespace RichanFongdasen\Repository\Tests\Supports\Repositories;

use RichanFongdasen\Repository\EloquentRepository;
use RichanFongdasen\Repository\Tests\Supports\Models\PostCategory;

class PostCategoryRepository extends EloquentRepository
{
    /**
     * Class constructor.
     *
     * @param \RichanFongdasen\Repository\Tests\Supports\Models\PostCategory $model
     */
    public function __construct(PostCategory $model)
    {
        parent::__construct($model);
    }
}
