<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qsc_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // General Information
            $table->string('store_name');
            $table->date('date');
            $table->string('mod'); // Manager on Duty
            $table->string('day')->nullable();
            $table->enum('time_option', ['12PM', '5PM', '8PM', '11PM']);
            
            // Exterior section
            $table->enum('exterior_lights_open', ['yes', 'no'])->nullable();
            $table->text('exterior_lights_open_comment')->nullable();
            $table->enum('exterior_logo_clean', ['yes', 'no'])->nullable();
            $table->text('exterior_logo_clean_comment')->nullable();
            $table->enum('exterior_parking_clean', ['yes', 'no'])->nullable();
            $table->text('exterior_parking_clean_comment')->nullable();
            $table->enum('exterior_no_graffiti', ['yes', 'no'])->nullable();
            $table->text('exterior_no_graffiti_comment')->nullable();
            
            // Doors and Glass section
            $table->enum('doors_glass_clean', ['yes', 'no'])->nullable();
            $table->text('doors_glass_clean_comment')->nullable();
            $table->enum('doors_door_handle', ['yes', 'no'])->nullable();
            $table->text('doors_door_handle_comment')->nullable();
            $table->enum('doors_entrance_clean', ['yes', 'no'])->nullable();
            $table->text('doors_entrance_clean_comment')->nullable();
            
            // Frontline section
            $table->enum('frontline_areas_organized', ['yes', 'no'])->nullable();
            $table->text('frontline_areas_organized_comment')->nullable();
            $table->enum('frontline_customers_greeted', ['yes', 'no'])->nullable();
            $table->text('frontline_customers_greeted_comment')->nullable();
            $table->enum('frontline_menu_available', ['yes', 'no'])->nullable();
            $table->text('frontline_menu_available_comment')->nullable();
            $table->enum('frontline_seven_steps', ['yes', 'no'])->nullable();
            $table->text('frontline_seven_steps_comment')->nullable();
            $table->enum('frontline_tables_clean', ['yes', 'no'])->nullable();
            $table->text('frontline_tables_clean_comment')->nullable();
            $table->enum('frontline_high_chairs', ['yes', 'no'])->nullable();
            $table->text('frontline_high_chairs_comment')->nullable();
            $table->enum('frontline_no_damaged_tables', ['yes', 'no'])->nullable();
            $table->text('frontline_no_damaged_tables_comment')->nullable();
            
            // Restroom section
            $table->enum('restroom_no_full_trash', ['yes', 'no'])->nullable();
            $table->text('restroom_no_full_trash_comment')->nullable();
            $table->enum('restroom_soap_available', ['yes', 'no'])->nullable();
            $table->text('restroom_soap_available_comment')->nullable();
            $table->enum('restroom_hand_dryer', ['yes', 'no'])->nullable();
            $table->text('restroom_hand_dryer_comment')->nullable();
            
            // Holding Heating section
            $table->enum('holding_product_temp', ['yes', 'no'])->nullable();
            $table->text('holding_product_temp_comment')->nullable();
            $table->enum('holding_product_age', ['yes', 'no'])->nullable();
            $table->text('holding_product_age_comment')->nullable();
            $table->enum('holding_check_product', ['yes', 'no'])->nullable();
            $table->text('holding_check_product_comment')->nullable();
            $table->enum('holding_products_fresh', ['yes', 'no'])->nullable();
            $table->text('holding_products_fresh_comment')->nullable();
            $table->enum('holding_internal_temp', ['yes', 'no'])->nullable();
            $table->text('holding_internal_temp_comment')->nullable();
            $table->enum('holding_shortening_level', ['yes', 'no'])->nullable();
            $table->text('holding_shortening_level_comment')->nullable();
            $table->enum('holding_check_shortening', ['yes', 'no'])->nullable();
            $table->text('holding_check_shortening_comment')->nullable();
            $table->enum('holding_fryer_maintenance', ['yes', 'no'])->nullable();
            $table->text('holding_fryer_maintenance_comment')->nullable();
            $table->enum('holding_use_tray', ['yes', 'no'])->nullable();
            $table->text('holding_use_tray_comment')->nullable();
            $table->enum('holding_fry_basket', ['yes', 'no'])->nullable();
            $table->text('holding_fry_basket_comment')->nullable();
            $table->enum('holding_fries_salted', ['yes', 'no'])->nullable();
            $table->text('holding_fries_salted_comment')->nullable();
            $table->enum('holding_fries_cooking', ['yes', 'no'])->nullable();
            $table->text('holding_fries_cooking_comment')->nullable();
            $table->enum('holding_buns_quality', ['yes', 'no'])->nullable();
            $table->text('holding_buns_quality_comment')->nullable();
            $table->enum('holding_teflon_sheet', ['yes', 'no'])->nullable();
            $table->text('holding_teflon_sheet_comment')->nullable();
            $table->enum('holding_bread_standard', ['yes', 'no'])->nullable();
            $table->text('holding_bread_standard_comment')->nullable();
            
            // Thawing section
            $table->enum('thawing_day_labels', ['yes', 'no'])->nullable();
            $table->text('thawing_day_labels_comment')->nullable();
            $table->enum('thawing_no_tampering', ['yes', 'no'])->nullable();
            $table->text('thawing_no_tampering_comment')->nullable();
            $table->enum('thawing_temp_range', ['yes', 'no'])->nullable();
            $table->text('thawing_temp_range_comment')->nullable();
            $table->enum('thawing_no_overstock', ['yes', 'no'])->nullable();
            $table->text('thawing_no_overstock_comment')->nullable();
            $table->enum('thawing_utensils_clean', ['yes', 'no'])->nullable();
            $table->text('thawing_utensils_clean_comment')->nullable();
            $table->enum('thawing_sink_setup', ['yes', 'no'])->nullable();
            $table->text('thawing_sink_setup_comment')->nullable();
            $table->enum('thawing_portion_standards', ['yes', 'no'])->nullable();
            $table->text('thawing_portion_standards_comment')->nullable();
            $table->enum('thawing_sultan_sauce', ['yes', 'no'])->nullable();
            $table->text('thawing_sultan_sauce_comment')->nullable();
            $table->enum('thawing_vegetables_date', ['yes', 'no'])->nullable();
            $table->text('thawing_vegetables_date_comment')->nullable();
            $table->enum('thawing_follow_fifo', ['yes', 'no'])->nullable();
            $table->text('thawing_follow_fifo_comment')->nullable();
            
            // Burger Assembly section
            $table->enum('burger_standard_setup', ['yes', 'no'])->nullable();
            $table->text('burger_standard_setup_comment')->nullable();
            $table->enum('burger_sauce_spiral', ['yes', 'no'])->nullable();
            $table->text('burger_sauce_spiral_comment')->nullable();
            $table->enum('burger_ingredients_order', ['yes', 'no'])->nullable();
            $table->text('burger_ingredients_order_comment')->nullable();
            
            // Manager and Status
            $table->string('manager_signature');
            $table->enum('status', ['Draft', 'Submitted', 'Reviewed', 'Approved'])->default('Submitted');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['store_name', 'date']);
            $table->index(['time_option', 'date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qsc_checklists');
    }
};
