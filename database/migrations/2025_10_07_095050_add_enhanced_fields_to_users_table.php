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
        Schema::table('users', function (Blueprint $table) {
            // Add enhanced user management fields
            $table->boolean('is_active')->default(true)->after('password');
            $table->string('employee_id')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('email');
            $table->timestamp('last_login_at')->nullable()->after('password');
            
            // Add indexes for better performance
            $table->index('is_active');
            $table->index('employee_id');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex(['is_active']);
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['last_login_at']);
            
            // Remove columns
            $table->dropColumn(['is_active', 'employee_id', 'avatar_url', 'last_login_at']);
        });
    }
};
