<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator; 
use App\Models\Todo; // Add this for the Todo model
use Illuminate\Http\JsonResponse; // Add this at the top with your other use statements


class TodoController extends Controller
{
    // Create a new Todo
    public function test(): JsonResponse 
    {
        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function create(Request $request): JsonResponse
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'deadline' => 'required|date_format:Y/m/d', // Adjust this to match the input format
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Create a new Todo with the user_id
        $todo = Todo::create([
            'name' => $request->name,
            'deadline' => $request->deadline,
            'description' => $request->description,
            'user_id' => $userId, // Assign the user_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo created successfully!',
            'data' => $todo,
        ], Response::HTTP_CREATED);
    }
    // Read all Todos
    public function showAll(): JsonResponse
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Retrieve only the todos that belong to the authenticated user
        $todos = Todo::where('user_id', $userId)->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $todos,
        ], Response::HTTP_OK);
    }

    // Read a single Todo
    public function viewSingle($id): JsonResponse
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Find the Todo by its ID and make sure it belongs to the authenticated user
        $todo = Todo::where('id', $id)->where('user_id', $userId)->first();

        if (!$todo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Todo not found or you do not have permission to view it.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'data' => $todo,
        ], Response::HTTP_OK);
    }

    // Update a Todo
    public function update(Request $request, $id): JsonResponse
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Find the Todo by its ID and ensure it belongs to the authenticated user
        $todo = Todo::where('id', $id)->where('user_id', $userId)->first();

        if (!$todo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Todo not found or you do not have permission to update it.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'deadline' => 'sometimes|date_format:Y/m/d',
            'description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update the Todo
        $todo->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully!',
            'data' => $todo,
        ], Response::HTTP_OK);
    }

    // Delete a Todo
    public function deleteTodo(Request $request, $id): JsonResponse
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Find the Todo by its ID and ensure it belongs to the authenticated user
        $todo = Todo::where('id', $id)->where('user_id', $userId)->first();

        if (!$todo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Todo not found or you do not have permission to delete it.',
            ], Response::HTTP_NOT_FOUND);
        }

      
            // Perform a hard delete
            $todo->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Todo deleted permanently!',
            ], Response::HTTP_OK);
        
    }
}
