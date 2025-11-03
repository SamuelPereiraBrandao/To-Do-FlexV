<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function search(?string $term, int $limit = 50): Collection;
    public function profileWithTodos(User $user): array;
    public function updateRole(User $user, string $role): User;
}

