<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['title' => 'Personal Services', 'abbreviation' => 'PS'],
            ['title' => 'Maintenance and Other Operating Expenses', 'abbreviation' => 'MOOE'],
            ['title' => 'Equipment Outlay', 'abbreviation' => 'EO'],
            // Add more divisions as needed.
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
