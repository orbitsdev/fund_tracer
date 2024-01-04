<?php

namespace App\Models;

use App\Models\ProjectDivisionCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDivisionSubCategoryExpense extends Model
{
    use HasFactory;

    public function Project_division_category(){
        return $this->belongsTo(ProjectDivisionCategory::class);
    }
}
