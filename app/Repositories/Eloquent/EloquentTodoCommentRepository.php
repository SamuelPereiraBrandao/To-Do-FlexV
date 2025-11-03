<?php

namespace App\Repositories\Eloquent;

use App\Models\Todo;
use App\Models\TodoComment;
use App\Repositories\Contracts\TodoCommentRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentTodoCommentRepository implements TodoCommentRepositoryInterface
{
    public function listForTodo(Todo $todo): Collection
    {
        return $todo->comments()->latest()->get();
    }

    public function create(Todo $todo, int $userId, string $body): TodoComment
    {
        $comment = TodoComment::create([
            'todo_id' => $todo->id,
            'user_id' => $userId,
            'body' => $body,
        ]);
        return $comment->load('user:id,name,email');
    }
}

