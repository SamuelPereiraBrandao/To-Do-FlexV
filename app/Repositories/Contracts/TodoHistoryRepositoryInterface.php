<?php

namespace App\Repositories\Contracts;

use App\Models\Todo;
use Illuminate\Support\Collection;

interface TodoHistoryRepositoryInterface
{
    public function listForTodo(Todo $todo): Collection;
    public function add(Todo $todo, ?int $performedBy, string $action, $changes = null): void;
}

