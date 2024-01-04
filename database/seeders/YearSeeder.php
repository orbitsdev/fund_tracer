<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = ['Year 1', 'Year 2', 'Year 3', 'Year 4'];

        foreach ($years as $year) {
            Year::create(['title' => $year]);
        }
    
    }
}
