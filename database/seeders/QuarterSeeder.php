<?php

namespace Database\Seeders;

use App\Models\Quarter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

        foreach($quarters  as $quarter){
            Quarter::create(['title'=> $quarter]);
        }
    }
}
