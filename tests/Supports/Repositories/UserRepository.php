<?php

namespace RichanFongdasen\Repository\Tests\Supports\Repositories;

use RichanFongdasen\Repository\EloquentRepository;
use RichanFongdasen\Repository\Tests\Supports\Models\User;

class UserRepository extends EloquentRepository
{
    /**
     * Class constructor.
     *
     * @param \RichanFongdasen\Repository\Tests\Supports\Models\User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
