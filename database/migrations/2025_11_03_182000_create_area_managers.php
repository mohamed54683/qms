<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Restaurant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Area Manager role if it doesn't exist
        $areaManagerRole = Role::firstOrCreate([
            'name' => 'Area Manager',
            'guard_name' => 'web'
        ]);
        
        echo "Created Area Manager role.\n";
        
        // Create test area manager users
        $areaManagers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@qms.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@qms.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@qms.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        ];
        
        foreach ($areaManagers as $managerData) {
            $manager = User::firstOrCreate(
                ['email' => $managerData['email']], 
                $managerData
            );
            
            // Assign Area Manager role
            $manager->assignRole($areaManagerRole);
            
            // Assign some restaurants to this area manager
            $restaurants = Restaurant::inRandomOrder()->take(3)->get();
            foreach ($restaurants as $restaurant) {
                $manager->restaurants()->syncWithoutDetaching([$restaurant->id]);
            }
            
            echo "Created area manager: {$manager->name}\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove area manager users
        User::whereHas('roles', function($q) {
            $q->where('name', 'Area Manager');
        })->delete();
        
        // Remove Area Manager role
        Role::where('name', 'Area Manager')->delete();
    }
};