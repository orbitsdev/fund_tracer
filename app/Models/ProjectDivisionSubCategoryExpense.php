<?php

namespace App\Models;

use App\Models\Quarter;
use App\Models\FourthLayer;
use App\Models\ProjectQuarter;
use App\Models\ProjectDivisionCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDivisionSubCategoryExpense extends Model
{
    use HasFactory;

    public function project_division_category(){
        return $this->belongsTo(ProjectDivisionCategory::class);
    }

    public function fourth_layers(){
        return $this->hasMany(FourthLayer::class);
    }

    public function project_quarter(){
        return $this->belongsTo(ProjectQuarter::class);
    }
}
