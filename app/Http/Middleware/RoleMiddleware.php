<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Split roles by pipe (|) to handle multiple roles
        $allowedRoles = explode('|', $roles);
        
        // Check if user has any of the required roles
        $hasRole = false;
        if ($user->roles) {
            foreach ($allowedRoles as $role) {
                $role = trim($role);
                if ($user->roles->contains('name', $role)) {
                    $hasRole = true;
                    break;
                }
                // Super Admin has access to all admin functions
                if ($role === 'admin' && $user->roles->contains('name', 'Super Admin')) {
                    $hasRole = true;
                    break;
                }
            }
        }
        
        if (!$hasRole) {
            abort(403, 'Access denied. Required role: ' . $roles);
        }

        return $next($request);
    }
}