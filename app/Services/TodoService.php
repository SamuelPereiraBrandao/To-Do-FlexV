<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\User;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TodoHistoryRepositoryInterface;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TodoService
{
    public function __construct(
        protected TodoRepositoryInterface $todos,
        protected TagRepositoryInterface $tags,
        protected TodoHistoryRepositoryInterface $history,
    ) {}

    public function index(User $user, array $filters): LengthAwarePaginator
    {
        return $user->isAdmin()
            ? $this->todos->paginateForAdmin($filters)
            : $this->todos->paginateForUser($user->id, $filters);
    }

    public function store(User $user, array $data): Todo
    {
        return DB::transaction(function () use ($user, $data) {
            if (array_key_exists('due_at', $data) && empty($data['due_at'])) {
                $data['due_at'] = null;
            }
            $payload = [
                'user_id' => $user->isAdmin() && !empty($data['user_id']) ? (int)$data['user_id'] : $user->id,
                'assignee_id' => $data['assignee_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'theme_id' => $data['theme_id'] ?? null,
                'done' => $data['done'] ?? false,
                'is_public' => $data['is_public'] ?? false,
                'due_at' => $data['due_at'] ?? null,
            ];
            $todo = $this->todos->create($payload);
            if (!empty($data['tags'])) {
                $ids = $this->tags->ensure($data['tags']);
                $todo->tags()->sync($ids);
            }
            $this->history->add($todo, $user->id, 'created', [
                'title' => $todo->title,
                'assignee_id' => $todo->assignee_id,
                'theme_id' => $todo->theme_id,
                'tags' => $todo->tags()->pluck('name')->all(),
            ]);
            return $todo->load(['tags','assignee:id,name,email']);
        });
    }

    public function show(User $user, Todo $todo): Todo
    {
        $this->authorizeView($user, $todo);
        return $todo->load(['tags','assignee:id,name,email']);
    }

    public function update(User $user, Todo $todo, array $data): Todo
    {
        $this->authorizeUpdate($user, $todo);
        return DB::transaction(function () use ($user, $todo, $data) {
            $originalDone = (bool)$todo->done;
            if (isset($data['user_id']) && $user->isAdmin()) {
                $todo->user_id = (int)$data['user_id'];
            }
            if (array_key_exists('due_at', $data) && empty($data['due_at'])) {
                $data['due_at'] = null;
            }
            $todo = $this->todos->update($todo, $data);
            if (array_key_exists('tags', $data)) {
                $ids = $this->tags->ensure($data['tags'] ?? []);
                $todo->tags()->sync($ids);
            }
            if (array_key_exists('done', $data)) {
                $this->history->add($todo, $user->id, $todo->done ? 'completed' : 'reopened', [
                    'from' => $originalDone,
                    'to' => (bool)$todo->done,
                ]);
            } else {
                $this->history->add($todo, $user->id, 'updated', [
                    'changes' => $todo->getChanges(),
                    'tags' => $todo->tags()->pluck('name')->all(),
                ]);
            }
            return $todo->load(['tags','assignee:id,name,email']);
        });
    }

    public function destroy(User $user, Todo $todo): void
    {
        $this->authorizeDestroy($user, $todo);
        DB::transaction(function () use ($user, $todo) {
            $this->todos->delete($todo);
            $this->history->add($todo, $user->id, 'deleted');
        });
    }

    public function history(User $user, Todo $todo)
    {
        $this->authorizeView($user, $todo);
        return $this->history->listForTodo($todo);
    }

    protected function authorizeView(User $user, Todo $todo): void
    {
        if ($user->isAdmin()) return;
        abort_unless($todo->user_id === $user->id || $todo->assignee_id === $user->id, 403);
    }

    protected function authorizeUpdate(User $user, Todo $todo): void
    {
        if ($user->isAdmin()) return;
        abort_unless($todo->user_id === $user->id || $todo->assignee_id === $user->id, 403);
    }

    protected function authorizeDestroy(User $user, Todo $todo): void
    {
        if ($user->isAdmin()) return;
        abort_unless($todo->user_id === $user->id, 403);
    }
}
