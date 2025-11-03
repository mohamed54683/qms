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
        Schema::create('store_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic visit information
            $table->string('restaurant_name');
            $table->enum('mic', ['Morning', 'Evening']);
            $table->date('visit_date');
            $table->integer('score')->nullable();
            $table->json('purpose_of_visit'); // Array of purposes: Quality Audit, Operational Assessment, etc.
            
            // Section A: Customer / QSC (3 items)
            $table->boolean('oca_board_followed')->nullable();
            $table->text('oca_board_comments')->nullable();
            $table->boolean('staff_know_duty')->nullable();
            $table->text('staff_duty_comments')->nullable();
            $table->boolean('coaching_directing')->nullable();
            $table->text('coaching_comments')->nullable();
            
            // Section B: Cashier (4 items)
            $table->boolean('smile_greetings')->nullable();
            $table->text('smile_comments')->nullable();
            $table->boolean('suggestive_selling')->nullable();
            $table->text('suggestive_comments')->nullable();
            $table->boolean('offer_promotion')->nullable();
            $table->text('promotion_comments')->nullable();
            $table->boolean('thank_direction')->nullable();
            $table->text('thank_comments')->nullable();
            
            // Section C: Service Standards (8 items)
            $table->boolean('team_work_hustle')->nullable();
            $table->text('teamwork_comments')->nullable();
            $table->boolean('order_accuracy')->nullable();
            $table->text('accuracy_comments')->nullable();
            $table->boolean('service_time')->nullable();
            $table->text('service_comments')->nullable();
            $table->boolean('dine_in')->nullable();
            $table->text('dine_comments')->nullable();
            $table->boolean('take_out')->nullable();
            $table->text('takeout_comments')->nullable();
            $table->boolean('family')->nullable();
            $table->text('family_comments')->nullable();
            $table->boolean('delivery')->nullable();
            $table->text('delivery_comments')->nullable();
            $table->boolean('drive_thru')->nullable();
            $table->text('drive_comments')->nullable();
            
            // Section D: Financials (5 items)
            $table->boolean('weekly_schedule')->nullable();
            $table->text('schedule_comments')->nullable();
            $table->boolean('mod_financial_goal')->nullable();
            $table->text('financial_comments')->nullable();
            $table->boolean('sales_objectives')->nullable();
            $table->text('sales_comments')->nullable();
            $table->boolean('cash_policies')->nullable();
            $table->text('cash_comments')->nullable();
            $table->boolean('daily_waste')->nullable();
            $table->text('waste_comments')->nullable();
            
            // Section E: Quality / Pathing (9 items)
            $table->boolean('ttf_followed')->nullable();
            $table->text('ttf_comments')->nullable();
            $table->boolean('sandwich_assembly')->nullable();
            $table->text('assembly_comments')->nullable();
            $table->boolean('qsc_completed')->nullable();
            $table->text('qsc_comments')->nullable();
            $table->boolean('oil_standards')->nullable();
            $table->text('oil_comments')->nullable();
            $table->boolean('day_labels')->nullable();
            $table->text('labels_comments')->nullable();
            $table->boolean('equipment_working')->nullable();
            $table->text('equipment_comments')->nullable();
            $table->boolean('fryer_condition')->nullable();
            $table->text('fryer_comments')->nullable();
            $table->boolean('vegetable_prep')->nullable();
            $table->text('vegetable_comments')->nullable();
            $table->boolean('employee_appearance')->nullable();
            $table->text('appearance_comments')->nullable();
            
            // Section F: Cleanliness (5 items)
            $table->boolean('equipment_wrapped')->nullable();
            $table->text('wrapped_comments')->nullable();
            $table->boolean('sink_setup')->nullable();
            $table->text('sink_comments')->nullable();
            $table->boolean('sanitizer_standard')->nullable();
            $table->text('sanitizer_comments')->nullable();
            $table->boolean('dining_area_clean')->nullable();
            $table->text('dining_comments')->nullable();
            $table->boolean('restroom_clean')->nullable();
            $table->text('restroom_comments')->nullable();
            
            // Additional fields
            $table->text('general_comments')->nullable();
            $table->json('photos')->nullable(); // Array of photo paths
            $table->enum('status', ['Draft', 'Submitted', 'Reviewed', 'Approved'])->default('Draft');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_visits');
    }
};
