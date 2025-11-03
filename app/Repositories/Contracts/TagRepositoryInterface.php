<?php

namespace App\Repositories\Contracts;

interface TagRepositoryInterface
{
    /** Ensure names exist and return their IDs */
    public function ensure(array $names): array;
}

