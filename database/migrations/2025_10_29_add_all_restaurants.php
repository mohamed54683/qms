<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $restaurants = [
            // Jeddah Region
            ['name' => 'Basateen', 'code' => 'JED-BAS', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Tahlia', 'code' => 'JED-TAH', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'SDB – Al-Naseem', 'code' => 'JED-NAS', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Hail', 'code' => 'JED-HAI', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Obhur', 'code' => 'JED-OBH', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Al-Hamdania', 'code' => 'JED-HAM', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'New Al-Samer 2', 'code' => 'JED-SAM', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Al-Rehab', 'code' => 'JED-REH', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Faisaliya', 'code' => 'JED-FAI', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Hira', 'code' => 'JED-HIR', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Al-Marwa', 'code' => 'JED-MAR', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Iceland', 'code' => 'JED-ICE', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Al-Rowda', 'code' => 'JED-ROW', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Al-Mohammadiah', 'code' => 'JED-MOH', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Khulais', 'code' => 'JED-KHU', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Wazzeriyah', 'code' => 'JED-WAZ', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Ameer Fawaz', 'code' => 'JED-FAW', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Rabigh', 'code' => 'JED-RAB', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Al-Sanabel', 'code' => 'JED-SAN', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'TNDR Hamdania', 'code' => 'JED-TNH', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'TNDR Rawdah', 'code' => 'JED-TNR', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'TNDR Rawdah 2', 'code' => 'JED-TNR2', 'location' => 'Jeddah', 'is_active' => 1],
            ['name' => 'Pizza Dealer', 'code' => 'JED-PIZ', 'location' => 'Jeddah', 'is_active' => 1],
            
            // Madina Region
            ['name' => 'Al Aziziyah', 'code' => 'MED-AZI', 'location' => 'Madina', 'is_active' => 1],
            ['name' => 'Al Hijra', 'code' => 'MED-HIJ', 'location' => 'Madina', 'is_active' => 1],
            ['name' => 'Umluj', 'code' => 'MED-UML', 'location' => 'Madina', 'is_active' => 1],
            ['name' => 'New Yanbu 2 (هيئة المدن)', 'code' => 'MED-YAN2', 'location' => 'Madina', 'is_active' => 1],
            ['name' => 'Yanbu', 'code' => 'MED-YAN', 'location' => 'Madina', 'is_active' => 1],
            ['name' => 'Sari Medina', 'code' => 'MED-SAR', 'location' => 'Madina', 'is_active' => 1],
            ['name' => 'TNDR Sultana', 'code' => 'MED-TNS', 'location' => 'Madina', 'is_active' => 1],
            
            // Makkah Region
            ['name' => 'Batha Quresh', 'code' => 'MAK-BAT', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Taif Shehar', 'code' => 'MAK-TSH', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Zaidi (Sassco)', 'code' => 'MAK-ZAI', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Al Shoukiyyah', 'code' => 'MAK-SHO', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Al Shraia', 'code' => 'MAK-SHR', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Al Hawiyyah (Taif)', 'code' => 'MAK-HAW', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Al Hada', 'code' => 'MAK-HAD', 'location' => 'Makkah', 'is_active' => 1],
            ['name' => 'Al Hait Makkah', 'code' => 'MAK-HAI', 'location' => 'Makkah', 'is_active' => 1],
            
            // Eastern Region
            ['name' => 'Dammam', 'code' => 'EST-DAM', 'location' => 'Eastern Province', 'is_active' => 1],
            ['name' => 'Al Khobar', 'code' => 'EST-KHO', 'location' => 'Eastern Province', 'is_active' => 1],
            
            // Southern Region
            ['name' => 'Almansk (Abha)', 'code' => 'SOU-MAN', 'location' => 'Abha', 'is_active' => 1],
            ['name' => 'Alraqy', 'code' => 'SOU-RAQ', 'location' => 'South', 'is_active' => 1],
            ['name' => 'Khamis Mushet', 'code' => 'SOU-KHA', 'location' => 'South', 'is_active' => 1],
            ['name' => 'TNDR Abha', 'code' => 'SOU-TNA', 'location' => 'Abha', 'is_active' => 1],
            
            // Riyadh Region
            ['name' => 'Al Rawabi Plaza', 'code' => 'RYD-RAW', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Al-Aziziya Riyad', 'code' => 'RYD-AZI', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Albadia', 'code' => 'RYD-BAD', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Al-Khaleej', 'code' => 'RYD-KHA', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Hassan Bin Thabit', 'code' => 'RYD-HAS', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Naseem – (Khalid Bin Waleed)', 'code' => 'RYD-NAS', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Al Nakheel', 'code' => 'RYD-NAK', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Al Nozaha', 'code' => 'RYD-NOZ', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Al Yasmeen Riyad', 'code' => 'RYD-YAS', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Dahiat Laban', 'code' => 'RYD-LAB', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'KHARG', 'code' => 'RYD-KHG', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'King Salman', 'code' => 'RYD-SAL', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Qurtuba', 'code' => 'RYD-QUR', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Thumama', 'code' => 'RYD-THU', 'location' => 'Riyadh', 'is_active' => 1],
            ['name' => 'Qasim', 'code' => 'RYD-QAS', 'location' => 'Qasim', 'is_active' => 1],
            ['name' => 'TNDR Qasim', 'code' => 'RYD-TNQ', 'location' => 'Qasim', 'is_active' => 1],
            ['name' => 'TNDR Hitin', 'code' => 'RYD-TNH', 'location' => 'Riyadh', 'is_active' => 1],
        ];

        foreach ($restaurants as $restaurant) {
            // Check if restaurant already exists by name
            $exists = DB::table('restaurants')->where('name', $restaurant['name'])->exists();
            
            if (!$exists) {
                DB::table('restaurants')->insert(array_merge($restaurant, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all restaurant names to delete
        $restaurantNames = [
            'Basateen', 'Tahlia', 'SDB – Al-Naseem', 'Hail', 'Obhur', 'Al-Hamdania', 
            'New Al-Samer 2', 'Al-Rehab', 'Faisaliya', 'Hira', 'Al-Marwa', 'Iceland',
            'Al-Rowda', 'Al-Mohammadiah', 'Khulais', 'Wazzeriyah', 'Ameer Fawaz', 'Rabigh',
            'Al-Sanabel', 'TNDR Hamdania', 'TNDR Rawdah', 'TNDR Rawdah 2', 'Pizza Dealer',
            'Al Aziziyah', 'Al Hijra', 'Umluj', 'New Yanbu 2 (هيئة المدن)', 'Yanbu', 
            'Sari Medina', 'TNDR Sultana', 'Batha Quresh', 'Taif Shehar', 'Zaidi (Sassco)',
            'Al Shoukiyyah', 'Al Shraia', 'Al Hawiyyah (Taif)', 'Al Hada', 'Al Hait Makkah',
            'Dammam', 'Al Khobar', 'Almansk (Abha)', 'Alraqy', 'Khamis Mushet', 'TNDR Abha',
            'Al Rawabi Plaza', 'Al-Aziziya Riyad', 'Albadia', 'Al-Khaleej', 'Hassan Bin Thabit',
            'Naseem – (Khalid Bin Waleed)', 'Al Nakheel', 'Al Nozaha', 'Al Yasmeen Riyad',
            'Dahiat Laban', 'KHARG', 'King Salman', 'Qurtuba', 'Thumama', 'Qasim',
            'TNDR Qasim', 'TNDR Hitin'
        ];

        DB::table('restaurants')->whereIn('name', $restaurantNames)->delete();
    }
};
