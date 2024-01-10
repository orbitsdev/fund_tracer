<?php

namespace App\Models;

use App\Models\FourthLayer;
use App\Models\ProjectYear;
use App\Models\ProjectQuarter;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuarterExpenseBudgetDivision;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuarterExpense extends Model
{
    use HasFactory;


public function quarter_expense_budget_division(){
    return $this->belongsTo(QuarterExpenseBudgetDivision::class);
}
public function fourth_layer(){
    return $this->belongsTo(FourthLayer::class);
}
}
