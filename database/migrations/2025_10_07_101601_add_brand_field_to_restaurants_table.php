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
            // Add brand field with enum values
            $table->enum('brand', ['SDB', 'FB', 'TNDR', 'Pizza Dealer'])->nullable()->after('code');
            
            // Add index for better performance
            $table->index('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['brand']);
            
            // Drop brand column
            $table->dropColumn('brand');
        });
    }
};
