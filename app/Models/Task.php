<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'todo_id',
        'user_id',
        'due_date',
        'description',
        'status',
        'isDeleted',
        'name',

    ];
}
