<?php

namespace App\Http\Controllers;

use App\Models\PagePermission;
use App\Models\RolePagePermission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class PermissionManagementController extends Controller
{
    /**
     * Display permission management page
     */
    public function index()
    {
        // Get roles ordered by name (alphabetically)
        $roles = Role::orderBy('name')->get();
        $pages = PagePermission::orderBy('display_order')->get();
        
        // Get all role-page permissions
        $rolePagePermissions = RolePagePermission::with(['role', 'pagePermission'])
            ->get()
            ->groupBy('role_id');
        
        return Inertia::render('Settings/PermissionManagement', [
            'roles' => $roles,
            'pages' => $pages,
            'rolePagePermissions' => $rolePagePermissions,
        ]);
    }

    /**
     * Update permissions for a role
     */
    public function updateRolePermissions(Request $request, $roleId)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*.page_permission_id' => 'required|exists:page_permissions,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
            'permissions.*.can_approve' => 'boolean',
            'permissions.*.show_in_dashboard' => 'boolean',
        ]);

        foreach ($validated['permissions'] as $permission) {
            RolePagePermission::updateOrCreate(
                [
                    'role_id' => $roleId,
                    'page_permission_id' => $permission['page_permission_id'],
                ],
                [
                    'can_view' => $permission['can_view'] ?? false,
                    'can_create' => $permission['can_create'] ?? false,
                    'can_edit' => $permission['can_edit'] ?? false,
                    'can_delete' => $permission['can_delete'] ?? false,
                    'can_approve' => $permission['can_approve'] ?? false,
                    'show_in_dashboard' => $permission['show_in_dashboard'] ?? true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Permissions updated successfully');
    }

    /**
     * Get permissions for a specific role
     */
    public function getRolePermissions($roleId)
    {
        $permissions = RolePagePermission::where('role_id', $roleId)
            ->with('pagePermission')
            ->get();

        return response()->json($permissions);
    }

    /**
     * Update page settings
     */
    public function updatePage(Request $request, $pageId)
    {
        $validated = $request->validate([
            'page_name' => 'required|string|max:255',
            'page_route' => 'nullable|string|max:255',
            'page_icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $page = PagePermission::findOrFail($pageId);
        $page->update($validated);

        return redirect()->back()->with('success', 'Page settings updated successfully');
    }
}
