<?php

namespace App\Models;

use App\Models\Quarter;
use App\Models\ProjectYear;
use App\Models\QuarterExpense;
use App\Models\ProjectDevision;
use App\Models\ProjectDivisionCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategoryExpense;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectQuarter extends Model
{
    use HasFactory;

    public function project_year(){
        return $this->belongsTo(ProjectYear::class);
    }
    public function quarter(){
        return $this->belongsTo(Quarter::class);
    }

    public function project_divisions(){
        return $this->hasMany(ProjectDevision::class);
    }

   
    // public function quarter_expenses(){
    //     return $this->hasMany(QuarterExpense::class);
    // }



    // public function project_division_sub_category_expenses(){
    //     return $this->hasMany(ProjectDivisionSubCategoryExpense::class);
    // }

    // public function project_division_categories(){
    //     return $this->hasMany(ProjectDivisionCategory::class);
    // }




}
