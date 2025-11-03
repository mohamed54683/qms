<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddNewRestaurantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            ['code' => 'ABH', 'name' => 'Abha', 'is_active' => true],
            ['code' => 'ABH-T', 'name' => 'Abha-TNDR', 'is_active' => true],
            ['code' => 'ALB', 'name' => 'Albadia', 'is_active' => true],
            ['code' => 'QAS', 'name' => 'Al-Qassim', 'is_active' => true],
            ['code' => 'AFZ', 'name' => 'Ameer Fawaz', 'is_active' => true],
            ['code' => 'AWL', 'name' => 'Awali', 'is_active' => true],
            ['code' => 'AZM', 'name' => 'Aziziyah-Madina', 'is_active' => true],
            ['code' => 'AZR', 'name' => 'Aziziyah-Riyad', 'is_active' => true],
            ['code' => 'BAS', 'name' => 'Basateen', 'is_active' => true],
            ['code' => 'BTQ', 'name' => 'Batha Quresh', 'is_active' => true],
            ['code' => 'DHL', 'name' => 'Dahiat Laban', 'is_active' => true],
            ['code' => 'DAM', 'name' => 'DAMMAM', 'is_active' => true],
            ['code' => 'FAS', 'name' => 'Faisaliya', 'is_active' => true],
            ['code' => 'HAL', 'name' => 'Hail', 'is_active' => true],
            ['code' => 'HAM', 'name' => 'Hamdaniya', 'is_active' => true],
            ['code' => 'HAM-T', 'name' => 'Hamdaniya-TNDR', 'is_active' => true],
            ['code' => 'HBT', 'name' => 'Hassan Bin Thabit', 'is_active' => true],
            ['code' => 'HJR', 'name' => 'Hijra', 'is_active' => true],
            ['code' => 'HIR', 'name' => 'Hira', 'is_active' => true],
            ['code' => 'ICE', 'name' => 'Iceland', 'is_active' => true],
            ['code' => 'IMS-T', 'name' => 'Imam Saud-TNDR', 'is_active' => true],
            ['code' => 'KHL', 'name' => 'Khaleej', 'is_active' => true],
            ['code' => 'KHM', 'name' => 'Khamis Mushet', 'is_active' => true],
            ['code' => 'KHG', 'name' => 'KHARG', 'is_active' => true],
            ['code' => 'KHB', 'name' => 'Khobar', 'is_active' => true],
            ['code' => 'KHU', 'name' => 'Khulias', 'is_active' => true],
            ['code' => 'KFM', 'name' => 'King Fahad Med', 'is_active' => true],
            ['code' => 'KSM', 'name' => 'King Salman', 'is_active' => true],
            ['code' => 'MAR', 'name' => 'Marwa', 'is_active' => true],
            ['code' => 'MAR-T', 'name' => 'Marwa-TNDR', 'is_active' => true],
            ['code' => 'MOH', 'name' => 'Mohamadiah', 'is_active' => true],
            ['code' => 'NAK', 'name' => 'Nakheel', 'is_active' => true],
            ['code' => 'NAS', 'name' => 'Nassem', 'is_active' => true],
            ['code' => 'NAS-T', 'name' => 'Nassem-TNDR', 'is_active' => true],
            ['code' => 'NOZ', 'name' => 'Nozha', 'is_active' => true],
            ['code' => 'OBH', 'name' => 'Obhur', 'is_active' => true],
            ['code' => 'QRT', 'name' => 'Qurtuba', 'is_active' => true],
            ['code' => 'RAB', 'name' => 'Rabigh', 'is_active' => true],
            ['code' => 'RAQ', 'name' => 'Raqi', 'is_active' => true],
            ['code' => 'RWP', 'name' => 'Rawabi Plaza', 'is_active' => true],
            ['code' => 'REH', 'name' => 'Rehab', 'is_active' => true],
            ['code' => 'ROW', 'name' => 'Rowdah', 'is_active' => true],
            ['code' => 'ROW-T', 'name' => 'Rowdah-TNDR', 'is_active' => true],
            ['code' => 'SAM', 'name' => 'Samer', 'is_active' => true],
            ['code' => 'SAN', 'name' => 'Sanabel', 'is_active' => true],
            ['code' => 'SAR', 'name' => 'Sari', 'is_active' => true],
            ['code' => 'SHK', 'name' => 'Shoukiya', 'is_active' => true],
            ['code' => 'SHR', 'name' => 'Shraia', 'is_active' => true],
            ['code' => 'SUL-T', 'name' => 'Sultana-TNDR', 'is_active' => true],
            ['code' => 'TAH', 'name' => 'Tahlia', 'is_active' => true],
            ['code' => 'TAF-S', 'name' => 'Taif-Shehar', 'is_active' => true],
            ['code' => 'TAF-H', 'name' => 'Taif-Hawiyah', 'is_active' => true],
            ['code' => 'THM', 'name' => 'Thumama', 'is_active' => true],
            ['code' => 'UML', 'name' => 'Umluj', 'is_active' => true],
            ['code' => 'WAZ', 'name' => 'Waziriyah', 'is_active' => true],
            ['code' => 'YAN1', 'name' => 'Yanbu 1', 'is_active' => true],
            ['code' => 'YAN2', 'name' => 'Yanbu 2', 'is_active' => true],
            ['code' => 'YAS', 'name' => 'Yasmen', 'is_active' => true],
            ['code' => 'ZAI', 'name' => 'Zaidi (Sasco)', 'is_active' => true],
            ['code' => 'ALS-T', 'name' => 'Al-Sultan-T', 'is_active' => true],
            ['code' => 'JED-F', 'name' => 'Jeddah Factory', 'is_active' => true],
            ['code' => 'RIY-F', 'name' => 'Riyadh Factory', 'is_active' => true],
            ['code' => 'JED-W', 'name' => 'Jed-Warehouse Dep', 'is_active' => true],
            ['code' => 'RIY-W', 'name' => 'Riy-Warehouse Dep', 'is_active' => true],
            ['code' => 'NAH', 'name' => 'Nahdah', 'is_active' => true],
            ['code' => 'HO', 'name' => 'H.O Store', 'is_active' => true],
            ['code' => 'IT-W', 'name' => 'IT Warehouse', 'is_active' => true],
            ['code' => 'MNT-W', 'name' => 'Maintenance Warehouse', 'is_active' => true],
            ['code' => 'REM-T', 'name' => 'Remal-TNDR', 'is_active' => true],
            ['code' => 'AST', 'name' => 'Assets', 'is_active' => true],
            ['code' => 'TAB-T', 'name' => 'Tabuk-TNDR', 'is_active' => true],
            ['code' => 'THM-F', 'name' => 'Thumama Factory', 'is_active' => true],
        ];

        $now = Carbon::now();

        foreach ($restaurants as $restaurant) {
            // Check if restaurant already exists
            $exists = DB::table('restaurants')
                ->where('name', $restaurant['name'])
                ->orWhere('code', $restaurant['code'])
                ->exists();

            if (!$exists) {
                DB::table('restaurants')->insert([
                    'code' => $restaurant['code'],
                    'name' => $restaurant['name'],
                    'is_active' => $restaurant['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                
                $this->command->info("✓ Added: {$restaurant['name']} ({$restaurant['code']})");
            } else {
                $this->command->warn("⚠ Skipped (already exists): {$restaurant['name']} ({$restaurant['code']})");
            }
        }

        $this->command->info("\n✅ Restaurant seeding completed!");
        $this->command->info("Total restaurants processed: " . count($restaurants));
    }
}
