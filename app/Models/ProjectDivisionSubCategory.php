<?php

namespace App\Models;

use App\Models\ProjectDivisionCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategoryExpense;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDivisionSubCategory extends Model
{
    use HasFactory;


    
    public function project_division_sub_category_expenses(){
        return $this->hasMany(ProjectDivisionSubCategoryExpense::class);
    }

}
