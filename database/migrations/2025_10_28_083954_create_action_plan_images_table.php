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
        Schema::create('action_plan_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_plan_id')->constrained('action_plans')->onDelete('cascade');
            $table->string('image_path'); // Path to the stored image
            $table->string('field_name'); // The question field this image belongs to (e.g., 'suggestive_selling')
            $table->string('original_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
            
            // Index for performance
            $table->index(['action_plan_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_plan_images');
    }
};
