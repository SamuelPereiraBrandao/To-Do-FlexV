<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\User;
use App\Repositories\Contracts\TodoCommentRepositoryInterface;

class CommentService
{
    public function __construct(protected TodoCommentRepositoryInterface $comments) {}

    public function list(User $user, Todo $todo)
    {
        $this->authorize($user, $todo);
        return $this->comments->listForTodo($todo);
    }

    public function create(User $user, Todo $todo, string $body)
    {
        $this->authorize($user, $todo);
        return $this->comments->create($todo, $user->id, $body);
    }

    protected function authorize(User $user, Todo $todo): void
    {
        if ($user->isAdmin()) return;
        abort_unless($todo->user_id === $user->id || $todo->assignee_id === $user->id, 403);
    }
}

