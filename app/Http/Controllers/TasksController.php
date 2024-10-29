<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Database\Eloquent\ModelNotFoundException; // Import this for catching specific exceptions
use App\Models\Todo; // Add this for the Todo model



class TasksController extends Controller



{
    /**
     * Create a new task.
     */

     
     public function create(Request $request): JsonResponse
     {
         // Get the authenticated user's ID
         $userId = Auth::id();
 
         // Validate the incoming request
         $validator = Validator::make($request->all(), [
             'todo_id' => 'required|exists:todos,id',
             'name' => 'required',
             'due_date' => 'nullable|date_format:d/m/Y H:i',
             'description' => 'required|string|max:255',
             'status' => 'string|max:50',
         ]);
 
         if ($validator->fails()) {
             return response()->json($validator->errors(), 422);
         }
 
         // Check if the authenticated user owns the specified todo
         try {
             $todo = Todo::where('id', $request->todo_id)
                         ->where('user_id', $userId) // Ensure the todo belongs to the authenticated user
                         ->firstOrFail(); // This will throw a ModelNotFoundException if not found
         } catch (ModelNotFoundException $e) {
             return response()->json(['message' => 'You do not own this todo or it does not exist. Please use a valid ID that belongs to you.'], 403);
         } catch (\Exception $e) {
             return response()->json(['message' => 'An unexpected error occurred. Please try again later.'], 500);
         }
 
         // Create the task
         $task = Task::create([
             'todo_id' => $request->todo_id,
             'name' => $request->name,
             'due_date' => $request->due_date,
             'description' => $request->description,
             'status' => $request->status ?? "pending", 
             'isDeleted' => false,
             'user_id' => $userId,  // Associate task with the signed-in user
         ]);
 
        
         return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully!',
            'task' => $task,
        ], JsonResponse::HTTP_CREATED);
     }
     

    /**
     * Get all tasks.
     */
    public function getAll(): JsonResponse
    {
         // Get the authenticated user's ID
         $userId = Auth::id(); 
        $tasks = Task::where('isDeleted', false)->where('user_id', $userId)->paginate(20); // Get only non-deleted tasks
        
        return response()->json([
            'status' => 'success',
            'message' => 'Task fetched successfully!',
            'tasks' => $tasks,
        ], 200);
    }
    public function getAllTrash(): JsonResponse
    {
         // Get the authenticated user's ID
         $userId = Auth::id(); 
        $tasks = Task::where('isDeleted', true)->where('user_id', $userId)->paginate(20); // Get only non-deleted tasks
        
        return response()->json([
            'status' => 'success',
            'message' => 'Task fetched successfully!',
            'tasks' => $tasks,
        ], 200);
    }
    /**
     * Get all tasks.
     */
    public function getAllTodo($id): JsonResponse
{
     // Get the authenticated user's ID
     $userId = Auth::id();
    $tasks = Task::where('todo_id', $id) // Filter by the provided todo_id
                 ->where('isDeleted', false) // Filter by isDeleted being false
                 ->where('user_id', $userId)// ensusr its this user that own task
                 ->paginate(20);

    if ($tasks->isEmpty()) {
        return response()->json(['message' => 'No tasks found'], 404);
    }

    return response()->json(['tasks' => $tasks], 200);
}

    /**
     * Get a specific task by ID.
     */
    public function show($id): JsonResponse
    {

         // Get the authenticated user's ID
         $userId = Auth::id();
        $task = Task::where('id', $id)->where('isDeleted', false)->where('user_id', $userId)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Task fetched successfully!',
            'task' => $task,
        ], 200);
    }

    /**
     * Update a task by ID.
     */
    public function update(Request $request, $id): JsonResponse
    {
         // Get the authenticated user's ID
         $userId = Auth::id();

         //check if task exists
        $task = Task::where('id', $id)->where('isDeleted', false)->where('user_id', $userId)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'todo_id' => 'nullable|exists:todos,id',
            'due_date' => 'nullable|date_format:d/m/Y H:i',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $task->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully!',
            'task' => $task,
        ], 200);
    }

    /**
     * Soft delete a task by ID (toggle isDeleted).
     */
    public function softDelete($id): JsonResponse
    {
         // Get the authenticated user's ID
         $userId = Auth::id();
        $task = Task::where('id', $id)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $task->isDeleted = true; // Toggle the isDeleted status
        $task->save();

        return response()->json([
            'status' => 'success',
            'message' => $task->isDeleted ? 'Task soft deleted successfully' : 'Task restored successfully',
            'task' => $task->isDeleted ?null :$task,
        ], 200);
    }
    //restor task deleted
    public function restoreDelete($id): JsonResponse
    {
         // Get the authenticated user's ID
         $userId = Auth::id();
        $task = Task::where('id', $id)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $task->isDeleted = false; // Toggle the isDeleted status
        $task->save();

        return response()->json([
            'status' => 'success',
            'message' => $task->isDeleted ? 'Task soft deleted successfully' : 'Task restored successfully',
            'task' => $task->isDeleted ?null :$task,
        ], 200);
    }

    /**
     * Hard delete a task by ID.
     */
    public function hardDelete($id): JsonResponse
    {
         // Get the authenticated user's ID
         $userId = Auth::id();
        $task = Task::where('id', $id)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $task->delete(); // Hard delete (remove from database)
        return response()->json([
            'status' => 'success',
            'message' => 'Task hard deleted successfully',
        ], 200);
    }
}
