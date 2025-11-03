<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(protected UserRepositoryInterface $users) {}

    public function search(?string $term): Collection
    {
        return $this->users->search($term);
    }

    public function profile(User $auth, User $user): array
    {
        if (!$auth->isAdmin() && $auth->id !== $user->id) abort(403);
        return $this->users->profileWithTodos($user);
    }

    public function updateRole(User $auth, User $user, string $role): User
    {
        abort_unless($auth->isAdmin(), 403);
        return $this->users->updateRole($user, $role);
    }
}

