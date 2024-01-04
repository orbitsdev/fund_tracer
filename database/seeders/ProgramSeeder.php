<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::create([
            'title' => 'NICER PROGRAM ON HALAL GOAT - Phase 2',
            'program_leader' => 'Asst. Prof Cyril John A. Domingo',
            'start_date' => '2024-01-01',
            'end_date' => '2024-02-01',
            'total_budget' => 2000000.00,
            'total_usage' => 0,
            'status' => 'Approved',
        ]);

       
    }
}
