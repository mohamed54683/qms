<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            'Abha',
            'Abha-TNDR',
            'Albadia',
            'Al-Qassim',
            'Al-Sultan-T',
            'Ameer Fawaz',
            'Awali',
            'Aziziyah-Madina',
            'Aziziyah-Riyad',
            'Basateen',
            'Batha Quresh',
            'Dahiat Laban',
            'DAMMAM',
            'DANTE CARLOS DAILISAN',
            'DARYL JAMES CARINO  ANCHETA',
            'Faisaliya',
            'Hail',
            'Hamdaniya',
            'Hamdaniya-TNDR',
            'Hani Hussein Ahmed Naseem',
            'Hassan Bin Thabit',
            'Hijra',
            'Hira',
            'Iceland',
            'Imam Saud -TNDR',
            'Ithar Abdulhadi Ishaq Hawsawi',
            'KAZI SHELIM  AHMMED',
            'Khaleej',
            'Khamis Mushet',
            'KHARG',
            'Khobar',
            'Khulias',
            'KING FAHAD MED',
            'King Salman',
            'Marwa',
            'Marwa-TNDR',
            'Mohamadiah',
            'Mohamed Ibrahim - Saeed',
            'Nakheel',
            'Nassem',
            'Nassem-TNDR',
            'Nozha',
            'Obhur',
            'Qurtuba',
            'Rabigh',
            'Raqi',
            'Rawabi Plaza',
            'Rayan Talal Yousef Ahmed',
            'Rehab',
            'Remal - TNDR',
            'Rowdahr',
            'Rowdah-TNDR',
            'Samer',
            'Sanabel',
            'Sari',
            'Shoukiya',
            'Shraia',
            'Sultana -TNDR',
            'Tahlia',
            'Taif-Hawiyah',
            'Taif-Shehar',
            'Thumama',
            'Umluj',
            'Waziriyah',
            'Yanbu 1',
            'Yanbu 2',
            'Yasmen',
            'Zaidi (Sasco)'
        ];

        // Clear existing restaurants first
        Restaurant::query()->delete();

        foreach ($restaurants as $index => $restaurantName) {
            Restaurant::create([
                'name' => $restaurantName,
                'code' => 'QMS' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'location' => $this->generateLocation($restaurantName),
                'phone' => $this->generatePhone(),
                'email' => $this->generateEmail($restaurantName),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ " . count($restaurants) . " restaurants created successfully!\n";
        echo "📍 Restaurant locations loaded from Saudi Arabia\n";
        echo "🏢 All QMS restaurant branches are now available\n";
    }

    private function generateLocation($restaurantName): string
    {
        $cities = [
            'Riyadh', 'Jeddah', 'Mecca', 'Medina', 'Dammam', 'Khobar', 
            'Dhahran', 'Taif', 'Yanbu', 'Abha', 'Hail', 'Tabuk'
        ];
        
        $districts = [
            'Al-Malaz', 'Al-Olaya', 'Al-Sulaimaniyah', 'King Fahd District',
            'Al-Aziziyah', 'Al-Rawdah', 'Al-Nakheel', 'Al-Hamra'
        ];

        // Use restaurant name for location if it contains city name
        if (str_contains($restaurantName, 'Riyadh') || str_contains($restaurantName, 'Aziziyah-Riyad')) {
            return "Riyadh, Saudi Arabia";
        } elseif (str_contains($restaurantName, 'Medina') || str_contains($restaurantName, 'Aziziyah-Madina')) {
            return "Medina, Saudi Arabia";
        } elseif (str_contains($restaurantName, 'Dammam') || str_contains($restaurantName, 'DAMMAM')) {
            return "Dammam, Saudi Arabia";
        } elseif (str_contains($restaurantName, 'Yanbu')) {
            return "Yanbu, Saudi Arabia";
        } elseif (str_contains($restaurantName, 'Taif')) {
            return "Taif, Saudi Arabia";
        } elseif (str_contains($restaurantName, 'Abha')) {
            return "Abha, Saudi Arabia";
        } else {
            $city = $cities[array_rand($cities)];
            $district = $districts[array_rand($districts)];
            return "{$district}, {$city}, Saudi Arabia";
        }
    }

    private function generatePhone(): string
    {
        return '+966 ' . rand(11, 59) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999);
    }

    private function generateEmail($restaurantName): string
    {
        $cleanName = strtolower(str_replace([' ', '-', '(', ')'], ['', '', '', ''], $restaurantName));
        return $cleanName . '@qms.sa';
    }
}
