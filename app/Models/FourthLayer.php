<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectDivisionSubCategoryExpense;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FourthLayer extends Model
{
    use HasFactory;
    
    public function project_division_sub_category_expense(){
        return $this->belongsTo(ProjectDivisionSubCategoryExpense::class);
    }
}
