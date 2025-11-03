<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the permission if it doesn't exist
        $permission = Permission::firstOrCreate([
            'name' => 'view_area_manager_filter',
            'guard_name' => 'web'
        ]);
        
        // Assign to admin roles
        $adminRoles = Role::whereIn('name', ['admin', 'Super Admin'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permission);
        }
        
        echo "Created and assigned view_area_manager_filter permission to admin roles.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'view_area_manager_filter')->delete();
    }
};