<?php

namespace App\Repositories\Contracts;

use App\Models\Todo;
use App\Models\TodoComment;
use Illuminate\Support\Collection;

interface TodoCommentRepositoryInterface
{
    public function listForTodo(Todo $todo): Collection;
    public function create(Todo $todo, int $userId, string $body): TodoComment;
}

