<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'customer'], ['description' => 'Regular customer']);
        Role::firstOrCreate(['name' => 'owner'], ['description' => 'Arena owner']);
        Role::firstOrCreate(['name' => 'admin'], ['description' => 'System administrator']);
    }
}
