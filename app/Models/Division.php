<?php

namespace App\Models;

use App\Models\ProjectDevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;


    public function project_divisions(){
        return $this->hasMany(ProjectDevision::class);
    }
}
