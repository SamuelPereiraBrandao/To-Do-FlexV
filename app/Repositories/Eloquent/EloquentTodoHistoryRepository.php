<?php

namespace App\Repositories\Eloquent;

use App\Models\Todo;
use App\Models\TodoHistory;
use App\Repositories\Contracts\TodoHistoryRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentTodoHistoryRepository implements TodoHistoryRepositoryInterface
{
    public function listForTodo(Todo $todo): Collection
    {
        return $todo->histories()->with('performer:id,name,email')->latest()->get();
    }

    public function add(Todo $todo, ?int $performedBy, string $action, $changes = null): void
    {
        TodoHistory::create([
            'todo_id' => $todo->id,
            'performed_by' => $performedBy,
            'action' => $action,
            'changes' => $changes,
        ]);
    }
}

