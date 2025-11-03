<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRestaurantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Admin/Super Admin can access all restaurants
        if ($user->roles && (
            $user->roles->contains('name', 'admin') || 
            $user->roles->contains('name', 'Super Admin')
        )) {
            return $next($request);
        }
        
        // Get restaurant from request (various forms)
        $restaurantName = $request->input('store_name') 
            ?? $request->input('restaurant_name')
            ?? $request->input('restaurant');
        
        // If restaurant was auto-detected by location middleware, it's already validated
        if ($request->input('auto_detected_restaurant')) {
            return $next($request);
        }
        
        // If no restaurant specified and user has only one assigned restaurant, auto-set it
        if (!$restaurantName) {
            $userRestaurants = $user->restaurants()->where('is_active', true)->get();
            
            if ($userRestaurants->isEmpty()) {
                return response()->json([
                    'message' => 'You have no assigned restaurants. Please contact your administrator.',
                    'error' => 'NO_ASSIGNED_RESTAURANTS'
                ], 403);
            }
            
            if ($userRestaurants->count() === 1) {
                // Auto-set the only assigned restaurant
                $restaurant = $userRestaurants->first();
                $request->merge([
                    'store_name' => $restaurant->name,
                    'restaurant_name' => $restaurant->name,
                    'restaurant_id' => $restaurant->id,
                    'auto_assigned_restaurant' => true
                ]);
                return $next($request);
            }
            
            // Multiple restaurants, user must specify
            return response()->json([
                'message' => 'Please select a restaurant. You have access to: ' . $userRestaurants->pluck('name')->implode(', '),
                'error' => 'RESTAURANT_NOT_SPECIFIED',
                'allowed_restaurants' => $userRestaurants->map(function($r) {
                    return [
                        'id' => $r->id,
                        'name' => $r->name,
                        'code' => $r->code,
                        'address' => $r->address
                    ];
                })
            ], 400);
        }
        
        // Check if user has access to this restaurant
        $userRestaurants = $user->restaurants->pluck('name')->toArray();
        
        if (empty($userRestaurants)) {
            return response()->json([
                'message' => 'You have no assigned restaurants. Please contact your administrator.',
                'error' => 'NO_ASSIGNED_RESTAURANTS'
            ], 403);
        }
        
        $hasAccess = false;
        foreach ($userRestaurants as $restaurant) {
            if (stripos($restaurantName, $restaurant) !== false || stripos($restaurant, $restaurantName) !== false) {
                $hasAccess = true;
                break;
            }
        }
        
        if (!$hasAccess) {
            return response()->json([
                'message' => 'You do not have access to this restaurant. You can only access: ' . implode(', ', $userRestaurants),
                'error' => 'UNAUTHORIZED_RESTAURANT_ACCESS',
                'allowed_restaurants' => $userRestaurants
            ], 403);
        }
        
        return $next($request);
    }
}
