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
            // Workflow Management
            if (!Schema::hasColumn('store_visits', 'restaurant_id')) {
                $table->foreignId('restaurant_id')->nullable()->constrained('restaurants')->onDelete('cascade')->after('user_id');
            }
            if (!Schema::hasColumn('store_visits', 'area_manager_id')) {
                $table->foreignId('area_manager_id')->nullable()->constrained('users')->onDelete('set null')->after('restaurant_id');
            }
            if (!Schema::hasColumn('store_visits', 'store_manager_id')) {
                $table->foreignId('store_manager_id')->nullable()->constrained('users')->onDelete('set null')->after('area_manager_id');
            }
            if (!Schema::hasColumn('store_visits', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('store_manager_id');
            }
            
            // Enhanced Status Management
            $table->enum('status', ['draft', 'submitted', 'under_review', 'action_plans_created', 'completed', 'approved', 'rejected'])
                  ->default('draft')->change();
            
            // Workflow Timestamps
            if (!Schema::hasColumn('store_visits', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('store_visits', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('store_visits', 'action_plans_completed_at')) {
                $table->timestamp('action_plans_completed_at')->nullable()->after('reviewed_at');
            }
            if (!Schema::hasColumn('store_visits', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('action_plans_completed_at');
            }
            
            // Scoring and Analytics
            if (!Schema::hasColumn('store_visits', 'calculated_score')) {
                $table->decimal('calculated_score', 5, 2)->nullable()->after('score'); // Auto-calculated percentage
            }
            if (!Schema::hasColumn('store_visits', 'total_questions')) {
                $table->integer('total_questions')->nullable()->after('calculated_score'); // Total questions answered
            }
            if (!Schema::hasColumn('store_visits', 'yes_answers')) {
                $table->integer('yes_answers')->nullable()->after('total_questions'); // Number of Yes answers
            }
            if (!Schema::hasColumn('store_visits', 'no_answers')) {
                $table->integer('no_answers')->nullable()->after('yes_answers'); // Number of No answers
            }
            
            // Notification tracking
            if (!Schema::hasColumn('store_visits', 'notifications_sent')) {
                $table->json('notifications_sent')->nullable()->after('no_answers'); // Track what notifications were sent
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_visits', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['area_manager_id']);
            $table->dropForeign(['store_manager_id']);
            $table->dropForeign(['reviewed_by']);
            
            $table->dropColumn([
                'restaurant_id', 'area_manager_id', 'store_manager_id', 'reviewed_by',
                'submitted_at', 'reviewed_at', 'action_plans_completed_at', 'approved_at',
                'calculated_score', 'total_questions', 'yes_answers', 'no_answers', 'notifications_sent'
            ]);
            
            $table->enum('status', ['Draft', 'Submitted', 'Reviewed', 'Approved'])->default('Draft')->change();
        });
    }
};
