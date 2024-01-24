<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\DivisionCategory;
use Database\Seeders\YearSeeder;
use Database\Seeders\ProgramSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\QuarterSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\DivisionCategorySeeder;
use Database\Seeders\MonitoringAgencySeeder;
use Database\Seeders\ImplementingAgencySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            YearSeeder::class,
            DivisionSeeder::class,
            DivisionCategorySeeder::class,
            ProgramSeeder::class,
            ProjectSeeder::class,
            QuarterSeeder::class,
            MonitoringAgencySeeder::class,
            ImplementingAgencySeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
