<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Test form to upload images
Route::get('/test-image-form', function() {
    return view('test-image-form');
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Test handler to process uploaded images
Route::post('/test-image-handler', function(\Illuminate\Http\Request $request) {
    $user = \App\Models\User::first();
    
    $storeVisit = new \App\Models\StoreVisit();
    $storeVisit->user_id = $user->id;
    $storeVisit->restaurant_name = $request->input('restaurant_name');
    $storeVisit->mic = $request->input('mic');
    $storeVisit->visit_date = $request->input('visit_date');
    $storeVisit->purpose_of_visit = json_encode($request->input('purpose_of_visit', []));
    $storeVisit->status = $request->input('action') === 'submit' ? 'submitted' : 'draft';
    
    // Add all question responses
    $questions = [
        'oca_board_followed', 'staff_know_duty', 'coaching_directing',
        'smile_greetings', 'suggestive_selling', 'offer_promotion',
        'thank_direction', 'team_work_hustle', 'order_accuracy',
        'service_time', 'dine_in', 'take_out', 'external_clean',
        'internal_clean', 'kitchen_clean', 'staff_grooming',
        'stock_fresh', 'cooling_equipment', 'manager_office',
        'updated_schedule', 'food_waste'
    ];
    
    foreach ($questions as $question) {
        $storeVisit->$question = $request->input($question, 1);
        $commentField = $question . '_comments';
        $storeVisit->$commentField = $request->input($commentField, 'Test comment');
    }
    
    $storeVisit->what_did_you_see = $request->input('what_did_you_see');
    $storeVisit->why_had_issue = $request->input('why_had_issue');
    $storeVisit->how_to_improve = $request->input('how_to_improve');
    $storeVisit->who_when_responsible = $request->input('who_when_responsible');
    
    $storeVisit->save();
    
    $result = ['visit_id' => $storeVisit->id, 'images' => []];
    
    // Handle image uploads
    if ($request->hasFile('question_images')) {
        foreach ($request->file('question_images') as $fieldName => $images) {
            if (!is_array($images)) {
                $images = [$images];
            }
            
            foreach ($images as $image) {
                if ($image->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('action-plans', $filename, 'public');
                    
                    $result['images'][] = [
                        'field' => $fieldName,
                        'original_name' => $image->getClientOriginalName(),
                        'stored_path' => $path,
                        'size' => $image->getSize()
                    ];
                }
            }
        }
    }
    
    return view('test-image-result', compact('result', 'storeVisit'));
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Test upload endpoint (no CSRF for testing)
Route::post('/test-upload-endpoint', function(\Illuminate\Http\Request $request) {
    \Log::info('=== TEST UPLOAD ENDPOINT ===', [
        'has_question_images' => $request->has('question_images'),
        'all_files' => array_keys($request->allFiles()),
        'question_images_files' => isset($request->allFiles()['question_images']) ? array_keys($request->allFiles()['question_images']) : 'none'
    ]);
    
    $result = [
        'success' => true,
        'has_question_images' => $request->has('question_images'),
        'files_received' => []
    ];
    
    if (isset($request->allFiles()['question_images'])) {
        foreach ($request->allFiles()['question_images'] as $fieldName => $filesArray) {
            if (!is_array($filesArray)) {
                $filesArray = [$filesArray];
            }
            foreach ($filesArray as $file) {
                $result['files_received'][] = [
                    'field' => $fieldName,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ];
            }
        }
    }
    
    return response()->json($result);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Test route for debugging Inertia data
Route::get('/test-inertia-data', function () {
    return Inertia::render('TestInertiaData');
})->middleware(['auth', 'verified']);

// Root redirect to QMS
Route::get('/', function () {
    return redirect('/qms');
});

// All routes under 'qms' prefix
Route::prefix('qms')->group(function () {
    Route::get('/', function () {
        // Redirect to login if not authenticated, otherwise to dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    });


    // Temperature Tracking with permission middleware
    Route::get('/temperature-tracking/export', [App\Http\Controllers\TemperatureTrackingController::class, 'export'])
        ->middleware('page_permission:temperature_tracking,view')
        ->name('temperature-tracking.export');
    Route::resource('temperature-tracking', App\Http\Controllers\TemperatureTrackingController::class)
        ->middleware('page_permission:temperature_tracking,view');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // Q.S.C CHECKLIST routes with permission middleware
        Route::prefix('qsc-checklist')->name('qsc-checklist.')->group(function () {
            Route::get('/', [\App\Http\Controllers\QscChecklistController::class, 'index'])
                ->middleware('page_permission:qsc_checklist,view')
                ->name('index');
            Route::get('/form', [\App\Http\Controllers\QscChecklistController::class, 'create'])
                ->middleware('page_permission:qsc_checklist,create')
                ->name('form');
            Route::post('/store', [\App\Http\Controllers\QscChecklistController::class, 'store'])
                ->middleware(['page_permission:qsc_checklist,create', 'restaurant_access', 'validate_location'])
                ->name('store');
            Route::get('/report', [\App\Http\Controllers\QscChecklistController::class, 'index'])
                ->middleware('page_permission:qsc_checklist,view')
                ->name('report');
            Route::get('/export', [\App\Http\Controllers\QscChecklistController::class, 'export'])
                ->middleware('page_permission:qsc_checklist,view')
                ->name('export');
            Route::get('/export/pdf', [\App\Http\Controllers\QscChecklistController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/export/csv', [\App\Http\Controllers\QscChecklistController::class, 'exportCsv'])->name('export-csv');
            Route::post('/{qscChecklist}/confirm', [\App\Http\Controllers\QscChecklistController::class, 'confirm'])
                ->middleware('page_permission:qsc_checklist,approve')
                ->name('confirm');
            Route::get('/{qscChecklist}', [\App\Http\Controllers\QscChecklistController::class, 'show'])
                ->middleware('page_permission:qsc_checklist,view')
                ->name('show');
            Route::get('/{qscChecklist}/edit', [\App\Http\Controllers\QscChecklistController::class, 'edit'])
                ->middleware('page_permission:qsc_checklist,edit')
                ->name('edit');
            Route::put('/{qscChecklist}', [\App\Http\Controllers\QscChecklistController::class, 'update'])
                ->middleware(['page_permission:qsc_checklist,edit', 'restaurant_access', 'validate_location'])
                ->name('update');
            Route::delete('/{qscChecklist}', [\App\Http\Controllers\QscChecklistController::class, 'destroy'])
                ->middleware('page_permission:qsc_checklist,delete')
                ->name('destroy');
        });

        // TTF page
        Route::get('/ttf', function () {
            return Inertia::render('TtfIndex');
        })->name('ttf.index');

    // Settings routes (Admin only)
    Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\SettingsController::class, 'users'])->name('users');
    });

    // Permission Management routes (Admin only)
    Route::middleware('role:admin')->prefix('permission-management')->name('permission-management.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PermissionManagementController::class, 'index'])->name('index');
        Route::post('/role/{roleId}', [\App\Http\Controllers\PermissionManagementController::class, 'updateRolePermissions'])->name('update-role');
        Route::get('/role/{roleId}', [\App\Http\Controllers\PermissionManagementController::class, 'getRolePermissions'])->name('get-role');
        Route::put('/page/{pageId}', [\App\Http\Controllers\PermissionManagementController::class, 'updatePage'])->name('update-page');
    });

    // User Management routes (Admin only with permissions)
    Route::middleware(['role:admin', 'page_permission:users,view'])->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\UserManagementController::class, 'create'])
            ->middleware('page_permission:users,create')
            ->name('users.create');
        Route::post('/users', [\App\Http\Controllers\UserManagementController::class, 'store'])
            ->middleware('page_permission:users,create')
            ->name('users.store');
        Route::get('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'show'])
            ->middleware('page_permission:users,view')
            ->name('users.show');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\UserManagementController::class, 'edit'])
            ->middleware('page_permission:users,edit')
            ->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'update'])
            ->middleware('page_permission:users,edit')
            ->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'destroy'])
            ->middleware('page_permission:users,delete')
            ->name('users.destroy');
        
        // Enhanced user management routes
        Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/approve', [\App\Http\Controllers\UserManagementController::class, 'approve'])->name('users.approve');
        Route::post('/users/bulk-action', [\App\Http\Controllers\UserManagementController::class, 'bulkAction'])->name('users.bulk-action');
        Route::get('/users/export', [\App\Http\Controllers\UserManagementController::class, 'export'])->name('users.export');
    });
    
    // Restaurant Management routes (Admin only with permissions)
    Route::middleware(['role:admin', 'page_permission:restaurants,view'])->group(function () {
        Route::get('/restaurants', [\App\Http\Controllers\RestaurantController::class, 'index'])->name('restaurants.index');
        Route::get('/restaurants/create', [\App\Http\Controllers\RestaurantController::class, 'create'])
            ->middleware('page_permission:restaurants,create')
            ->name('restaurants.create');
        Route::post('/restaurants', [\App\Http\Controllers\RestaurantController::class, 'store'])
            ->middleware('page_permission:restaurants,create')
            ->name('restaurants.store');
        Route::get('/restaurants/{restaurant}', [\App\Http\Controllers\RestaurantController::class, 'show'])
            ->name('restaurants.show');
        Route::get('/restaurants/{restaurant}/edit', [\App\Http\Controllers\RestaurantController::class, 'edit'])
            ->middleware('page_permission:restaurants,edit')
            ->name('restaurants.edit');
        Route::put('/restaurants/{restaurant}', [\App\Http\Controllers\RestaurantController::class, 'update'])
            ->middleware('page_permission:restaurants,edit')
            ->name('restaurants.update');
        Route::delete('/restaurants/{restaurant}', [\App\Http\Controllers\RestaurantController::class, 'destroy'])
            ->middleware('page_permission:restaurants,delete')
            ->name('restaurants.destroy');
        
        // Bulk actions for restaurants
        Route::post('/restaurants/bulk-action', [\App\Http\Controllers\RestaurantController::class, 'bulkAction'])
            ->middleware('page_permission:restaurants,edit')
            ->name('restaurants.bulk-action');
        
        // Enhanced restaurant management routes
        Route::post('/restaurants/check-code', [\App\Http\Controllers\RestaurantController::class, 'checkCodeAvailability'])->name('restaurants.check-code');
    });
    
    // API endpoints for dropdowns
    Route::get('/api/restaurants', [\App\Http\Controllers\RestaurantController::class, 'getRestaurants'])->name('api.restaurants');

    // Store Visit routes
    Route::prefix('store-visits')->name('store-visits.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StoreVisitController::class, 'index'])
            ->middleware('page_permission:store_visits,view')
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\StoreVisitController::class, 'create'])
            ->middleware('page_permission:store_visits,create')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\StoreVisitController::class, 'store'])
            ->middleware(['page_permission:store_visits,create', 'restaurant_access', 'validate_location'])
            ->name('store');
        Route::get('/{visit}', [\App\Http\Controllers\StoreVisitController::class, 'show'])
            ->middleware('page_permission:store_visits,view')
            ->name('show');
        Route::get('/{visit}/edit', [\App\Http\Controllers\StoreVisitController::class, 'edit'])
            ->middleware('page_permission:store_visits,edit')
            ->name('edit');
        Route::put('/{visit}', [\App\Http\Controllers\StoreVisitController::class, 'update'])
            ->middleware(['page_permission:store_visits,edit', 'restaurant_access', 'validate_location'])
            ->name('update');
        
        // NEW WORKFLOW ROUTES
        Route::post('/{visit}/submit-visit', [\App\Http\Controllers\StoreVisitController::class, 'submitVisit'])->name('submit-visit');
        Route::post('/{visit}/submit', [\App\Http\Controllers\StoreVisitController::class, 'submit'])->name('submit');
        Route::post('/confirm-action-plans', [\App\Http\Controllers\StoreVisitController::class, 'confirmAndCreateActionPlans'])->name('confirm-action-plans');
        Route::post('/bulk-confirm-action-plans', [\App\Http\Controllers\StoreVisitController::class, 'bulkConfirmActionPlans'])->name('bulk-confirm-action-plans');
        Route::get('/{visit}/print-report', [\App\Http\Controllers\StoreVisitController::class, 'printReport'])->name('print-report');
        Route::get('/{visit}/test-confirm', function($visit) {
            $storeVisit = \App\Models\StoreVisit::findOrFail($visit);
            $allActionPlans = \DB::table('action_plans')->where('store_visit_id', $visit)->get();
            
            // Check some sample "No" answers
            $noAnswers = [];
            if ($storeVisit->oca_board_followed === false) $noAnswers[] = 'OCA Board: No';
            if ($storeVisit->staff_know_duty === false) $noAnswers[] = 'Staff Duty: No';
            if ($storeVisit->smile_greetings === false) $noAnswers[] = 'Smile Greetings: No';
            if ($storeVisit->cash_policies === false) $noAnswers[] = 'Cash Policies: No';
            
            return "Visit ID: {$storeVisit->id}, Status: {$storeVisit->status}, Score: {$storeVisit->score}%, Action Items: " . count($storeVisit->getActionItems()) . ", Sample No Answers: " . implode(', ', $noAnswers) . ", DB Action Plans: " . $allActionPlans->count();
        })->name('test-confirm');
        
        Route::get('/debug-visits', function() {
            $visits = \App\Models\StoreVisit::orderBy('id', 'desc')->take(5)->get(['id', 'restaurant_name', 'status', 'score', 'created_at']);
            $output = "Recent Visits:\n";
            foreach($visits as $visit) {
                $output .= "ID: {$visit->id}, Restaurant: {$visit->restaurant_name}, Status: {$visit->status}, Score: {$visit->score}%\n";
            }
            return nl2br($output);
        })->name('debug-visits');
        
        Route::get('/{visit}/quick-confirm', [\App\Http\Controllers\StoreVisitController::class, 'quickConfirm'])->name('quick-confirm');
        
        Route::post('/test-confirm-post', function(\Illuminate\Http\Request $request) {
            \Log::info('Test confirm route called', ['data' => $request->all()]);
            return response()->json(['success' => true, 'message' => 'Test route working']);
        })->name('test-confirm-post');
        
        Route::get('/{visit}/force-create-plans', function($visit) {
            $storeVisit = \App\Models\StoreVisit::findOrFail($visit);
            $actionItems = $storeVisit->getActionItems();
            
            if (count($actionItems) > 0) {
                // Complete action plan data
                $data = [];
                foreach ($actionItems as $item) {
                    $data[] = [
                        'store_visit_id' => $storeVisit->id,
                        'item' => $item['question'],
                        'issue' => $item['question'] . ' - Issue identified during visit',
                        'action_required' => 'Improve: ' . $item['question'],
                        'priority' => 'Medium',
                        'status' => 'Pending',
                        'assigned_to' => null,
                        'due_date' => null,
                        'notes' => $item['comment'] ?: 'Action required based on store visit findings',
                        'visit_purpose' => 'Quality Audit',
                        'area_manager' => auth()->user()->name ?? 'System',
                        'restaurant_manager' => 'To be assigned',
                        'what' => $item['question'] . ' needs improvement',
                        'why' => 'Non-compliance identified during quality visit',
                        'when_detail' => 'During visit on ' . $storeVisit->visit_date,
                        'where' => $storeVisit->restaurant_name,
                        'who' => 'Restaurant staff and management',
                        'how' => 'Follow standard operating procedures',
                        'is_approved' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                \DB::table('action_plans')->insert($data);
                $storeVisit->update(['status' => 'Confirmed']);
                
                return redirect('/qms/action-plans')->with('success', count($actionItems) . ' action plans created!');
            }
            
            return 'No action items found';
        })->name('force-create-plans');
        Route::get('/{visit}/review', [\App\Http\Controllers\StoreVisitController::class, 'review'])->name('review');
        Route::get('/qms-dashboard', [\App\Http\Controllers\StoreVisitController::class, 'qmsDashboard'])->name('qms-dashboard');
        
        Route::post('/bulk-update', [\App\Http\Controllers\StoreVisitController::class, 'bulkUpdate'])
            ->middleware('page_permission:store_visits,approve')
            ->name('bulk-update');
        Route::get('/export-pdf', [\App\Http\Controllers\StoreVisitController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [\App\Http\Controllers\StoreVisitController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{visit}/export-pdf', [\App\Http\Controllers\StoreVisitController::class, 'exportSinglePdf'])->name('export-single-pdf');
        Route::get('/{visit}', [\App\Http\Controllers\StoreVisitController::class, 'show'])->name('show');
    });

    // Action Plan routes with permission middleware
    Route::prefix('action-plans')->name('action-plans.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ActionPlanController::class, 'index'])
            ->middleware('page_permission:action_plans,view')
            ->name('index');
        Route::put('/{actionPlan}', [\App\Http\Controllers\ActionPlanController::class, 'update'])
            ->middleware('page_permission:action_plans,edit')
            ->name('update');
        Route::post('/bulk-update', [\App\Http\Controllers\ActionPlanController::class, 'bulkUpdate'])
            ->middleware('page_permission:action_plans,approve')
            ->name('bulk-update');
        Route::get('/export-excel', [\App\Http\Controllers\ActionPlanController::class, 'exportExcel'])
            ->middleware('page_permission:action_plans,view')
            ->name('export-excel');
    });


    
    }); // Close auth/verified middleware group

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Debug route for testing permissions
    Route::get('/debug-permissions', function () {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        // Get permissions using the same logic as HandleInertiaRequests
        $permissionsData = [];
        
        foreach ($user->roles as $role) {
            $rolePermissions = \App\Models\RolePagePermission::with('pagePermission')
                ->where('role_id', $role->id)
                ->get();
            
            foreach ($rolePermissions as $rolePermission) {
                $pageKey = $rolePermission->pagePermission->page_key;
                
                if (!isset($permissionsData[$pageKey])) {
                    $permissionsData[$pageKey] = [
                        'can_view' => false,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'can_approve' => false,
                    ];
                }
                
                $permissionsData[$pageKey]['can_view'] = $permissionsData[$pageKey]['can_view'] || $rolePermission->can_view;
                $permissionsData[$pageKey]['can_create'] = $permissionsData[$pageKey]['can_create'] || $rolePermission->can_create;
                $permissionsData[$pageKey]['can_edit'] = $permissionsData[$pageKey]['can_edit'] || $rolePermission->can_edit;
                $permissionsData[$pageKey]['can_delete'] = $permissionsData[$pageKey]['can_delete'] || $rolePermission->can_delete;
                $permissionsData[$pageKey]['can_approve'] = $permissionsData[$pageKey]['can_approve'] || $rolePermission->can_approve;
            }
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles,
                'permissions' => $permissionsData
            ],
            'can_action_plans_view' => isset($permissionsData['action_plans']) && $permissionsData['action_plans']['can_view'],
            'frontend_test' => [
                'permissions_exists' => isset($permissionsData['action_plans']),
                'can_view_value' => $permissionsData['action_plans']['can_view'] ?? 'NOT_SET'
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    })->middleware('auth');

    // Test permissions page
    Route::get('/test-permissions-page', function () {
        return Inertia::render('TestPermissions');
    })->middleware('auth');

    // Simple debug route for JSON data
    Route::get('/debug-data', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $middleware = new \App\Http\Middleware\HandleInertiaRequests();
        $request = request();
        $sharedData = $middleware->share($request);
        
        return response()->json($sharedData, 200, [], JSON_PRETTY_PRINT);
    })->middleware('auth');

    // Simple HTML debug page
    Route::get('/debug-html', function () {
        $user = auth()->user();
        if (!$user) {
            return 'Not authenticated';
        }
        
        $middleware = new \App\Http\Middleware\HandleInertiaRequests();
        $request = request();
        $sharedData = $middleware->share($request);
        
        $actionPlansPermissions = $sharedData['auth']['user']['permissions']['action_plans'] ?? null;
        
        $html = '
        <h1>Debug User Permissions</h1>
        <h2>User: ' . $user->name . ' (' . $user->email . ')</h2>
        
        <h3>Action Plans Permissions:</h3>';
        
        if ($actionPlansPermissions) {
            $html .= '<ul>
                <li>Can View: ' . ($actionPlansPermissions['can_view'] ? 'YES' : 'NO') . '</li>
                <li>Can Create: ' . ($actionPlansPermissions['can_create'] ? 'YES' : 'NO') . '</li>
                <li>Can Edit: ' . ($actionPlansPermissions['can_edit'] ? 'YES' : 'NO') . '</li>
            </ul>';
            
            if ($actionPlansPermissions['can_view']) {
                $html .= '<div style="background: green; color: white; padding: 10px; margin: 10px 0;">
                    ✅ Action Plans SHOULD appear in navigation
                </div>';
            } else {
                $html .= '<div style="background: red; color: white; padding: 10px; margin: 10px 0;">
                    ❌ Action Plans will NOT appear in navigation
                </div>';
            }
        } else {
            $html .= '<div style="background: red; color: white; padding: 10px; margin: 10px 0;">
                ❌ Action Plans permissions NOT found!
            </div>';
        }
        
        $html .= '<h3>All Permissions:</h3>
        <pre>' . json_encode($sharedData['auth']['user']['permissions'], JSON_PRETTY_PRINT) . '</pre>';
        
        return $html;
    })->middleware('auth');

    // Include auth routes within the qms prefix
    require __DIR__.'/auth.php';
}); // Close qms prefix group
require __DIR__.'/test-upload.php';
