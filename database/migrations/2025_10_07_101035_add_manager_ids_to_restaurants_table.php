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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurant_manager_id')->nullable()->after('phone');
            $table->unsignedBigInteger('area_manager_id')->nullable()->after('restaurant_manager_id');
            
            // Add foreign key constraints
            $table->foreign('restaurant_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('area_manager_id')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index('restaurant_manager_id');
            $table->index('area_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['restaurant_manager_id']);
            $table->dropForeign(['area_manager_id']);
            
            // Drop indexes
            $table->dropIndex(['restaurant_manager_id']);
            $table->dropIndex(['area_manager_id']);
            
            // Drop columns
            $table->dropColumn('restaurant_manager_id');
            $table->dropColumn('area_manager_id');
        });
    }
};
