<?php

namespace RichanFongdasen\Repository\Tests\Supports\Repositories;

use RichanFongdasen\Repository\EloquentRepository;
use RichanFongdasen\Repository\Tests\Supports\Models\Comment;

class CommentRepository extends EloquentRepository
{
    /**
     * Class constructor
     *
     * @param \RichanFongdasen\Repository\Tests\Supports\Models\Comment $model
     */
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }
}
