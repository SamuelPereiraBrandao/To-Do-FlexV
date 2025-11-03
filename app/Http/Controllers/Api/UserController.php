<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $service) {}
    public function index(Request $request)
    {
        return response()->json($this->service->search($request->string('search') ?: null));
    }

    public function show(Request $request, User $user)
    {
        return response()->json($this->service->profile($request->user(), $user));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required','in:admin,user'],
        ]);
        $updated = $this->service->updateRole($request->user(), $user, $data['role']);
        return response()->json($updated->only(['id','name','email','role']));
    }
}
