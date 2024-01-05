<?php

namespace App\Models;

use App\Models\Quarter;
use App\Models\ProjectYear;
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

    public function project_division_sub_category_expenses(){
        return $this->hasMany(ProjectDivisionSubCategoryExpense::class);
    }


}
