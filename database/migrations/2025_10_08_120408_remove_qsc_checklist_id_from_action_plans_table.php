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
        Schema::table('action_plans', function (Blueprint $table) {
            if (Schema::hasColumn('action_plans', 'qsc_checklist_id')) {
                $table->dropForeign(['qsc_checklist_id']);
                $table->dropColumn('qsc_checklist_id');
            }
        });
        
        // Drop QSC checklists table
        Schema::dropIfExists('qsc_checklists');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_plans', function (Blueprint $table) {
            //
        });
    }
};
