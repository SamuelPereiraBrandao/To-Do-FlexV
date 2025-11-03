<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Services\CommentService;
use Illuminate\Http\Request;

class TodoCommentController extends Controller
{
    public function __construct(protected CommentService $service) {}
    public function index(Request $request, Todo $todo)
    {
        $comments = $this->service->list($request->user(), $todo);
        return response()->json($comments);
    }

    public function store(Request $request, Todo $todo)
    {
        $data = $request->validate([
            'body' => ['required','string','max:5000'],
        ]);
        $comment = $this->service->create($request->user(), $todo, $data['body']);
        return response()->json($comment, 201);
    }
}
