<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Restaurant;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('is_active', true)->count(),
            'inactive_restaurants' => Restaurant::where('is_active', false)->count(),
            'total_roles' => Role::count(),
            'admin_users' => User::role('admin')->count(),
            'area_managers' => User::role('area_manager')->count(),
            'store_managers' => User::role('sm')->count(),
        ];

        $recentUsers = User::with('roles')->latest()->take(8)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at->format('M j, Y'),
            ];
        });

        $recentRestaurants = Restaurant::latest()->take(8)->get()->map(function ($restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'code' => $restaurant->code,
                'location' => $restaurant->location,
                'phone' => $restaurant->phone,
                'is_active' => $restaurant->is_active,
                'status' => $restaurant->is_active ? 'Active' : 'Inactive',
                'created_at' => $restaurant->created_at->format('M j, Y'),
            ];
        });

        // Restaurant statistics by location
        $locationStats = Restaurant::selectRaw('
            CASE 
                WHEN city LIKE "%Riyadh%" THEN "Riyadh"
                WHEN city LIKE "%Jeddah%" THEN "Jeddah" 
                WHEN city LIKE "%Dammam%" THEN "Dammam"
                WHEN city LIKE "%Medina%" THEN "Medina"
                WHEN city LIKE "%Mecca%" THEN "Mecca"
                WHEN city LIKE "%Yanbu%" THEN "Yanbu"
                WHEN city LIKE "%Taif%" THEN "Taif"
                WHEN city LIKE "%Abha%" THEN "Abha"
                ELSE "Other"
            END as city_group,
            COUNT(*) as count
        ')
        ->groupByRaw('
            CASE 
                WHEN city LIKE "%Riyadh%" THEN "Riyadh"
                WHEN city LIKE "%Jeddah%" THEN "Jeddah" 
                WHEN city LIKE "%Dammam%" THEN "Dammam"
                WHEN city LIKE "%Medina%" THEN "Medina"
                WHEN city LIKE "%Mecca%" THEN "Mecca"
                WHEN city LIKE "%Yanbu%" THEN "Yanbu"
                WHEN city LIKE "%Taif%" THEN "Taif"
                WHEN city LIKE "%Abha%" THEN "Abha"
                ELSE "Other"
            END
        ')
        ->orderBy('count', 'desc')
        ->get();

        return Inertia::render('Settings/Index', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentRestaurants' => $recentRestaurants,
            'locationStats' => $locationStats,
        ]);
    }

    public function users()
    {
        $users = User::with('roles', 'restaurants')->paginate(10);
        $roles = Role::all();
        $restaurants = Restaurant::orderBy('name')->get();

        return Inertia::render('Settings/Users', [
            'users' => $users,
            'roles' => $roles,
            'restaurants' => $restaurants,
        ]);
    }
}
