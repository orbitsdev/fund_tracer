<?php

namespace App\Models;

use App\Models\ProjectYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quarter extends Model
{
    use HasFactory;

    public function project_year(){
        return $this->belongsTo(ProjectYear::class);
    }
}
