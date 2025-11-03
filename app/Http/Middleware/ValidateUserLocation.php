<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;

class ValidateUserLocation
{
    /**
     * Maximum allowed distance from restaurant in kilometers
     */
    const MAX_DISTANCE_KM = 0.5; // 500 meters

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Admin/Super Admin bypass location validation
        if ($user->roles && (
            $user->roles->contains('name', 'admin') || 
            $user->roles->contains('name', 'Super Admin')
        )) {
            return $next($request);
        }
        
        // Get user's current location from request
        $userLat = $request->input('user_latitude');
        $userLon = $request->input('user_longitude');
        
        // If location data not provided, return error
        if (!$userLat || !$userLon) {
            return response()->json([
                'message' => 'Location permission required. Please enable location access to continue.',
                'error' => 'LOCATION_REQUIRED'
            ], 400);
        }
        
        // Get restaurant from request
        $restaurantName = $request->input('store_name') 
            ?? $request->input('restaurant_name')
            ?? $request->input('restaurant');
        
        // Get user's assigned restaurants
        $userRestaurants = $user->restaurants()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        
        if ($userRestaurants->isEmpty()) {
            // User has no assigned restaurants with coordinates
            return response()->json([
                'message' => 'You have no assigned restaurants with location coordinates. Please contact your administrator.',
                'error' => 'NO_VALID_RESTAURANTS'
            ], 403);
        }
        
        // If no restaurant specified, determine based on user's location
        if (!$restaurantName) {
            // Find nearest assigned restaurant
            $nearestRestaurant = null;
            $minDistance = PHP_FLOAT_MAX;
            
            foreach ($userRestaurants as $rest) {
                $distance = $this->calculateDistance(
                    $userLat,
                    $userLon,
                    $rest->latitude,
                    $rest->longitude
                );
                
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestRestaurant = $rest;
                }
            }
            
            if ($nearestRestaurant && $minDistance <= self::MAX_DISTANCE_KM) {
                // Auto-set restaurant based on location
                $request->merge([
                    'store_name' => $nearestRestaurant->name,
                    'restaurant_name' => $nearestRestaurant->name,
                    'restaurant_id' => $nearestRestaurant->id,
                    'auto_detected_restaurant' => true
                ]);
                $restaurant = $nearestRestaurant;
                $restaurantName = $nearestRestaurant->name;
            } else {
                return response()->json([
                    'message' => sprintf(
                        'You are not within %d meters of any assigned restaurant. Nearest: %s (%.0f meters away)',
                        self::MAX_DISTANCE_KM * 1000,
                        $nearestRestaurant ? $nearestRestaurant->name : 'None',
                        $minDistance * 1000
                    ),
                    'error' => 'NOT_AT_ASSIGNED_RESTAURANT',
                    'nearest_restaurant' => $nearestRestaurant ? [
                        'name' => $nearestRestaurant->name,
                        'distance_km' => round($minDistance, 2),
                        'address' => $nearestRestaurant->address
                    ] : null,
                    'assigned_restaurants' => $userRestaurants->map(function($r) {
                        return ['name' => $r->name, 'address' => $r->address];
                    })
                ], 403);
            }
        } else {
            // Restaurant specified, find it
            $restaurant = $userRestaurants->first(function($r) use ($restaurantName) {
                return stripos($r->name, $restaurantName) !== false || stripos($restaurantName, $r->name) !== false;
            });
            
            if (!$restaurant) {
                // Try to find in all restaurants (for admins who might bypass restaurant_access)
                $restaurant = Restaurant::where('name', 'LIKE', '%' . $restaurantName . '%')->first();
            }
        }
        
        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant not found.',
                'error' => 'RESTAURANT_NOT_FOUND'
            ], 404);
        }
        
        // Check if restaurant has location coordinates
        if (!$restaurant->latitude || !$restaurant->longitude) {
            // If restaurant doesn't have coordinates, allow access (backward compatibility)
            return $next($request);
        }
        
        // Calculate distance between user and restaurant
        $distance = $this->calculateDistance(
            $userLat,
            $userLon,
            $restaurant->latitude,
            $restaurant->longitude
        );
        
        // Check if user is within allowed distance
        if ($distance > self::MAX_DISTANCE_KM) {
            return response()->json([
                'message' => sprintf(
                    'You must be within %d meters of %s to perform this action. Current distance: %.0f meters.',
                    self::MAX_DISTANCE_KM * 1000,
                    $restaurant->name,
                    $distance * 1000
                ),
                'error' => 'LOCATION_TOO_FAR',
                'distance_km' => round($distance, 2),
                'max_distance_km' => self::MAX_DISTANCE_KM,
                'restaurant' => [
                    'name' => $restaurant->name,
                    'address' => $restaurant->address
                ]
            ], 403);
        }
        
        // Add location info to request for logging
        $request->merge([
            'validated_location' => [
                'user_lat' => $userLat,
                'user_lon' => $userLon,
                'restaurant_lat' => $restaurant->latitude,
                'restaurant_lon' => $restaurant->longitude,
                'distance_km' => round($distance, 2)
            ]
        ]);
        
        return $next($request);
    }
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
