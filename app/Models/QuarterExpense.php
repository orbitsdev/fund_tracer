<?php

namespace App\Models;

use App\Models\FourthLayer;
use App\Models\ProjectYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuarterExpense extends Model
{
    use HasFactory;

public function project_year(){
    return $this->belongsTo(ProjectYear::class);
}
public function fourth_layer(){
    return $this->belongsTo(FourthLayer::class);
}
}
