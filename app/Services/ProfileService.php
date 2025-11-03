<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function me(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'user',
            'avatar_url' => $user->avatar_path ? url("storage/{$user->avatar_path}") : null,
        ];
    }

    public function update(User $user, array $data): array
    {
        if (array_key_exists('role', $data)) {
            abort_unless($user->isAdmin(), 403);
        }
        $user->fill($data)->save();
        return $user->only(['id','name','email']);
    }

    public function uploadAvatar(User $user, UploadedFile $file): array
    {
        $path = $file->store('avatars','public');
        $user->avatar_path = $path;
        $user->save();
        return ['avatar_url' => url("storage/{$path}")];
    }

    public function removeAvatar(User $user): void
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
            $user->save();
        }
    }
}

