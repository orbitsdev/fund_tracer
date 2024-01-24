<?php

namespace Database\Seeders;

use App\Models\ImplementingAgency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImplementingAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = ['Sultan Kudarat State University'];

        foreach($collections  as $data){
            ImplementingAgency::create(['title'=> $data]);
        }
    }
}
