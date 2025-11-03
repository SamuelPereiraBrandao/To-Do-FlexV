<?php

namespace App\Repositories\Eloquent;

use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTodoRepository implements TodoRepositoryInterface
{
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = Todo::query()->with(['tags', 'assignee:id,name,email', 'creator:id,name,email'])
            ->where(function ($qq) use ($userId) {
                $qq->where('user_id', $userId)->orWhere('assignee_id', $userId);
            });
        $this->applyFilters($q, $filters);
        return $q->latest()->paginate($perPage);
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = Todo::query()->with(['tags', 'assignee:id,name,email', 'creator:id,name,email']);
        $this->applyFilters($q, $filters, true);
        return $q->latest()->paginate($perPage);
    }

    public function create(array $data): Todo
    {
        return Todo::create($data);
    }

    public function update(Todo $todo, array $data): Todo
    {
        $todo->fill($data);
        $todo->save();
        return $todo;
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
    }

    public function findWithRelations(int $id): ?Todo
    {
        return Todo::with(['tags', 'assignee:id,name,email'])->find($id);
    }

    protected function applyFilters($q, array $filters, bool $admin = false): void
    {
        if (!empty($filters['theme_id'])) $q->where('theme_id', $filters['theme_id']);
        if (array_key_exists('done', $filters)) $q->where('done', (bool)$filters['done']);
        if (!empty($filters['tag'])) $q->whereHas('tags', fn($qq) => $qq->where('name', $filters['tag']));
        if ($admin) {
            if (!empty($filters['user_id'])) $q->where('user_id', (int)$filters['user_id']);
            if (!empty($filters['assignee_id'])) $q->where('assignee_id', (int)$filters['assignee_id']);
        }
    }
}
