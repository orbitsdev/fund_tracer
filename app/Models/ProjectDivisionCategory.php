<?php

namespace App\Models;

use App\Models\ProjectQuarter;
use App\Models\ProjectDevision;
use App\Models\DivisionCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategory;
use App\Models\ProjectDivisionSubCategoryExpense;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDivisionCategory extends Model
{
    use HasFactory;

    public function project_devision(){
        return $this->belongsTo(ProjectDevision::class);
    }

    public function division_category(){
        return $this->belongsTo(DivisionCategory::class);
    }


    public function project_quarter(){
        return $this->belongsTo(ProjectQuarter::class);
    }




    public function project_division_sub_category_expenses(){
        return $this->hasMany(ProjectDivisionSubCategoryExpense::class);
    }



}
