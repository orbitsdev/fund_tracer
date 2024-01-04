<?php

namespace App\Models;

use App\Models\ProjectYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Year extends Model
{
    use HasFactory;


    public function project_years(){
        return $this->hasMany(ProjectYear::class);
    }
}
