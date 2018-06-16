<?php

namespace RichanFongdasen\Repository\Tests\Supports\Repositories;

use RichanFongdasen\Repository\EloquentRepository;
use RichanFongdasen\Repository\Tests\Supports\Models\Post;

class PostRepository extends EloquentRepository
{
    /**
     * Class constructor.
     *
     * @param \RichanFongdasen\Repository\Tests\Supports\Models\Post $model
     */
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }
}
