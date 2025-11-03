<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PagePermission;
use App\Models\RolePagePermission;

class CheckPagePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $pageKey  The page key to check permission for
     * @param  string  $action  The action to check (view, create, edit, delete, approve)
     */
    public function handle(Request $request, Closure $next, string $pageKey, string $action = 'view'): Response
    {
        $user = $request->user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's role
        $role = $user->roles->first();
        
        // If user has no role, deny access
        if (!$role) {
            abort(403, 'You do not have permission to access this page');
        }

        // Super Admin bypass - allow full access
        if ($role->name === 'Super Admin') {
            return $next($request);
        }

        // Get the page permission
        $page = PagePermission::where('page_key', $pageKey)->first();
        
        // If page doesn't exist or is inactive, deny access
        if (!$page || !$page->is_active) {
            abort(404, 'Page not found');
        }

        // Get role-page permission
        $permission = RolePagePermission::where('role_id', $role->id)
            ->where('page_permission_id', $page->id)
            ->first();

        // If no permission record exists, deny access
        if (!$permission) {
            abort(403, 'You do not have permission to access this page');
        }

        // Check the specific action permission
        $hasPermission = false;
        switch ($action) {
            case 'view':
                $hasPermission = $permission->can_view;
                break;
            case 'create':
                $hasPermission = $permission->can_create;
                break;
            case 'edit':
                $hasPermission = $permission->can_edit;
                break;
            case 'delete':
                $hasPermission = $permission->can_delete;
                break;
            case 'approve':
                $hasPermission = $permission->can_approve;
                break;
        }

        if (!$hasPermission) {
            abort(403, 'You do not have permission to perform this action');
        }

        return $next($request);
    }
}
