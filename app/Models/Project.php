<?php

namespace App\Models;

use App\Models\Expense;
use App\Models\Program;
use App\Models\ProjectYear;
use App\Models\ProjectDevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    public function program(){
        return $this->belongsTo(Program::class);
    }
    public function manager(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function expenses(){
        return $this->hasMany(Expense::class);
    }
    public function project_years(){
        return $this->hasMany(ProjectYear::class);
    }

    public function project_divisions(){
        return $this->hasMany(ProjectDevision::class);
    }

}
