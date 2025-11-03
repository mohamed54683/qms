<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateRestaurantStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all restaurants to be active by default
        Restaurant::whereNull('is_active')->update(['is_active' => true]);
        
        // Make some restaurants inactive for demo purposes (about 10%)
        $inactiveCount = max(1, floor(Restaurant::count() * 0.1));
        Restaurant::inRandomOrder()
            ->take($inactiveCount)
            ->update(['is_active' => false]);
            
        echo "✅ Restaurant statuses updated!\n";
        echo "📊 Active: " . Restaurant::where('is_active', true)->count() . "\n";
        echo "📊 Inactive: " . Restaurant::where('is_active', false)->count() . "\n";
    }
}
