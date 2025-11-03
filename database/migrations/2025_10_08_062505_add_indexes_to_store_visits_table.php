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
            // Add indexes for commonly queried fields
            $table->index('visit_date');
            $table->index('status');
            $table->index('mic');
            $table->index('restaurant_name');
            $table->index(['user_id', 'visit_date']);
            $table->index(['status', 'visit_date']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_visits', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['visit_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['mic']);
            $table->dropIndex(['restaurant_name']);
            $table->dropIndex(['user_id', 'visit_date']);
            $table->dropIndex(['status', 'visit_date']);
            $table->dropIndex(['created_at']);
        });
    }
};
