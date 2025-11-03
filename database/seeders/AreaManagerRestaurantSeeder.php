<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurant;
use Spatie\Permission\Models\Role;

class AreaManagerRestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get Area Manager role
        $areaManagerRole = Role::firstOrCreate(['name' => 'Area Manager']);
        
        // Create a test area manager user if one doesn't exist
        $areaManager = User::where('email', 'areamanager@test.com')->first();
        
        if (!$areaManager) {
            $areaManager = User::create([
                'name' => 'Test Area Manager',
                'email' => 'areamanager@test.com',
                'password' => bcrypt('password'), // Remember to change this in production
            ]);
        }
        
        // Assign Area Manager role to the user
        $areaManager->assignRole($areaManagerRole);
        
        // Get some restaurants to assign (take first 3 restaurants)
        $restaurants = Restaurant::take(3)->get();
        
        if ($restaurants->count() > 0) {
            // Assign restaurants to area manager using the pivot table
            $areaManager->restaurants()->sync($restaurants->pluck('id')->toArray());
            
            $this->command->info('Area Manager assigned to ' . $restaurants->count() . ' restaurants:');
            foreach ($restaurants as $restaurant) {
                $this->command->info('- ' . $restaurant->name);
            }
        } else {
            $this->command->info('No restaurants found to assign.');
        }
        
        $this->command->info('Area Manager test user created: areamanager@test.com / password');
    }
}
