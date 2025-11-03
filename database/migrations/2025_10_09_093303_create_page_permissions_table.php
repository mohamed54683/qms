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
        Schema::create('page_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('page_name'); // اسم الصفحة
            $table->string('page_key')->unique(); // مفتاح فريد للصفحة
            $table->string('page_route')->nullable(); // المسار
            $table->string('page_icon')->nullable(); // الأيقونة
            $table->boolean('is_active')->default(true); // نشط/غير نشط
            $table->integer('display_order')->default(0); // ترتيب العرض
            $table->timestamps();
        });

        // جدول ربط الصلاحيات بالأدوار
        Schema::create('role_page_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('page_permission_id')->constrained('page_permissions')->onDelete('cascade');
            $table->boolean('can_view')->default(false); // صلاحية العرض
            $table->boolean('can_create')->default(false); // صلاحية الإضافة
            $table->boolean('can_edit')->default(false); // صلاحية التعديل
            $table->boolean('can_delete')->default(false); // صلاحية الحذف
            $table->boolean('can_approve')->default(false); // صلاحية الاعتماد
            $table->boolean('show_in_dashboard')->default(true); // الظهور في لوحة الحكم
            $table->timestamps();
            
            $table->unique(['role_id', 'page_permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_page_permissions');
        Schema::dropIfExists('page_permissions');
    }
};
