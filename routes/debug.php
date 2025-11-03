<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-permissions', function () {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    // Get the same data that HandleInertiaRequests would send
    $authData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'roles' => $user->roles,
        'restaurants' => $user->restaurants,
        'permissions' => app(\App\Http\Middleware\HandleInertiaRequests::class)
            ->getUserPermissions($user),
    ];
    
    return response()->json([
        'auth' => [
            'user' => $authData
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware('auth');