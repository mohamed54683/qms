<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('temperature_tracking_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_visit_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('shift')->nullable();
            $table->text('corrective_action')->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void {
        Schema::dropIfExists('temperature_tracking_forms');
    }
};
