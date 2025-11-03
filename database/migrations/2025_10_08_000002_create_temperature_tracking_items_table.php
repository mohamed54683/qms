<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('temperature_tracking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('temperature_tracking_forms')->onDelete('cascade');
            $table->string('category'); // cooked, vegetable, equipment, sanitizer, etc.
            $table->string('item_name');
            $table->string('time_slot'); // e.g., 11AM–2PM
            $table->float('temperature')->nullable();
            $table->integer('ppm')->nullable();
            $table->enum('status', ['Y', 'N'])->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('temperature_tracking_items');
    }
};
