<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\StoreVisit::create([
            'user_id' => 1, // Assuming user with ID 1 exists
            'restaurant_name' => 'Jeddah 02',
            'mic' => 'Morning',
            'visit_date' => '2025-10-06',
            'purpose_of_visit' => ['Quality Audit', 'Training Audit'],
            'score' => 85,
            
            // Section A - good performance
            'oca_board_followed' => true,
            'oca_board_comments' => 'Well implemented',
            'staff_know_duty' => true,
            'staff_duty_comments' => '',
            'coaching_directing' => false,
            'coaching_comments' => 'Need more active coaching',
            
            // Section B - mixed performance
            'smile_greetings' => true,
            'smile_comments' => 'Excellent customer service',
            'suggestive_selling' => true,
            'suggestive_comments' => '',
            'offer_promotion' => false,
            'promotion_comments' => 'Staff not aware of current promotions',
            'thank_direction' => true,
            'thank_comments' => '',
            
            'general_comments' => 'Overall good performance with some areas for improvement',
            'status' => 'Submitted',
            'created_at' => now()->subDays(1)
        ]);

        \App\Models\StoreVisit::create([
            'user_id' => 1,
            'restaurant_name' => 'Riyadh Central',
            'mic' => 'Evening',
            'visit_date' => '2025-10-05',
            'purpose_of_visit' => ['Operational Assessment'],
            'score' => 92,
            
            // Section A - excellent performance
            'oca_board_followed' => true,
            'staff_know_duty' => true,
            'coaching_directing' => true,
            
            // Section B - excellent performance
            'smile_greetings' => true,
            'suggestive_selling' => true,
            'offer_promotion' => true,
            'thank_direction' => true,
            
            'general_comments' => 'Excellent performance across all areas',
            'status' => 'Reviewed',
            'created_at' => now()->subDays(2)
        ]);

        \App\Models\StoreVisit::create([
            'user_id' => 1,
            'restaurant_name' => 'Dammam Plaza',
            'mic' => 'Morning',
            'visit_date' => '2025-10-04',
            'purpose_of_visit' => ['Quality Audit'],
            'score' => 68,
            
            // Section A - needs improvement
            'oca_board_followed' => false,
            'oca_board_comments' => 'OCA board not updated',
            'staff_know_duty' => true,
            'coaching_directing' => false,
            'coaching_comments' => 'No active coaching observed',
            
            // Section B - mixed
            'smile_greetings' => true,
            'suggestive_selling' => false,
            'suggestive_comments' => 'No upselling attempts',
            'offer_promotion' => false,
            'thank_direction' => true,
            
            'general_comments' => 'Several areas need immediate attention and action plan',
            'status' => 'Submitted',
            'created_at' => now()->subDays(3)
        ]);
    }
}
