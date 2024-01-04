<?php

namespace Database\Seeders;

use App\Models\DivisionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Direct Cost', 'Indirect Cost'];

        foreach ($categories as $category) {
            DivisionCategory::create([
                'title'=> $category
            ]);
        }
    }
}
