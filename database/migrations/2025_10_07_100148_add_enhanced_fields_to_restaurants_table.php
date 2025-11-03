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
            // Enhanced restaurant fields
            $table->string('type')->nullable()->after('code'); // fast-casual, quick-service, etc.
            $table->enum('status', ['active', 'inactive', 'under-review'])->default('active')->after('type');
            $table->text('address')->nullable()->after('status');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('manager', 255)->nullable()->after('email'); // Manager name as string
            $table->string('opening_hours', 100)->nullable()->after('manager');
            $table->integer('seating_capacity')->nullable()->after('opening_hours');
            $table->integer('staff_count')->nullable()->after('seating_capacity');
            $table->text('notes')->nullable()->after('staff_count');
            
            // Add indexes for better performance
            $table->index('type');
            $table->index('status');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['city']);
            
            // Remove columns
            $table->dropColumn([
                'type', 'status', 'address', 'city', 'manager', 
                'opening_hours', 'seating_capacity', 'staff_count', 'notes'
            ]);
        });
    }
};
