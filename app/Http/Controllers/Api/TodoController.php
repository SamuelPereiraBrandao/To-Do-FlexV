<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use App\Services\TodoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    public function __construct(protected TodoService $service) {}
    public function index(Request $request)
    {
        $filters = [
            'theme_id' => $request->input('theme_id'),
            'done' => $request->has('done') ? filter_var($request->input('done'), FILTER_VALIDATE_BOOLEAN) : null,
            'tag' => $request->input('tag'),
            'user_id' => $request->input('user_id'),
            'assignee_id' => $request->input('assignee_id'),
        ];
        // remove null, string vazia e zero
        $filters = array_filter($filters, function ($v) {
            if ($v === null) return false;
            if (is_string($v) && $v === '') return false;
            if (is_numeric($v) && (int)$v === 0) return false;
            return true;
        });
        $todos = $this->service->index($request->user(), $filters);
        return response()->json($todos);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'theme_id' => ['nullable', 'string', 'max:64'],
            'done' => ['sometimes', 'boolean'],
            'is_public' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
            'assignee_id' => ['nullable', Rule::exists('users', 'id')],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:64'],
            'user_id' => ['nullable', Rule::exists('users','id')],
        ]);
        if (array_key_exists('due_at', $data) && empty($data['due_at'])) {
            $data['due_at'] = null;
        }
        if (!$user->isAdmin()) {
            unset($data['user_id']);
        } elseif (array_key_exists('user_id', $data) && empty($data['user_id'])) {
            unset($data['user_id']);
        }
        $todo = $this->service->store($user, $data);
        return response()->json($todo, 201);
    }

    public function show(Request $request, Todo $todo)
    {
        $todo = $this->service->show($request->user(), $todo);
        return response()->json($todo);
    }

    public function update(Request $request, Todo $todo)
    {
        $user = $request->user();

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'theme_id' => ['nullable', 'string', 'max:64'],
            'done' => ['sometimes', 'boolean'],
            'is_public' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
            'assignee_id' => ['nullable', Rule::exists('users', 'id')],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:64'],
            'user_id' => ['nullable', Rule::exists('users', 'id')],
        ]);
        if (array_key_exists('due_at', $data) && empty($data['due_at'])) {
            $data['due_at'] = null;
        }
        if (!$user->isAdmin()) {
            unset($data['user_id']);
        } elseif (array_key_exists('user_id', $data) && empty($data['user_id'])) {
            unset($data['user_id']);
        }

        $todo = $this->service->update($user, $todo, $data);
        return response()->json($todo);
    }

    public function destroy(Request $request, Todo $todo)
    {
        $user = $request->user();
        $this->service->destroy($user, $todo);
        return response()->noContent();
    }

    public function historyIndex(Request $request, Todo $todo)
    {
        $history = $this->service->history($request->user(), $todo);
        return response()->json($history);
    }
}
