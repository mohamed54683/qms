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
        Schema::table('temperature_tracking_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('temperature_tracking_forms', 'status')) {
                $table->enum('status', ['Draft', 'Submitted', 'Reviewed'])->default('Draft')->after('shift_turnover');
            }
            if (!Schema::hasColumn('temperature_tracking_forms', 'mic_morning')) {
                $table->string('mic_morning')->nullable()->after('shift');
            }
            if (!Schema::hasColumn('temperature_tracking_forms', 'mic_evening')) {
                $table->string('mic_evening')->nullable()->after('mic_morning');
            }
            if (!Schema::hasColumn('temperature_tracking_forms', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temperature_tracking_forms', function (Blueprint $table) {
            if (Schema::hasColumn('temperature_tracking_forms', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('temperature_tracking_forms', 'mic_morning')) {
                $table->dropColumn('mic_morning');
            }
            if (Schema::hasColumn('temperature_tracking_forms', 'mic_evening')) {
                $table->dropColumn('mic_evening');
            }
            if (Schema::hasColumn('temperature_tracking_forms', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
