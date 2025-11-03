<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        return [
            ...parent::share($request),
            'csrf_token' => csrf_token(),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles,
                    'restaurants' => $user->restaurants,
                    'permissions' => $this->getUserPermissions($user),
                ] : null,
            ],
        ];
    }
    
    /**
     * Get user permissions for all pages
     */
    public function getUserPermissions($user): array
    {
        if (!$user) {
            return [];
        }
        
        if ($user->roles->isEmpty()) {
            return [];
        }
        
        $permissionsData = [];
        
        // Process all user roles and merge permissions
        foreach ($user->roles as $role) {
            $rolePermissions = \App\Models\RolePagePermission::with('pagePermission')
                ->where('role_id', $role->id)
                ->get();
            
            foreach ($rolePermissions as $rolePermission) {
                if ($rolePermission->pagePermission) {
                    $pageKey = $rolePermission->pagePermission->page_key;
                    
                    if (!isset($permissionsData[$pageKey])) {
                        $permissionsData[$pageKey] = [
                            'can_view' => false,
                            'can_create' => false,
                            'can_edit' => false,
                            'can_delete' => false,
                            'can_approve' => false,
                            'show_in_dashboard' => false,
                        ];
                    }
                    
                    // Merge permissions (if any role has permission, user has permission)
                    $permissionsData[$pageKey]['can_view'] = $permissionsData[$pageKey]['can_view'] || $rolePermission->can_view;
                    $permissionsData[$pageKey]['can_create'] = $permissionsData[$pageKey]['can_create'] || $rolePermission->can_create;
                    $permissionsData[$pageKey]['can_edit'] = $permissionsData[$pageKey]['can_edit'] || $rolePermission->can_edit;
                    $permissionsData[$pageKey]['can_delete'] = $permissionsData[$pageKey]['can_delete'] || $rolePermission->can_delete;
                    $permissionsData[$pageKey]['can_approve'] = $permissionsData[$pageKey]['can_approve'] || $rolePermission->can_approve;
                    $permissionsData[$pageKey]['show_in_dashboard'] = $permissionsData[$pageKey]['show_in_dashboard'] || $rolePermission->show_in_dashboard;
                }
            }
        }
        
        return $permissionsData;
    }
}
