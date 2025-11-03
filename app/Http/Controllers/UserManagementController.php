<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Restaurant;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'restaurants'])
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->where('model_has_roles.model_type', User::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%")
                  ->orWhere('users.employee_id', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('roles.name', $request->role);
        }

        // Status filter (assuming you add a status column to users table)
        if ($request->filled('status')) {
            $statusCondition = $request->status === 'active' ? 1 : 0;
            if ($request->status === 'pending') {
                $query->whereNull('users.email_verified_at');
            } else {
                $query->where('users.is_active', $statusCondition);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'role') {
            $query->orderBy('roles.name', $sortDirection);
        } elseif ($sortField === 'last_login') {
            $query->orderBy('users.last_login_at', $sortDirection);
        } else {
            $query->orderBy("users.{$sortField}", $sortDirection);
        }

        // Get users with pagination
        $users = $query->paginate($request->get('per_page', 15));

        // Transform user data
        $transformedUsers = $users->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'role' => $user->role_name,
                'status' => $this->getUserStatus($user),
                'last_login' => $user->last_login_at,
                'created_at' => $user->created_at,
                'avatar' => $user->avatar_url ?? null,
                'restaurants' => $user->restaurants->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'name' => $restaurant->name,
                    ];
                }),
            ];
        });

        // Replace the collection
        $users->setCollection($transformedUsers);

        // Calculate statistics
        $statistics = $this->calculateUserStatistics();

        return Inertia::render('User/Index', [
            'users' => $transformedUsers,
            'statistics' => $statistics,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'status' => $request->status,
                'sort' => $sortField,
                'direction' => $sortDirection,
            ]
        ]);
    }

    /**
     * Get user status based on various conditions
     */
    private function getUserStatus($user)
    {
        if (is_null($user->email_verified_at)) {
            return 'pending';
        }
        
        if (isset($user->is_active) && !$user->is_active) {
            return 'inactive';
        }
        
        return 'active';
    }

    /**
     * Calculate user statistics for dashboard
     */
    private function calculateUserStatistics()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', 1)
            ->whereNotNull('email_verified_at')
            ->count();
        $pendingUsers = User::whereNull('email_verified_at')->count();
        $adminUsers = User::role('admin')->count();

        return [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'pendingUsers' => $pendingUsers,
            'adminUsers' => $adminUsers,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Define role display names with short letters
        $roleNames = [
            'area_manager' => 'AM - Area Manager',
            'coo' => 'COO - Chief Operations Officer',
            'om' => 'OM - Operations Manager',
            'rm' => 'RM - Restaurant Manager',
            'sm' => 'SM - Store Manager',
            'training' => 'TM - Training Manager'
        ];

        // Get roles excluding admin, ordered alphabetically (same as permission management)
        $roles = Role::whereNotIn('name', ['admin'])
            ->orderBy('name')
            ->get()
            ->map(function ($role) use ($roleNames) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $roleNames[$role->name] ?? ucwords(str_replace('_', ' ', $role->name))
                ];
            });

        $restaurants = Restaurant::all()->map(function ($restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name
            ];
        });

        return Inertia::render('User/Create', [
            'roles' => $roles,
            'restaurants' => $restaurants
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name|not_in:admin',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|in:management,operations,quality,kitchen,service,training,finance,hr',
            'restaurant_ids' => 'nullable|array',
            'restaurant_ids.*' => 'exists:restaurants,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // Assign role
        $user->assignRole($request->role);

        // Assign restaurants if provided
        if ($request->restaurant_ids && count($request->restaurant_ids) > 0) {
            $user->restaurants()->attach($request->restaurant_ids);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['roles', 'restaurants'])->findOrFail($id);
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'employee_id' => $user->employee_id,
            'department' => $user->department,
            'is_active' => $user->is_active ?? true,
            'email_verified_at' => $user->email_verified_at,
            'role' => $user->roles->first()?->name ?? null,
            'role_display' => $user->roles->first()?->name ? ucwords(str_replace('_', ' ', $user->roles->first()->name)) : 'No Role',
            'restaurants' => $user->restaurants->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'code' => $restaurant->code ?? 'N/A',
                    'brand' => $restaurant->brand ?? 'N/A',
                ];
            }),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'last_login_at' => $user->last_login_at ?? null,
        ];

        return Inertia::render('User/Show', [
            'user' => $userData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['roles', 'restaurants'])->findOrFail($id);
        
        // Define role display names with short letters
        $roleNames = [
            'area_manager' => 'AM - Area Manager',
            'coo' => 'COO - Chief Operations Officer',
            'om' => 'OM - Operations Manager',
            'rm' => 'RM - Restaurant Manager',
            'sm' => 'SM - Store Manager',
            'training' => 'TM - Training Manager'
        ];

        // Get roles excluding admin, ordered alphabetically (same as permission management)
        $roles = Role::whereNotIn('name', ['admin'])
            ->orderBy('name')
            ->get()
            ->map(function ($role) use ($roleNames) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $roleNames[$role->name] ?? ucwords(str_replace('_', ' ', $role->name))
                ];
            });

        $restaurants = Restaurant::all()->map(function ($restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name
            ];
        });

        // Get users assigned to this area manager (if this user is an area manager)
        $assignedUsers = [];
        if ($user->hasRole('Area Manager')) {
            // Get restaurants managed by this area manager
            $managedRestaurantIds = $user->managedRestaurants()->pluck('id')->toArray();
            
            // Get users assigned to these restaurants
            $assignedUsers = User::with('roles')
                ->whereHas('restaurants', function($query) use ($managedRestaurantIds) {
                    $query->whereIn('restaurants.id', $managedRestaurantIds);
                })
                ->where('id', '!=', $user->id) // Exclude the area manager themselves
                ->get()
                ->map(function ($assignedUser) {
                    return [
                        'id' => $assignedUser->id,
                        'name' => $assignedUser->name,
                        'email' => $assignedUser->email,
                        'role' => $assignedUser->roles->first()->name ?? 'No Role'
                    ];
                });
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'employee_id' => $user->employee_id,
            'department' => $user->department,
            'role' => $user->roles->first()->name ?? null,
            'restaurant_ids' => $user->restaurants->pluck('id')->toArray(),
            'managed_restaurant_ids' => $user->managedRestaurants()->pluck('id')->toArray(),
            'area_manager_ids' => $user->assignedAreaManagers()->pluck('users.id')->toArray(),
        ];

        // Get all area managers for assignment
        $areaManagers = User::role('Area Manager')
            ->withCount('managedRestaurants')
            ->get()
            ->map(function ($manager) {
                return [
                    'id' => $manager->id,
                    'name' => $manager->name,
                    'restaurants_count' => $manager->managed_restaurants_count
                ];
            });

        return Inertia::render('User/Edit', [
            'user' => $userData,
            'roles' => $roles,
            'restaurants' => $restaurants,
            'assignedUsers' => $assignedUsers,
            'areaManagers' => $areaManagers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name|not_in:admin',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|in:management,operations,quality,kitchen,service,training,finance,hr',
            'restaurant_ids' => 'nullable|array',
            'restaurant_ids.*' => 'exists:restaurants,id',
            'managed_restaurant_ids' => 'nullable|array',
            'managed_restaurant_ids.*' => 'exists:restaurants,id',
            'area_manager_ids' => 'nullable|array',
            'area_manager_ids.*' => 'exists:users,id'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Update role
        $user->syncRoles([$request->role]);

        // Update restaurants
        if ($request->has('restaurant_ids')) {
            $user->restaurants()->sync($request->restaurant_ids ?? []);
        }

        // Update managed restaurants (for area managers)
        if ($request->has('managed_restaurant_ids')) {
            $managedRestaurantIds = $request->managed_restaurant_ids ?? [];
            
            // First, remove area_manager_id from all restaurants currently managed by this user
            Restaurant::where('area_manager_id', $user->id)->update(['area_manager_id' => null]);
            
            // Then, assign the new restaurants to this area manager
            if (!empty($managedRestaurantIds)) {
                Restaurant::whereIn('id', $managedRestaurantIds)->update(['area_manager_id' => $user->id]);
            }
        }

        // Update area manager assignments
        if ($request->has('area_manager_ids')) {
            $user->assignedAreaManagers()->sync($request->area_manager_ids ?? []);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Check if user is trying to delete themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Check if user is the last admin
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last administrator.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Check if user is trying to deactivate themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Approve pending user
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'email_verified_at' => now(),
            'is_active' => true
        ]);

        return back()->with('success', 'User approved successfully.');
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,approve',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        // Prevent self-action
        if (in_array(auth()->id(), $userIds)) {
            return back()->with('error', 'You cannot perform bulk actions on your own account.');
        }

        switch ($action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = 'Users activated successfully.';
                break;
                
            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = 'Users deactivated successfully.';
                break;
                
            case 'approve':
                User::whereIn('id', $userIds)->update([
                    'email_verified_at' => now(),
                    'is_active' => true
                ]);
                $message = 'Users approved successfully.';
                break;
                
            case 'delete':
                // Check if any admin users would be deleted
                $adminCount = User::role('admin')->count();
                $adminsToDelete = User::whereIn('id', $userIds)->role('admin')->count();
                
                if ($adminCount - $adminsToDelete < 1) {
                    return back()->with('error', 'Cannot delete all administrators.');
                }
                
                User::whereIn('id', $userIds)->delete();
                $message = 'Users deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::with(['roles', 'restaurants']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 
                'Department', 'Employee ID', 'Restaurants', 'Last Login', 'Created At'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->roles->first()->name ?? 'No Role',
                    $this->getUserStatus($user),
                    $user->department,
                    $user->employee_id,
                    $user->restaurants->pluck('name')->join(', '),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
