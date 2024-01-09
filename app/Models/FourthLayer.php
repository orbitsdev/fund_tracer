<?php

namespace App\Models;

use App\Models\QuarterExpense;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategoryExpense;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FourthLayer extends Model
{
    use HasFactory;
    
    public function project_division_sub_category_expense(){
        return $this->belongsTo(ProjectDivisionSubCategoryExpense::class);
    }

    public function QuarterExpense(){
        return $this->hasMany(QuarterExpense::class);
    }
}
