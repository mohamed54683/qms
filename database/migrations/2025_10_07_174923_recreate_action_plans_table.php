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
        // Drop existing table if exists
        Schema::dropIfExists('action_plans');
        
        // Create the table with correct structure
        Schema::create('action_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_visit_id')->constrained()->onDelete('cascade');
            $table->string('item'); // Name of the checklist item
            $table->string('issue')->default('Not compliant'); // Issue type
            $table->text('action_required'); // Description of what needs to be done
            $table->enum('priority', ['High', 'Medium', 'Low'])->default('Medium');
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
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
