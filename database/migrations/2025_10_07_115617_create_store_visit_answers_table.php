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
        Schema::create('store_visit_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('store_visits')->onDelete('cascade');
            $table->string('section'); // e.g., "Customer/QSC", "Cashier", etc.
            $table->string('question'); // The specific question text
            $table->string('field_name'); // The database field name for reference
            $table->boolean('answer'); // Yes/No response
            $table->text('comment')->nullable(); // Required when answer is No
            $table->timestamps();

            $table->index(['visit_id', 'answer']); // For quick filtering of No answers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_visit_answers');
    }
};
