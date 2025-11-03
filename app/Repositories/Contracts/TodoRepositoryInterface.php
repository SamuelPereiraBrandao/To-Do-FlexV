<?php

namespace App\Repositories\Contracts;

use App\Models\Todo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TodoRepositoryInterface
{
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function paginateForAdmin(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Todo;
    public function update(Todo $todo, array $data): Todo;
    public function delete(Todo $todo): void;
    public function findWithRelations(int $id): ?Todo;
}

