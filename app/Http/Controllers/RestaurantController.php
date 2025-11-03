<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get restaurants with relationships
        $restaurants = Restaurant::with(['restaurantManager', 'areaManager'])
                                ->select('id', 'name', 'code', 'brand', 'type', 'status', 'address', 'city', 'phone', 'email', 'restaurant_manager_id', 'area_manager_id', 'is_active')
                                ->orderBy('name')
                                ->get();

        // Calculate statistics
        $statistics = [
            'total' => $restaurants->count(),
            'active' => $restaurants->where('status', 'active')->count(),
            'inactive' => $restaurants->where('status', 'inactive')->count(),
            'totalManagers' => User::role(['rm', 'area_manager'])->count()
        ];

        return Inertia::render('Restaurant/Index', [
            'restaurants' => $restaurants,
            'statistics' => $statistics
        ]);
    }

    /**
     * Get restaurants for dropdown (API endpoint)
     */
    public function getRestaurants()
    {
        return Restaurant::where('is_active', true)
                        ->select('id', 'name', 'code', 'location')
                        ->orderBy('name')
                        ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch users with specific roles for dropdowns
        $restaurantManagers = User::role('Regional Manager')->select('id', 'name', 'email')->get();
        $areaManagers = User::role('Area Manager')->select('id', 'name', 'email')->get();
        
        // Get Saudi Arabian cities from config
        $cities = config('cities.saudi_arabia');
        
        return Inertia::render('Restaurant/Create', [
            'restaurantManagers' => $restaurantManagers,
            'areaManagers' => $areaManagers,
            'cities' => $cities
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Enhanced validation with detailed rules and messages
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'code' => 'required|string|unique:restaurants,code|max:20|min:3|regex:/^[A-Z0-9]+$/',
            'brand' => 'nullable|in:SDB,FB,TNDR,Pizza Dealer',
            'type' => 'nullable|in:fast-casual,quick-service,full-service,cafe,drive-thru,kiosk', // Keep for backward compatibility
            'status' => 'required|in:active,inactive,under-review',
            'address' => 'nullable|string|max:500', // Made optional since field is hidden
            'city' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20|regex:/^\+966\s\d{2}\s\d{3}\s\d{4}$/',
            'restaurant_manager_id' => 'nullable|exists:users,id',
            'area_manager_id' => 'nullable|exists:users,id',
            'manager' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:restaurants,email',
            'opening_hours' => 'nullable|string|max:100',
            'seating_capacity' => 'nullable|integer|min:0|max:1000',
            'staff_count' => 'nullable|integer|min:0|max:200',
            'notes' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'Restaurant name is required.',
            'name.min' => 'Restaurant name must be at least 2 characters.',
            'code.required' => 'Restaurant code is required.',
            'code.unique' => 'This restaurant code is already taken. Please choose another.',
            'code.regex' => 'Restaurant code must contain only uppercase letters and numbers.',
            'code.min' => 'Restaurant code must be at least 3 characters.',
            'brand.in' => 'Please select a valid restaurant brand (SDB, FB, TNDR, or Pizza Dealer).',
            'latitude.required' => 'Latitude is required for location tracking.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required' => 'Longitude is required for location tracking.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
            'phone.regex' => 'Phone number must be in the format: +966 XX XXX XXXX',
            'restaurant_manager_id.exists' => 'The selected restaurant manager is invalid.',
            'area_manager_id.exists' => 'The selected area manager is invalid.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already associated with another restaurant.',
            'seating_capacity.max' => 'Seating capacity cannot exceed 1000.',
            'staff_count.max' => 'Staff count cannot exceed 200.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ]);

        // Set default values
        $validated['is_active'] = $validated['status'] === 'active';
        
        // Clean and format data
        $validated['code'] = strtoupper($validated['code']);
        $validated['name'] = trim($validated['name']);
        
        // Format phone number if provided
        if (!empty($validated['phone'])) {
            $validated['phone'] = $this->formatPhoneNumber($validated['phone']);
        }

        // Create restaurant
        $restaurant = Restaurant::create($validated);

        // Log the creation
        \Log::info('New restaurant created', [
            'restaurant_id' => $restaurant->id,
            'name' => $restaurant->name,
            'code' => $restaurant->code,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant "' . $restaurant->name . '" created successfully!');
    }

    /**
     * Check if restaurant code is available
     */
    public function checkCodeAvailability(Request $request)
    {
        $code = strtoupper($request->input('code'));
        
        if (empty($code)) {
            return response()->json(['available' => null, 'message' => '']);
        }

        $exists = Restaurant::where('code', $code)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists 
                ? 'This restaurant code is already in use. Please choose another.' 
                : 'Restaurant code is available!'
        ]);
    }

    /**
     * Format phone number to standard format
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // Handle Saudi phone numbers
        if (str_starts_with($cleaned, '966')) {
            $cleaned = substr($cleaned, 3);
        } elseif (str_starts_with($cleaned, '0')) {
            $cleaned = substr($cleaned, 1);
        }
        
        // Format as +966 XX XXX XXXX
        if (strlen($cleaned) === 9) {
            return '+966 ' . substr($cleaned, 0, 2) . ' ' . substr($cleaned, 2, 3) . ' ' . substr($cleaned, 5, 4);
        }
        
        return $phone; // Return original if can't format
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        // Load restaurant with relationships
        $restaurant->load(['restaurantManager', 'areaManager', 'users']);
        
        return Inertia::render('Restaurant/Show', [
            'restaurant' => $restaurant
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant)
    {
        // Fetch users with specific roles for dropdowns
        $restaurantManagers = User::role('Regional Manager')->select('id', 'name', 'email')->get();
        $areaManagers = User::role('Area Manager')->select('id', 'name', 'email')->get();
        
        // Get Saudi Arabian cities from config
        $cities = config('cities.saudi_arabia');
        
        // Load restaurant with relationships
        $restaurant->load(['restaurantManager', 'areaManager']);
        
        return Inertia::render('Restaurant/Edit', [
            'restaurant' => $restaurant,
            'restaurantManagers' => $restaurantManagers,
            'areaManagers' => $areaManagers,
            'cities' => $cities
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        // Enhanced validation with detailed rules and messages
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'code' => 'required|string|max:20|min:3|regex:/^[A-Z0-9]+$/|unique:restaurants,code,' . $restaurant->id,
            'brand' => 'nullable|in:SDB,FB,TNDR,Pizza Dealer',
            'type' => 'nullable|in:fast-casual,quick-service,full-service,cafe,drive-thru,kiosk',
            'status' => 'required|in:active,inactive,under-review',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20|regex:/^\+966\s\d{2}\s\d{3}\s\d{4}$/',
            'restaurant_manager_id' => 'nullable|exists:users,id',
            'area_manager_id' => 'nullable|exists:users,id',
            'manager' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:restaurants,email,' . $restaurant->id,
            'opening_hours' => 'nullable|string|max:100',
            'seating_capacity' => 'nullable|integer|min:0|max:1000',
            'staff_count' => 'nullable|integer|min:0|max:200',
            'notes' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'Restaurant name is required.',
            'name.min' => 'Restaurant name must be at least 2 characters.',
            'code.required' => 'Restaurant code is required.',
            'code.unique' => 'This restaurant code is already taken. Please choose another.',
            'code.regex' => 'Restaurant code must contain only uppercase letters and numbers.',
            'code.min' => 'Restaurant code must be at least 3 characters.',
            'brand.in' => 'Please select a valid restaurant brand (SDB, FB, TNDR, or Pizza Dealer).',
            'latitude.required' => 'Latitude is required for location tracking.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required' => 'Longitude is required for location tracking.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
            'phone.regex' => 'Phone number must be in the format: +966 XX XXX XXXX',
            'restaurant_manager_id.exists' => 'The selected restaurant manager is invalid.',
            'area_manager_id.exists' => 'The selected area manager is invalid.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already associated with another restaurant.',
            'seating_capacity.max' => 'Seating capacity cannot exceed 1000.',
            'staff_count.max' => 'Staff count cannot exceed 200.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ]);

        // Set default values
        $validated['is_active'] = $validated['status'] === 'active';
        
        // Clean and format data
        $validated['code'] = strtoupper($validated['code']);
        $validated['name'] = trim($validated['name']);
        
        // Format phone number if provided
        if (!empty($validated['phone'])) {
            $validated['phone'] = $this->formatPhoneNumber($validated['phone']);
        }

        // Update restaurant
        $restaurant->update($validated);

        // Log the update
        \Log::info('Restaurant updated', [
            'restaurant_id' => $restaurant->id,
            'name' => $restaurant->name,
            'code' => $restaurant->code,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant "' . $restaurant->name . '" updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        // Store restaurant details for logging before deletion
        $restaurantName = $restaurant->name;
        $restaurantId = $restaurant->id;
        
        try {
            // Delete the restaurant
            $restaurant->delete();
            
            // Log the deletion
            \Log::info('Restaurant deleted', [
                'restaurant_id' => $restaurantId,
                'name' => $restaurantName,
                'deleted_by' => auth()->id()
            ]);
            
            return redirect()->route('restaurants.index')->with('success', 'Restaurant "' . $restaurantName . '" deleted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete restaurant', [
                'restaurant_id' => $restaurantId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('restaurants.index')->with('error', 'Failed to delete restaurant. It may have associated data.');
        }
    }

    /**
     * Perform bulk actions on restaurants.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate',
            'restaurant_ids' => 'required|array',
            'restaurant_ids.*' => 'exists:restaurants,id'
        ]);

        $action = $request->action;
        $restaurantIds = $request->restaurant_ids;
        
        $count = 0;

        if ($action === 'activate') {
            $count = Restaurant::whereIn('id', $restaurantIds)->update(['status' => 'active']);
            $message = "$count restaurants activated successfully!";
        } elseif ($action === 'deactivate') {
            $count = Restaurant::whereIn('id', $restaurantIds)->update(['status' => 'inactive']);
            $message = "$count restaurants deactivated successfully!";
        }

        return back()->with('success', $message);
    }
}
