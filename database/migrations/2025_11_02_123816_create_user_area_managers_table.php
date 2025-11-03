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
        Schema::create('user_area_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_manager_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['user_id', 'area_manager_id']);
            
            // Add indexes for better performance
            $table->index('user_id');
            $table->index('area_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_area_managers');
    }
};
