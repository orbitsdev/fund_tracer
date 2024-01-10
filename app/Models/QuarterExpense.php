<?php

namespace App\Models;

use App\Models\FourthLayer;
use App\Models\ProjectYear;
use App\Models\ProjectQuarter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuarterExpense extends Model
{
    use HasFactory;

public function project_quarter(){
    return $this->belongsTo(ProjectQuarter::class);
}
public function fourth_layer(){
    return $this->belongsTo(FourthLayer::class);
}
}
