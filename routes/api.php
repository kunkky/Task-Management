<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiUserAuthentication; // Import your custom controller
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TasksController;

/*
|--------------------------------------------------------------------------
// API Routes
|--------------------------------------------------------------------------
// Here is where you can register API routes for your application.
// These routes are loaded by the RouteServiceProvider within a group 
| assigned the "api" middleware group. Enjoy building your API!
|--------------------------------------------------------------------------*/


// Middleware for JWT authentication
Route::middleware(['auth:api'])->group(function () {
    // Protected routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Todo routes
    Route::post('/createtodo', [TodoController::class, 'create']); // Create a new Todo
    Route::get('/todos', [TodoController::class, 'showAll']); // Get all Todos
    Route::get('/todos/{id}', [TodoController::class, 'viewSingle']); // Get a specific Todo
    Route::put('/todos/{id}', [TodoController::class, 'update']); // Update a Todo
    Route::delete('/todos/{id}', [TodoController::class, 'deleteTodo']); // Delete a Todo

    Route::post('/test', [TodoController::class, 'test']); // Create a new Todo

    //Task routes
    Route::post('/tasks', [TasksController::class, 'create']); // Create a new task
    Route::get('/tasks', [TasksController::class, 'getAll']); // Get all tasks
    Route::get('/tasks/trash', [TasksController::class, 'getAllTrash']); // Get all tasks trashed
    Route::get('/tasks/todo/{id}', [TasksController::class, 'getAllTodo']); // Get all tasks
    Route::get('/tasks/{id}', [TasksController::class, 'show']); // Get a specific task
    Route::put('/tasks/{id}', [TasksController::class, 'update']); // Update a task
    Route::post('/tasks/{id}/soft', [TasksController::class, 'softDelete']); // Soft delete 
    Route::post('/tasks/{id}/restore', [TasksController::class, 'restoreDelete']); //  restore a task
    Route::delete('/tasks/{id}/hard', [TasksController::class, 'hardDelete']); // Hard delete a task

    //log out
    Route::get('/logout', [ApiUserAuthentication::class, 'logout'])
    ->name('logout');
});

// Public routes for authentication
Route::post('/register', [ApiUserAuthentication::class, 'register'])
                ->middleware('guest')
                ->name('register');

Route::post('/login', [ApiUserAuthentication::class, 'login'])
                ->middleware('guest')
                ->name('login');

