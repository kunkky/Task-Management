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
        Schema::create('todos', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name of the todo
            $table->date('deadline'); // Deadline date
            $table->text('description')->nullable(); // Optional description
            $table->timestamps(); // Created at and updated at timestamps
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key reference to user table

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todos'); // Drops the table when rolling back
    }
};
