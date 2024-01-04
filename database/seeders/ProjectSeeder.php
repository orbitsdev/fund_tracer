<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        Project::create([
            'user_id' => null,
            'program_id' => Program::inRandomOrder()->first()->id, // U// Use another existing program_id
            'title' => 'Development of Regulatory Policy Instruments towards Certification of Halal-Compliant Goat Farms',
            'implementing_agency' => 'Sultan Kudarat State University',
            'monitoring_agency' => 'DOST-PCAARRD',
            'project_leader' => 'Asst. Prof Cyril John A. Domingo2',
            'allocated_fund' => 8000.00,
            'total_usage' => 3000.00,
            'start_date' => '2023-03-01',
            'end_date' => '2026-04-01',
            'status' => 'Completed',
        ]);
    }
}
