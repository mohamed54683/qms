<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('temperature_tracking_forms')) {
            Schema::table('temperature_tracking_forms', function (Blueprint $table) {
                if (!Schema::hasColumn('temperature_tracking_forms','cooked_products')) {
                    $table->json('cooked_products')->nullable()->after('date');
                }
                if (!Schema::hasColumn('temperature_tracking_forms','holding_products')) {
                    $table->json('holding_products')->nullable()->after('cooked_products');
                }
                if (!Schema::hasColumn('temperature_tracking_forms','vegetables')) {
                    $table->json('vegetables')->nullable()->after('holding_products');
                }
                if (!Schema::hasColumn('temperature_tracking_forms','equipment')) {
                    $table->json('equipment')->nullable()->after('vegetables');
                }
                if (!Schema::hasColumn('temperature_tracking_forms','sanitizer')) {
                    $table->json('sanitizer')->nullable()->after('equipment');
                }
                if (!Schema::hasColumn('temperature_tracking_forms','receiving_products')) {
                    $table->json('receiving_products')->nullable()->after('sanitizer');
                }
                if (!Schema::hasColumn('temperature_tracking_forms','shift_turnover')) {
                    $table->json('shift_turnover')->nullable()->after('corrective_action');
                }

                // Extended columns (side products, receiving side, separate corrective segments)
                if (!Schema::hasColumn('temperature_tracking_forms','side_cooked')) {
                    $table->json('side_cooked')->nullable();
                }
                if (!Schema::hasColumn('temperature_tracking_forms','side_holding')) {
                    $table->json('side_holding')->nullable();
                }
                if (!Schema::hasColumn('temperature_tracking_forms','vegetable_receiving')) {
                    $table->json('vegetable_receiving')->nullable();
                }
                if (!Schema::hasColumn('temperature_tracking_forms','product_receiving_side')) {
                    $table->json('product_receiving_side')->nullable();
                }
                if (!Schema::hasColumn('temperature_tracking_forms','corrective_action_upper')) {
                    $table->text('corrective_action_upper')->nullable();
                }
                if (!Schema::hasColumn('temperature_tracking_forms','corrective_action_lower')) {
                    $table->text('corrective_action_lower')->nullable();
                }
                if (Schema::hasColumn('temperature_tracking_forms','status')) {
                    // ensure enum values match current expected case (Draft/Submitted/Reviewed) if original lowercase
                    // For MySQL altering enum is more complex; we skip here to avoid destructive change.
                }
            });
        }
    }
    public function down(): void
    {
        // Do not drop columns on rollback to avoid data loss.
    }
};