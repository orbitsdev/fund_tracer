<?php

namespace App\Models;

use App\Models\ProjectDivisionCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DivisionCategory extends Model
{
    use HasFactory;


    public function project_division_categories(){
        return $this->hasMany(ProjectDivisionCategory::class);
    }
}
