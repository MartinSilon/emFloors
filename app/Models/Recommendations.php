<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendations extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'body',
        'rating',
        'to_publish',
        'pinned',
    ];

    use HasFactory;
}
