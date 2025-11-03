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
        Schema::create('action_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_visit_id')->constrained()->onDelete('cascade');
            $table->string('item'); // Name of the checklist item (e.g., "Customer service", "Restroom cleanliness")
            $table->string('issue')->default('Not compliant'); // Issue type: "Not compliant", "Needs improvement"
            $table->text('action_required'); // Description of what needs to be done
            $table->enum('priority', ['High', 'Medium', 'Low'])->default('Medium');
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Who is responsible
            $table->date('due_date')->nullable(); // When it should be completed
            $table->text('notes')->nullable(); // Additional notes or updates
            $table->timestamp('completed_at')->nullable(); // When it was marked as completed
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['store_visit_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_plans');
    }
};
