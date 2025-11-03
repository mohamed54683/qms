<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create all standard roles (7 roles)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $areaManagerRole = Role::firstOrCreate(['name' => 'area_manager']);
        $smRole = Role::firstOrCreate(['name' => 'sm']);
        $rmRole = Role::firstOrCreate(['name' => 'rm']);
        $omRole = Role::firstOrCreate(['name' => 'om']);
        $cooRole = Role::firstOrCreate(['name' => 'coo']);
        $trainingRole = Role::firstOrCreate(['name' => 'training']);
        
        // Assign admin role to first user
        $user = User::first();
        if ($user && !$user->hasRole('admin')) {
            $user->assignRole('admin');
            echo "Assigned admin role to user: {$user->name}\n";
        }
    }
}