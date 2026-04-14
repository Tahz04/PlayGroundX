<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@playgroundx.vn',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]);
    }
}
