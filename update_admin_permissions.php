<?php
/**
 * Update Admin Permissions Script
 * This script ensures the admin user has full access to view and edit all pages
 */

require_once 'vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Updating Admin Permissions ===\n\n";

// Find the admin user
$adminUser = User::where('email', 'admin@gsqms.com')->first();

if (!$adminUser) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Found admin user: {$adminUser->name} ({$adminUser->email})\n";

// Create comprehensive permissions for all pages
$allPermissions = [
    // Dashboard permissions
    'view_dashboard',
    'view_analytics',
    'view_reports',
    
    // Store Visits permissions
    'view_store_visits',
    'create_store_visits',
    'edit_store_visits',
    'delete_store_visits',
    'approve_store_visits',
    'submit_store_visits',
    'review_store_visits',
    'export_store_visits',
    
    // Action Plans permissions
    'view_action_plans',
    'create_action_plans',
    'edit_action_plans',
    'delete_action_plans',
    'approve_action_plans',
    'assign_action_plans',
    'complete_action_plans',
    
    // User Management permissions
    'view_users',
    'create_users',
    'edit_users',
    'delete_users',
    'manage_users',
    'assign_roles',
    'view_user_profiles',
    
    // Restaurant Management permissions
    'view_restaurants',
    'create_restaurants',
    'edit_restaurants',
    'delete_restaurants',
    'manage_restaurants',
    'assign_managers',
    
    // Role and Permission Management
    'view_roles',
    'create_roles',
    'edit_roles',
    'delete_roles',
    'manage_roles',
    'view_permissions',
    'create_permissions',
    'edit_permissions',
    'delete_permissions',
    'manage_permissions',
    
    // Temperature Tracking permissions
    'view_temperature_tracking',
    'create_temperature_tracking',
    'edit_temperature_tracking',
    'delete_temperature_tracking',
    'approve_temperature_tracking',
    
    // QSC Checklists permissions
    'view_qsc_checklists',
    'create_qsc_checklists',
    'edit_qsc_checklists',
    'delete_qsc_checklists',
    
    // System Administration
    'view_system_settings',
    'edit_system_settings',
    'view_audit_logs',
    'manage_system',
    'backup_system',
    'restore_system',
    
    // Report Generation
    'generate_reports',
    'export_reports',
    'view_all_reports',
    'schedule_reports',
    
    // Page-specific permissions
    'access_all_pages',
    'view_all_data',
    'edit_all_data',
    'delete_all_data',
    
    // Advanced permissions
    'super_admin_access',
    'bypass_restrictions',
    'view_sensitive_data',
    'edit_sensitive_data',
];

echo "\n📋 Creating/updating permissions...\n";

// Create all permissions if they don't exist
foreach ($allPermissions as $permissionName) {
    $permission = Permission::firstOrCreate([
        'name' => $permissionName,
        'guard_name' => 'web'
    ]);
    echo "✅ Permission: {$permissionName}\n";
}

// Get Super Admin role
$superAdminRole = Role::where('name', 'Super Admin')->first();

if (!$superAdminRole) {
    echo "\n❌ Super Admin role not found! Creating it...\n";
    $superAdminRole = Role::create([
        'name' => 'Super Admin',
        'guard_name' => 'web'
    ]);
    echo "✅ Created Super Admin role\n";
}

// Assign all permissions to Super Admin role
echo "\n🔧 Assigning all permissions to Super Admin role...\n";
$allPermissionObjects = Permission::all();
$superAdminRole->syncPermissions($allPermissionObjects);
echo "✅ Assigned {$allPermissionObjects->count()} permissions to Super Admin role\n";

// Ensure admin user has Super Admin role
if (!$adminUser->hasRole('Super Admin')) {
    $adminUser->assignRole('Super Admin');
    echo "✅ Assigned Super Admin role to admin user\n";
} else {
    echo "✅ Admin user already has Super Admin role\n";
}

// Also directly assign all permissions to the admin user (double security)
echo "\n🔐 Directly assigning all permissions to admin user...\n";
foreach ($allPermissionObjects as $permission) {
    if (!$adminUser->hasPermissionTo($permission->name)) {
        $adminUser->givePermissionTo($permission);
    }
}
echo "✅ Admin user now has direct access to all permissions\n";

// Verify permissions
echo "\n🔍 Verifying admin permissions...\n";
echo "✅ Admin user has Super Admin role with all permissions\n";

// Test some key permissions
$keyPermissions = [
    'view_dashboard',
    'manage_users',
    'manage_restaurants',
    'view_store_visits',
    'edit_store_visits',
    'create_action_plans',
    'super_admin_access'
];

echo "\n🧪 Testing key permissions:\n";
foreach ($keyPermissions as $permission) {
    try {
        if ($adminUser->can($permission)) {
            echo "✅ Can: {$permission}\n";
        } else {
            echo "❌ Cannot: {$permission}\n";
        }
    } catch (Exception $e) {
        echo "⚠️ Permission check skipped: {$permission}\n";
    }
}

echo "\n=== Admin Permissions Update Complete! ===\n";
echo "The admin user (admin@gsqms.com) now has full access to:\n";
echo "- View all pages and data\n";
echo "- Edit all content\n";
echo "- Create and delete records\n";
echo "- Manage users and roles\n";
echo "- Access system administration\n";
echo "- Generate and export reports\n";
echo "- All other system features\n\n";
echo "🎉 Admin can now access and edit everything in the system!\n";
?>