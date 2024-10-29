<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('todo_id')->constrained()->onDelete('cascade'); // Foreign key reference to todos table
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key reference to user table
            $table->string('due_date'); // Due date for the task
            $table->text('description')->nullable(); // Optional description
            $table->string('status'); // Status of the task (e.g., "in-progress", "completed")
            $table->string('name'); // Status of the task (e.g., "in-progress", "completed")
            $table->boolean('isDeleted')->default(false); // Soft delete flag
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks'); // Drop the tasks table
    }
};
