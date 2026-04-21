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
        
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@playgroundx.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('12345678'),
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );
    }
}
