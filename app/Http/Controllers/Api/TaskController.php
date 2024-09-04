<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        if ($tasks->count() > 0) {
            return TaskResource::collection($tasks);
        } else {
            return response()->json(['message' => 'No tasks available'], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'is_completed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages()
            ], 422);
        }

        $task = Task::create([
            'title' => $request->title,
            'is_completed' => $request->is_completed,
        ]);

        return response()->json([
            'message' => 'Task added successfully',
            'data' => new TaskResource($task)
        ], 201);
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        } else {
            return new TaskResource($task);
        }
    }

    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'is_completed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages()
            ], 422);
        }

        $task->update([
            'title' => $request->title,
            'is_completed' => $request->is_completed,
        ]);

        return response()->json([
            'message' => 'Task added successfully',
            'data' => new TaskResource($task)
        ], 201);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
