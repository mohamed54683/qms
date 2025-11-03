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
        Schema::table('store_visits', function (Blueprint $table) {
            // Section 8 - Follow-up
            $table->date('last_visit_date')->nullable();
            $table->text('last_visit_summary')->nullable();
            $table->text('last_visit_update')->nullable();
            $table->text('other_follow_up')->nullable();
            
            // Section 9 - Observation Summary
            $table->text('what_did_you_see')->nullable();
            $table->text('why_had_issue')->nullable();
            $table->text('how_to_improve')->nullable();
            $table->text('who_when_responsible')->nullable();
            
            // Section 10 - Action Plan (Auto-Generated) - JSON field for action items
            $table->json('action_plan_items')->nullable(); // Auto-generated based on "NO" responses
            
            // Section 11 - Report Management
            $table->decimal('performance_score_percentage', 5, 2)->nullable(); // Calculated performance %
            $table->timestamp('report_timestamp')->nullable();
            $table->string('digital_signature_mic')->nullable();
            $table->string('digital_signature_reviewer')->nullable();
            
            // Additional fields for comprehensive tracking
            $table->enum('report_type', ['Quality Audit', 'Operational Assessment', 'Training Audit', 'Measurement and Coaching'])->nullable();
            $table->json('export_history')->nullable(); // Track PDF/Excel exports
            
            // Enhanced status tracking
            $table->string('created_by_name')->nullable();
            $table->string('reviewer_name')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // File attachments
            $table->json('attachments')->nullable(); // Store file paths for attachments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_visits', function (Blueprint $table) {
            $table->dropColumn([
                'last_visit_date',
                'last_visit_summary', 
                'last_visit_update',
                'other_follow_up',
                'what_did_you_see',
                'why_had_issue',
                'how_to_improve',
                'who_when_responsible',
                'action_plan_items',
                'performance_score_percentage',
                'report_timestamp',
                'digital_signature_mic',
                'digital_signature_reviewer',
                'report_type',
                'export_history',
                'created_by_name',
                'reviewer_name',
                'reviewed_at',
                'approved_at',
                'attachments'
            ]);
        });
    }
};
