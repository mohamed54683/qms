<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QscChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'date',
        'mod',
        'day',
        'time_option',
        
        // Exterior section
        'exterior_lights_open',
        'exterior_lights_open_comment',
        'exterior_logo_clean',
        'exterior_logo_clean_comment',
        'exterior_parking_clean',
        'exterior_parking_clean_comment',
        'exterior_no_graffiti',
        'exterior_no_graffiti_comment',
        
        // Doors and Glass section
        'doors_glass_clean',
        'doors_glass_clean_comment',
        'doors_door_handle',
        'doors_door_handle_comment',
        'doors_entrance_clean',
        'doors_entrance_clean_comment',
        
        // Frontline section
        'frontline_areas_organized',
        'frontline_areas_organized_comment',
        'frontline_customers_greeted',
        'frontline_customers_greeted_comment',
        'frontline_menu_available',
        'frontline_menu_available_comment',
        'frontline_seven_steps',
        'frontline_seven_steps_comment',
        'frontline_tables_clean',
        'frontline_tables_clean_comment',
        'frontline_high_chairs',
        'frontline_high_chairs_comment',
        'frontline_no_damaged_tables',
        'frontline_no_damaged_tables_comment',
        
        // Restroom section
        'restroom_no_full_trash',
        'restroom_no_full_trash_comment',
        'restroom_soap_available',
        'restroom_soap_available_comment',
        'restroom_hand_dryer',
        'restroom_hand_dryer_comment',
        
        // Holding Heating section
        'holding_product_temp',
        'holding_product_temp_comment',
        'holding_product_age',
        'holding_product_age_comment',
        'holding_check_product',
        'holding_check_product_comment',
        'holding_products_fresh',
        'holding_products_fresh_comment',
        'holding_internal_temp',
        'holding_internal_temp_comment',
        'holding_shortening_level',
        'holding_shortening_level_comment',
        'holding_check_shortening',
        'holding_check_shortening_comment',
        'holding_fryer_maintenance',
        'holding_fryer_maintenance_comment',
        'holding_use_tray',
        'holding_use_tray_comment',
        'holding_fry_basket',
        'holding_fry_basket_comment',
        'holding_fries_salted',
        'holding_fries_salted_comment',
        'holding_fries_cooking',
        'holding_fries_cooking_comment',
        'holding_buns_quality',
        'holding_buns_quality_comment',
        'holding_teflon_sheet',
        'holding_teflon_sheet_comment',
        'holding_bread_standard',
        'holding_bread_standard_comment',
        
        // Thawing section
        'thawing_day_labels',
        'thawing_day_labels_comment',
        'thawing_no_tampering',
        'thawing_no_tampering_comment',
        'thawing_temp_range',
        'thawing_temp_range_comment',
        'thawing_no_overstock',
        'thawing_no_overstock_comment',
        'thawing_utensils_clean',
        'thawing_utensils_clean_comment',
        'thawing_sink_setup',
        'thawing_sink_setup_comment',
        'thawing_portion_standards',
        'thawing_portion_standards_comment',
        'thawing_sultan_sauce',
        'thawing_sultan_sauce_comment',
        'thawing_vegetables_date',
        'thawing_vegetables_date_comment',
        'thawing_follow_fifo',
        'thawing_follow_fifo_comment',
        
        // Burger Assembly section
        'burger_standard_setup',
        'burger_standard_setup_comment',
        'burger_sauce_spiral',
        'burger_sauce_spiral_comment',
        'burger_ingredients_order',
        'burger_ingredients_order_comment',
        
        'manager_signature',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate compliance score based on Yes/No responses
    public function getComplianceScoreAttribute()
    {
        $totalQuestions = 0;
        $yesAnswers = 0;
        
        // Count all the Yes/No fields
        $fields = [
            'exterior_lights_open', 'exterior_logo_clean', 'exterior_parking_clean', 'exterior_no_graffiti',
            'doors_glass_clean', 'doors_door_handle', 'doors_entrance_clean',
            'frontline_areas_organized', 'frontline_customers_greeted', 'frontline_menu_available', 
            'frontline_seven_steps', 'frontline_tables_clean', 'frontline_high_chairs', 'frontline_no_damaged_tables',
            'restroom_no_full_trash', 'restroom_soap_available', 'restroom_hand_dryer',
            // Add all other fields...
        ];
        
        foreach ($fields as $field) {
            if (!is_null($this->$field)) {
                $totalQuestions++;
                if ($this->$field === 'yes') {
                    $yesAnswers++;
                }
            }
        }
        
        return $totalQuestions > 0 ? round(($yesAnswers / $totalQuestions) * 100, 1) : 0;
    }
}