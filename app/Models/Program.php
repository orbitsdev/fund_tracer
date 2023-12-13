<?php

namespace App\Models;

use App\Models\File;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }
}
