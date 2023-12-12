<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    protected $table = 'projects';

    public function images()
    {
        return $this->hasMany(Images::class, 'project_id');
    }
}
