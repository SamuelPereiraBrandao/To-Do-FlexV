<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $service) {}
    public function me(Request $request)
    {
        return response()->json($this->service->me($request->user()));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255','unique:users,email,'. $request->user()->id],
            'role' => ['sometimes','in:admin,user'],
        ]);
        $updated = $this->service->update($request->user(), $data);
        return response()->json($updated);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => ['required','image','max:2048']]);
        return response()->json($this->service->uploadAvatar($request->user(), $request->file('avatar')));
    }

    public function removeAvatar(Request $request)
    {
        $this->service->removeAvatar($request->user());
        return response()->noContent();
    }
}
