<?php

namespace App\Models;

use App\Models\ProjectQuarter;
use App\Models\ProjectDevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuarterExpenseBudgetDivision extends Model
{
    use HasFactory;

    public function project_quarter(){
        return $this->belongsTo(ProjectQuarter::class);
    }
    public function project_devision(){
        return $this->belongsTo(ProjectDevision::class);
    }
}
