<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            'admin' => 'System Administrator',
            'area_manager' => 'Area Manager', 
            'sm' => 'Store Manager',
            'rm' => 'Regional Manager',
            'om' => 'Operations Manager',
            'coo' => 'Chief Operating Officer',
            'training' => 'Training Manager'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name]);
        }

        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'it@ghidas.com',
        ], [
            'name' => 'QMS Administrator',
            'password' => Hash::make('Sdb@123456'),
            'email_verified_at' => now(),
        ]);

        // Assign admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        echo "✅ Admin user created successfully!\n";
        echo "📧 Email: it@ghidas.com\n";
        echo "🔐 Password: Sdb@123456\n";
        echo "🎯 Role: admin\n";
    }
}
