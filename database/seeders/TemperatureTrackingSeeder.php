<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemperatureTrackingForm;
use App\Models\Restaurant;
use App\Models\User;

class TemperatureTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Restaurant::take(5)->get();
        $users = User::take(2)->get();

        if ($stores->isEmpty() || $users->isEmpty()) {
            echo "Need restaurants and users to seed temperature tracking forms.\n";
            return;
        }

        $timeSlots = ['11AM-2PM','3PM-6PM','7PM-10PM','11PM-2AM'];
        $primaryProducts = ['KIDS CHICKEN BURGER','REGULAR CHICKEN','SULTAN BEEF','DOUBLE G BEEF','CRISPY CHICKEN','GRILLED CHICKEN'];
        $sideProducts = ['JULIENNE','MAC & CHEESE','NACHOS'];
        $vegetables = ['LETTUCE','TOMATO','PICKLES','ONION'];

        for ($i = 0; $i < 10; $i++) {
            $store = $stores->random();
            $user = $users->random();
            $date = now()->subDays(rand(0, 30))->format('Y-m-d');
            
            // Create matrices with random temperatures
            $cookedProducts = [];
            foreach ($primaryProducts as $product) {
                $cookedProducts[$product] = [];
                foreach ($timeSlots as $slot) {
                    // 80% chance of valid temp, 20% chance of out-of-range
                    $cookedProducts[$product][$slot] = rand(1, 10) <= 8 ? rand(160, 165) : rand(150, 159);
                }
            }

            $holdingProducts = [];
            foreach ($primaryProducts as $product) {
                $holdingProducts[$product] = [];
                foreach ($timeSlots as $slot) {
                    $holdingProducts[$product][$slot] = rand(1, 10) <= 8 ? rand(140, 150) : rand(130, 139);
                }
            }

            $sideCooked = [];
            foreach ($sideProducts as $product) {
                $sideCooked[$product] = [];
                foreach ($timeSlots as $slot) {
                    $sideCooked[$product][$slot] = rand(1, 10) <= 8 ? rand(160, 165) : rand(150, 159);
                }
            }

            $vegetableTemps = [];
            foreach ($vegetables as $veg) {
                $vegetableTemps[$veg] = [];
                foreach ($timeSlots as $slot) {
                    $vegetableTemps[$veg][$slot] = rand(1, 10) <= 8 ? rand(36, 40) : rand(41, 45);
                }
            }

            $sanitizer = [];
            foreach ($timeSlots as $slot) {
                $sanitizer[$slot] = rand(1, 10) <= 8 ? rand(150, 300) : rand(100, 149);
            }

            $receivingProducts = [];
            foreach ($primaryProducts as $product) {
                $receivingProducts[$product] = [
                    'received' => $date,
                    'production' => now()->subDays(rand(1, 3))->format('Y-m-d'),
                    'expiry' => now()->addDays(rand(7, 30))->format('Y-m-d'),
                    'yn' => rand(1, 10) <= 8 ? 'Y' : 'N'
                ];
            }

            TemperatureTrackingForm::create([
                'store_id' => $store->id,
                'date' => $date,
                'shift' => rand(1, 2) == 1 ? 'morning' : 'evening',
                'mic_morning' => 'MIC Morning ' . $i,
                'mic_evening' => 'MIC Evening ' . $i,
                'cooked_products' => $cookedProducts,
                'holding_products' => $holdingProducts,
                'side_cooked' => $sideCooked,
                'vegetables' => $vegetableTemps,
                'sanitizer' => $sanitizer,
                'receiving_products' => $receivingProducts,
                'corrective_action_upper' => rand(1, 3) == 1 ? 'Reheated products to proper temperature' : '',
                'corrective_action_lower' => rand(1, 3) == 1 ? 'Adjusted sanitizer solution' : '',
                'created_by' => $user->id,
                'status' => ['Draft', 'Submitted', 'Reviewed'][rand(0, 2)]
            ]);
        }

        echo "Created 10 temperature tracking forms with test data.\n";
    }
}
