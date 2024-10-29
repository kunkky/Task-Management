<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    // Define the fillable fields for  assignment
    protected $fillable = [
        'name',
        'user_id',
        'deadline',
        'description',
    ];
}
