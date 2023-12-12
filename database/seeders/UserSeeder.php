<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $admin_user = User::create([
            'first_name'=> 'Admin ',
            'last_name'=> 'User',
            'email'=> 'admin@gmail.com',
            'role'=> 'admin',
            'password'=> Hash::make('password'),
        ]);
    }
}
