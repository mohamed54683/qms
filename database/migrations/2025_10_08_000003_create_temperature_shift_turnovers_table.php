<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('temperature_shift_turnovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('temperature_tracking_forms')->onDelete('cascade');
            $table->string('category');
            $table->enum('mic_morning_status', ['Y', 'N'])->nullable();
            $table->enum('mic_evening_status', ['Y', 'N'])->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('temperature_shift_turnovers');
    }
};
