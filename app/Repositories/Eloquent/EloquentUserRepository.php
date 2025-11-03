<?php

namespace App\Repositories\Eloquent;

use App\Models\Todo;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function search(?string $term, int $limit = 50): Collection
    {
        $q = User::query()->select(['id','name','email'])->orderBy('name');
        if ($term) {
            $like = '%' . str_replace('%', '', $term) . '%';
            $q->where(function ($qq) use ($like) {
                $qq->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }
        return $q->limit($limit)->get();
    }

    public function profileWithTodos(User $user): array
    {
        $owned = Todo::where('user_id', $user->id)->with(['tags','assignee:id,name,email'])->latest()->get();
        $assigned = Todo::where('assignee_id', $user->id)->with(['tags','assignee:id,name,email'])->latest()->get();
        return [
            'user' => $user->only(['id','name','email']) + [
                'role' => $user->role ?? 'user',
                'avatar_url' => $user->avatar_path ? url('storage/' . $user->avatar_path) : null,
            ],
            'owned' => $owned,
            'assigned' => $assigned,
        ];
    }

    public function updateRole(User $user, string $role): User
    {
        $user->role = $role;
        $user->save();
        return $user;
    }
}
