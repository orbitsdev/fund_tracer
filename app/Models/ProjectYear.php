<?php

namespace App\Models;


use App\Models\Year;
use App\Models\Project;
use App\Models\Quarter;
use App\Models\ProjectQuarter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectYear extends Model
{
    use HasFactory;

    public function project(){
        return  $this->belongsTo(Project::class);
    }
    public function year(){
        return $this->belongsTo(Year::class);
    }

    public function project_quarters(){
        return $this->hasMany(ProjectQuarter::class);
    }
    // public function quarters(){
    //     return  $this->hasMany(Quarter::class);
    // }

}
