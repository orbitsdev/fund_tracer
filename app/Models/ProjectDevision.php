<?php

namespace App\Models;

use App\Models\Project;
use App\Models\Division;
use App\Models\ProjectQuarter;
use App\Models\ProjectDivisionCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuarterExpenseBudgetDivision;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDevision extends Model
{
    use HasFactory;

    public function division(){
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function project_quarter(){
        return $this->belongsTo(ProjectQuarter::class);
    }
    public function project_division_categories(){
    return $this->hasMany(ProjectDivisionCategory::class);
}


    public function quarter_expense_budget_divisions(){
        return $this->hasMany(QuarterExpenseBudgetDivision::class);
    }
}
