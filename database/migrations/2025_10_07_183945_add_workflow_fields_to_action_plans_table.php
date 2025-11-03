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
        Schema::table('action_plans', function (Blueprint $table) {
            // Workflow tracking fields
            $table->boolean('is_approved')->default(false)->after('status');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            
            // Additional visit context
            $table->string('visit_purpose')->nullable()->after('approved_by');
            $table->string('area_manager')->nullable()->after('visit_purpose');
            $table->string('restaurant_manager')->nullable()->after('area_manager');
            
            // Action plan 5W1H fields
            $table->text('what')->nullable()->after('action_required'); // What needs to be done (detailed)
            $table->text('where')->nullable()->after('what'); // Where will this be implemented
            $table->text('why')->nullable()->after('where'); // Why this action is needed
            $table->text('how')->nullable()->after('why'); // How will this be executed
            $table->text('who')->nullable()->after('how'); // Who will execute this (detailed)
            $table->text('when_detail')->nullable()->after('who'); // When in detail (beyond due_date)
            $table->text('comment')->nullable()->after('when_detail'); // Additional comments
            
            // Indexes for better performance
            $table->index(['is_approved', 'status']);
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_plans', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['is_approved', 'status']);
            $table->dropIndex(['approved_by']);
            
            $table->dropColumn([
                'is_approved', 'approved_at', 'approved_by',
                'visit_purpose', 'area_manager', 'restaurant_manager',
                'what', 'where', 'why', 'how', 'who', 'when_detail', 'comment'
            ]);
        });
    }
};
