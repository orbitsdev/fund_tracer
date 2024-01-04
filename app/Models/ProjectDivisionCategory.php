<?php

namespace App\Models;

use App\Models\ProjectDevision;
use App\Models\DivisionCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDivisionCategory extends Model
{
    use HasFactory;

    public function project_devision(){
        return $this->belongsTo(ProjectDevision::class);
    }
    public function division_categories(){
        return $this->belongsTo(DivisionCategory::class);
    }
}
