<?php

namespace App\Http\Controllers;

use App\Models\StoreVisit;
use App\Models\Restaurant;
use App\Models\ActionPlan;
use App\Models\ActionPlanImage;
use App\Models\User;
use App\Exports\StoreVisitsExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StoreVisitController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->roles && (
            $user->roles->contains('name', 'admin') || 
            $user->roles->contains('name', 'Super Admin')
        );
        
        // Get user's assigned restaurants
        $assignedRestaurants = [];
        if (!$isAdmin) {
            $assignedRestaurants = $user->restaurants()->pluck('restaurants.id')->toArray();
        }
        
        // Build query for store visits with optimized relationships
        $query = StoreVisit::with(['user.roles', 'actionPlans']);
        
        // Restrict to assigned restaurants for non-admin users
        if (!$isAdmin && !empty($assignedRestaurants)) {
            $assignedRestaurantNames = \App\Models\Restaurant::whereIn('id', $assignedRestaurants)
                ->pluck('name')
                ->toArray();
            
            if (!empty($assignedRestaurantNames)) {
                $query->whereIn('restaurant_name', $assignedRestaurantNames);
            }
        }
        
        // Apply filters
        // Multi-restaurant filter
        if ($request->filled('restaurants') && is_array($request->restaurants)) {
            $query->whereIn('restaurant_name', $request->restaurants);
        } elseif ($request->filled('restaurant')) {
            $query->where('restaurant_name', 'LIKE', '%' . $request->restaurant . '%');
        }
        
        // Area manager filter - filter by restaurants assigned to the area manager
        if ($request->filled('area_manager')) {
            $areaManagerId = $request->area_manager;
            
            // Get restaurants assigned to this area manager
            $areaManagerUser = User::find($areaManagerId);
            if ($areaManagerUser) {
                $assignedRestaurants = $areaManagerUser->restaurants->pluck('name')->toArray();
                if (!empty($assignedRestaurants)) {
                    $query->whereIn('restaurant_name', $assignedRestaurants);
                } else {
                    // If area manager has no assigned restaurants, show nothing
                    $query->whereRaw('1 = 0');
                }
            }
        }
        
        if ($request->filled('mic')) {
            $query->where('mic', $request->mic);
        }
        
        if ($request->filled('date_from')) {
            $query->where('visit_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('visit_date', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('restaurant_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('general_comments', 'LIKE', '%' . $search . '%')
                  ->orWhere('what_did_you_see', 'LIKE', '%' . $search . '%');
            });
        }
        
        if ($request->filled('purpose')) {
            $query->whereJsonContains('purpose_of_visit', $request->purpose);
        }
        
        if ($request->filled('score_min')) {
            $query->whereRaw('COALESCE(score, 0) >= ?', [$request->score_min]);
        }
        
        if ($request->filled('score_max')) {
            $query->whereRaw('COALESCE(score, 0) <= ?', [$request->score_max]);
        }
        
        // Get visits
        $visits = $query->latest('visit_date')->get()->map(function ($visit) {
            $actionItems = $visit->getActionItems();
            $existingActionPlans = $visit->actionPlans->count();
            
            return [
                'id' => $visit->id,
                'restaurant' => $visit->restaurant_name,
                'mic' => $visit->mic,
                'date' => $visit->visit_date->format('d/m/Y'),
                'score' => $visit->score ?? 0,
                'purpose' => is_array($visit->purpose_of_visit) ? implode(', ', $visit->purpose_of_visit) : $visit->purpose_of_visit,
                'status' => $visit->status,
                'actionItems' => count($actionItems),
                'existingActionPlans' => $existingActionPlans,
                'hasIssues' => count($actionItems) > 0,
                'needsActionPlans' => count($actionItems) > 0 && $existingActionPlans === 0,
                'issuesSummary' => count($actionItems) > 0 ? count($actionItems) . ' issue' . (count($actionItems) > 1 ? 's' : '') . ' found' : 'No issues found',
                'created_by' => $visit->user->name ?? 'Unknown',
                'created_by_role' => $visit->user->roles?->first()?->name ?? 'User',
                'created_at' => $visit->created_at->format('M j, Y g:i A')
            ];
        });
        
        // Get restaurants for filter - based on user access
        $restaurantsQuery = \App\Models\Restaurant::where('is_active', true);
        
        if (!$isAdmin && !empty($assignedRestaurants)) {
            $restaurantsQuery->whereIn('id', $assignedRestaurants);
        }
        
        $restaurants = $restaurantsQuery->select('id', 'name', 'code', 'location')
                                        ->orderBy('code')
                                        ->get();
        
        // Get area managers (based on permission and assigned area managers)
        $areaManagers = [];
        $canViewAreaManagerFilter = $user->can('view_area_manager_filter');
        
        if ($canViewAreaManagerFilter) {
            if ($isAdmin) {
                // Admin can see all area managers
                $areaManagers = User::role('Area Manager')
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();
            } else {
                // Non-admin users can only see their assigned area managers
                $areaManagers = $user->assignedAreaManagers()
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();
            }
        }
        
        return Inertia::render('StoreVisit/Index', [
            'visits' => [
                'data' => $visits,
                'total' => $visits->count(),
                'current_page' => 1,
                'per_page' => 50,
                'last_page' => 1
            ],
            'restaurants' => $restaurants,
            'areaManagers' => $areaManagers,
            'assignedRestaurants' => !$isAdmin ? $assignedRestaurants : [],
            'filters' => $request->only(['restaurant', 'restaurants', 'area_manager', 'mic', 'date_from', 'date_to', 'status', 'search', 'purpose', 'score_min', 'score_max']),
            'isAdmin' => $isAdmin,
            'canViewAreaManagerFilter' => $canViewAreaManagerFilter,
            'canExportAll' => $isAdmin,
            'canViewAllRestaurants' => $isAdmin,
            'csrf_token' => csrf_token()
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        // Get restaurants - admin sees all, users see only assigned ones
        $restaurantsQuery = \App\Models\Restaurant::where('is_active', true);
        
        if (!$isAdmin) {
            // Filter by user's assigned restaurants
            $restaurantsQuery->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        
        $restaurants = $restaurantsQuery->select('id', 'name', 'code', 'location', 'latitude', 'longitude')
                                        ->orderBy('code')
                                        ->get();
        
        return Inertia::render('StoreVisit/Create', [
            'restaurants' => $restaurants,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|string|max:255',
            'mic' => 'required|in:Morning,Evening',
            'visit_date' => 'required|date',
            'purpose_of_visit' => 'required|array|min:1',
            'purpose_of_visit.*' => 'in:Quality Audit,Operational Assessment,Training Audit,Measurement & Coaching',
            
            // All boolean fields are nullable but must be boolean if provided
            'oca_board_followed' => 'nullable|boolean',
            'staff_know_duty' => 'nullable|boolean',
            'coaching_directing' => 'nullable|boolean',
            'smile_greetings' => 'nullable|boolean',
            'suggestive_selling' => 'nullable|boolean',
            'offer_promotion' => 'nullable|boolean',
            'thank_direction' => 'nullable|boolean',
            'team_work_hustle' => 'nullable|boolean',
            'order_accuracy' => 'nullable|boolean',
            'service_time' => 'nullable|boolean',
            'dine_in' => 'nullable|boolean',
            'take_out' => 'nullable|boolean',
            'family' => 'nullable|boolean',
            'delivery' => 'nullable|boolean',
            'drive_thru' => 'nullable|boolean',
            'weekly_schedule' => 'nullable|boolean',
            'mod_financial_goal' => 'nullable|boolean',
            'sales_objectives' => 'nullable|boolean',
            'cash_policies' => 'nullable|boolean',
            'daily_waste' => 'nullable|boolean',
            'ttf_followed' => 'nullable|boolean',
            'sandwich_assembly' => 'nullable|boolean',
            'qsc_completed' => 'nullable|boolean',
            'oil_standards' => 'nullable|boolean',
            'day_labels' => 'nullable|boolean',
            'equipment_working' => 'nullable|boolean',
            'fryer_condition' => 'nullable|boolean',
            'vegetable_prep' => 'nullable|boolean',
            'employee_appearance' => 'nullable|boolean',
            'equipment_wrapped' => 'nullable|boolean',
            'sink_setup' => 'nullable|boolean',
            'sanitizer_standard' => 'nullable|boolean',
            'dining_area_clean' => 'nullable|boolean',
            'restroom_clean' => 'nullable|boolean',
            
            'general_comments' => 'nullable|string',
            'status' => 'nullable|in:Draft,Submitted,Reviewed,Approved',
            
            // Location fields for verification
            'user_latitude' => 'nullable|numeric',
            'user_longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the store visit
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['status'] = $data['status'] ?? 'Submitted';

        $storeVisit = StoreVisit::create($data);
        
        // Calculate and update score
        $storeVisit->score = $storeVisit->calculateScore();
        $storeVisit->save();

        // Handle image uploads - now coming as flattened fields like question_images_fieldName_0
        $uploadedImages = [];
        
        // Get all request keys that start with 'question_images_'
        $imageKeys = array_filter(array_keys($request->all()), function($key) use ($request) {
            return strpos($key, 'question_images_') === 0 && $request->hasFile($key);
        });
        
        \Log::info('Image upload check', [
            'has_image_keys' => count($imageKeys) > 0,
            'image_keys' => $imageKeys,
            'all_file_keys' => array_keys($request->allFiles())
        ]);
        
        if (count($imageKeys) > 0) {
            foreach ($imageKeys as $key) {
                // Parse the key: question_images_fieldName_index
                preg_match('/^question_images_(.+)_(\d+)$/', $key, $matches);
                if (count($matches) === 3) {
                    $fieldName = $matches[1];
                    $index = $matches[2];
                    
                    $image = $request->file($key);
                    
                    // Generate unique filename
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    
                    // Store in storage/app/public/action-plans
                    $path = $image->storeAs('action-plans', $filename, 'public');
                    
                    // Ensure file is accessible via public storage link
                    $this->ensureImageAccessible($path);
                    
                    if (!isset($uploadedImages[$fieldName])) {
                        $uploadedImages[$fieldName] = [];
                    }
                    
                    $uploadedImages[$fieldName][] = [
                        'path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                    ];
                    
                    \Log::info('Image uploaded', [
                        'field' => $fieldName,
                        'index' => $index,
                        'filename' => $filename,
                        'original' => $image->getClientOriginalName()
                    ]);
                }
            }
            
            // Store uploaded images info in session for later linking to action plans
            if (count($uploadedImages) > 0) {
                session(['pending_action_plan_images' => [
                    'store_visit_id' => $storeVisit->id,
                    'images' => $uploadedImages
                ]]);
                
                \Log::info('Stored pending images in session', [
                    'store_visit_id' => $storeVisit->id,
                    'field_count' => count($uploadedImages),
                    'total_images' => array_sum(array_map('count', $uploadedImages))
                ]);
            }
        } else {
            \Log::info('No images in request', [
                'has_images_key' => false,
                'all_keys' => array_keys($request->all())
            ]);
        }

        // If status is Draft, redirect to index immediately
        if ($storeVisit->status === 'Draft') {
            return redirect()->route('store-visits.index')
                           ->with('success', 'Store visit report saved as draft successfully!');
        }

        // Always redirect to index after submission
        return redirect()->route('store-visits.index')
                       ->with('success', 'Store visit report created successfully!');
    }

    public function show($id)
    {
        $visit = StoreVisit::with(['actionPlans.images'])->findOrFail($id);
        
        // Check if this visit needs confirmation (has action items but no action plans created)
        $actionItems = $visit->getActionItems();
        $showConfirmation = (session('success') || session('confirm') || request()->has('confirm')) && 
                           count($actionItems) > 0 && 
                           $visit->actionPlans->isEmpty();
        
        return Inertia::render('StoreVisit/Show', [
            'visit' => $visit,
            'actionItemsPreview' => $actionItems,
            'showConfirmation' => $showConfirmation
        ]);
    }

    public function edit($id)
    {
        $visit = StoreVisit::findOrFail($id);
        
        // Check if user can edit this visit
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        if (!$isAdmin && $visit->user_id !== $user->id) {
            abort(403, 'You can only edit your own visits.');
        }
        
        // Get restaurants for dropdown
        $restaurants = Restaurant::where('is_active', true)
                                 ->select('id', 'name', 'code', 'location')
                                 ->orderBy('code')
                                 ->get();
        
        return Inertia::render('StoreVisit/Edit', [
            'visit' => $visit,
            'restaurants' => $restaurants
        ]);
    }

    public function update(Request $request, $id)
    {
        $visit = StoreVisit::findOrFail($id);
        
        // Check if user can update this visit
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        if (!$isAdmin && $visit->user_id !== $user->id) {
            abort(403, 'You can only update your own visits.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'mic' => 'required|in:Morning,Evening',
            'visit_date' => 'required|date',
            'purpose_of_visit' => 'required|array',
            // Add other validation rules as needed
        ]);
        
        // Update the visit
        $visit->update($validated);
        
        // Recalculate and update score
        $visit->score = $visit->calculateScore();
        $visit->save();
        
        return redirect()->route('store-visits.index')->with('success', 'Store visit updated successfully!');
    }

    public function actionPlans()
    {
        $restaurants = Restaurant::orderBy('name')->get();
        $visits = StoreVisit::with('restaurant')->latest()->get();
        
        return Inertia::render('StoreVisit/ActionPlan', [
            'restaurants' => $restaurants,
            'visits' => $visits,
            'visitId' => null, // For when not coming from specific visit
            'actionPlans' => [] // Will be populated with actual action plans later
        ]);
    }

    public function storeActionPlan(Request $request)
    {
        // Store action plan logic here
        return redirect()->route('store-visits.action-plans')->with('success', 'Action plan created successfully!');
    }

    /**
     * Quick confirm route - creates action plans and links images
     */
    public function quickConfirm($id)
    {
        try {
            $storeVisit = StoreVisit::findOrFail($id);
            $actionItems = $storeVisit->getActionItems();
            
            if (count($actionItems) > 0) {
                // Generate action plans
                $this->generateActionPlans($storeVisit);
                
                // Link uploaded images to action plans
                $this->linkImagesToActionPlans($storeVisit);
                
                $storeVisit->update(['status' => 'approved']);
                
                return redirect()->route('store-visits.index')->with('success', count($actionItems) . ' action plan(s) created successfully!');
            } else {
                $storeVisit->update(['status' => 'approved']);
                return redirect()->route('store-visits.index')->with('success', 'Visit confirmed! No action plans needed.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'visits' => 'required|array',
            'visits.*' => 'exists:store_visits,id',
            'status' => 'required|in:Draft,Submitted,Pending Review,Approved'
        ]);

        StoreVisit::whereIn('id', $request->visits)
                  ->update(['status' => $request->status]);

        return back()->with('success', count($request->visits) . ' visit(s) updated successfully!');
    }

    public function exportPdf(Request $request)
    {
        // Build the same query as index
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        $query = StoreVisit::with('user');
        
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }
        
        // Apply same filters as index method
        $this->applyFilters($query, $request);
        
        $visits = $query->latest('visit_date')->get();
        
        // Calculate statistics
        $statistics = [
            'total' => $visits->count(),
            'completed' => $visits->where('status', 'Submitted')->count() + $visits->where('status', 'Approved')->count(),
            'pending' => $visits->whereIn('status', ['Draft', 'Pending Review'])->count(),
            'average_score' => $visits->count() > 0 ? round($visits->avg('score') ?? 0) : 0,
            'with_action_items' => $visits->filter(function($visit) {
                return count($visit->getActionItems()) > 0;
            })->count()
        ];
        
        $pdf = Pdf::loadView('exports.store-visits-pdf', compact('visits', 'statistics', 'user', 'isAdmin'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->download('store-visits-report-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        // Build the same query as index
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        $query = StoreVisit::with('user');
        
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }
        
        $this->applyFilters($query, $request);
        
        $visits = $query->latest('visit_date')->get();
        
        return Excel::download(
            new StoreVisitsExport($visits), 
            'store-visits-report-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }

    public function exportSinglePdf($id)
    {
        $visit = StoreVisit::with('user')->findOrFail($id);
        
        $pdf = Pdf::loadView('exports.single-visit-pdf', compact('visit'))
                  ->setPaper('a4');
        
        return $pdf->download('store-visit-' . $visit->restaurant_name . '-' . $visit->visit_date->format('Y-m-d') . '.pdf');
    }

    private function applyFilters($query, $request)
    {
        if ($request->filled('restaurant')) {
            $query->where('restaurant_name', 'LIKE', '%' . $request->restaurant . '%');
        }
        
        if ($request->filled('mic')) {
            $query->where('mic', $request->mic);
        }
        
        if ($request->filled('date_from')) {
            $query->where('visit_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('visit_date', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('restaurant_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('general_comments', 'LIKE', '%' . $search . '%')
                  ->orWhere('what_did_you_see', 'LIKE', '%' . $search . '%');
            });
        }
        
        if ($request->filled('purpose')) {
            $query->whereJsonContains('purpose_of_visit', $request->purpose);
        }
        
        if ($request->filled('score_min')) {
            $query->whereRaw('COALESCE(score, 0) >= ?', [$request->score_min]);
        }
        
        if ($request->filled('score_max')) {
            $query->whereRaw('COALESCE(score, 0) <= ?', [$request->score_max]);
        }
    }

    /**
     * Auto-generate action plans for "No" answers in store visits
     */
    private function generateActionPlans(StoreVisit $storeVisit)
    {
        $actionItems = $storeVisit->getActionItems();
        
        // Batch create for better performance
        $actionPlansData = [];
        
        foreach ($actionItems as $item) {
            $actionPlansData[] = [
                'store_visit_id' => $storeVisit->id,
                'item' => $item['question'],
                'issue' => $item['question'] . ' - Issue identified during visit',
                'action_required' => $this->generateActionDescription($item['field'], $item['question']),
                'priority' => $this->determinePriority($item['field']),
                'status' => 'Pending',
                'assigned_to' => null,
                'due_date' => null,
                'is_approved' => true,
                'notes' => $item['comment'] ?: 'Action required based on store visit findings',
                // Visit context
                'visit_purpose' => is_array($storeVisit->purpose_of_visit) ? 
                    implode(', ', $storeVisit->purpose_of_visit) : $storeVisit->purpose_of_visit,
                'area_manager' => auth()->user() ? auth()->user()->name : 'System',
                'restaurant_manager' => 'To be assigned',
                // 5W1H fields
                'what' => $item['question'] . ' needs improvement',
                'why' => 'Non-compliance identified during quality visit',
                'when_detail' => 'During visit on ' . $storeVisit->visit_date->format('Y-m-d'),
                'where' => $storeVisit->restaurant_name,
                'who' => 'Restaurant staff and management',
                'how' => $this->generateActionDescription($item['field'], $item['question']),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Bulk insert for better performance
        if (!empty($actionPlansData)) {
            ActionPlan::insert($actionPlansData);
            
            // Link uploaded images to action plans
            $this->linkImagesToActionPlans($storeVisit);
        }
    }
    
    /**
     * Link uploaded images to their corresponding action plans
     */
    private function linkImagesToActionPlans(StoreVisit $storeVisit)
    {
        // Get uploaded images from session
        $pendingImages = session('pending_action_plan_images');
        
        if (!$pendingImages || $pendingImages['store_visit_id'] != $storeVisit->id) {
            return;
        }
        
        $images = $pendingImages['images'];
        
        // Get all action plans for this visit
        $actionPlans = $storeVisit->actionPlans()->get();
        
        // For each action plan, check if there are images for its field
        foreach ($actionPlans as $actionPlan) {
            // Extract field name from action plan item or issue
            // The item field contains the question text, we need to map it to field names
            $fieldName = $this->getFieldNameFromQuestion($actionPlan->item);
            
            if ($fieldName && isset($images[$fieldName])) {
                // Create action plan images
                foreach ($images[$fieldName] as $imageData) {
                    ActionPlanImage::create([
                        'action_plan_id' => $actionPlan->id,
                        'image_path' => $imageData['path'],
                        'field_name' => $fieldName,
                        'original_name' => $imageData['original_name'],
                        'file_size' => $imageData['file_size'],
                        'mime_type' => $imageData['mime_type'],
                    ]);
                }
            }
        }
        
        // Clear the session data
        session()->forget('pending_action_plan_images');
    }
    
    /**
     * Map question text back to field name
     * IMPORTANT: These must match EXACTLY with the question text in StoreVisit model getActionItems()
     */
    private function getFieldNameFromQuestion($questionText)
    {
        $questionMap = [
            'OCA Board is Completely Followed/Communicated' => 'oca_board_followed',
            'Staff Know their Side Duty' => 'staff_know_duty',
            'Coaching and Directing' => 'coaching_directing',
            'Smile and Friendly Greetings' => 'smile_greetings',
            'Suggestive Selling' => 'suggestive_selling',
            'Offer new Promotion' => 'offer_promotion',
            'Saying Thank You and Provides Direction' => 'thank_direction',
            'Team Work and Hustle' => 'team_work_hustle',
            'Order Accuracy' => 'order_accuracy',
            'Service Time' => 'service_time',
            'Dine In' => 'dine_in',
            'Take Out' => 'take_out',
            'Family' => 'family',
            'Delivery' => 'delivery',
            'Drive Thru' => 'drive_thru',
            'Weekly Schedule and Overtime' => 'weekly_schedule',
            'MOD aware of Financial Goal' => 'mod_financial_goal',
            'Sales (Cashier Objectives)' => 'sales_objectives',
            'Cash Policies followed Spot Check' => 'cash_policies',
            'Daily Waste Followed Properly (Daily)' => 'daily_waste',
            'TTF followed properly' => 'ttf_followed',
            'Sandwich Assembly being followed' => 'sandwich_assembly',
            'QSC was completed and followed properly' => 'qsc_completed',
            'Oil/Shortening Meets Standards' => 'oil_standards',
            'Day Labels updated' => 'day_labels',
            'Equipments are all working properly' => 'equipment_working',
            'Fryer Basket in Good Condition (not broken or rusty)' => 'fryer_condition',
            'Vegetable Preparation meets standards and Salad Prep' => 'vegetable_prep',
            'Employee Appearance well groomed' => 'employee_appearance',
            'Equipment are wrapped and hang' => 'equipment_wrapped',
            'Compartment sink are set-up properly' => 'sink_setup',
            'Sanitizer meets standard' => 'sanitizer_standard',
            'Dining Area/Family area no busting' => 'dining_area_clean',
            'CR or handwash area has tissue and clean' => 'restroom_clean',
        ];
        
        return $questionMap[$questionText] ?? null;
    }

    /**
     * Generate detailed action description based on the failed item
     */
    private function generateActionDescription($field, $question)
    {
        $actionDescriptions = [
            'oca_board_followed' => 'Review OCA board procedures with staff and ensure proper communication of all items',
            'staff_know_duty' => 'Conduct side duty training session and create duty assignment checklist',
            'coaching_directing' => 'Implement proper coaching and directing procedures with management training',
            'smile_greetings' => 'Train staff on customer service standards and friendly greeting protocols',
            'suggestive_selling' => 'Conduct suggestive selling training and implement sales targets',
            'offer_promotion' => 'Ensure staff are informed about current promotions and train on promotion offerings',
            'thank_direction' => 'Train staff on proper customer farewell procedures and direction giving',
            'team_work_hustle' => 'Enhance team coordination and work pace through team building exercises',
            'order_accuracy' => 'Review and improve order taking procedures, implement double-check system',
            'service_time' => 'Optimize service time to meet standards through process improvement',
            'dine_in' => 'Improve dine-in service quality and customer experience',
            'take_out' => 'Enhance take-out service procedures and packaging standards',
            'family' => 'Improve family-friendly service approach and facilities',
            'delivery' => 'Optimize delivery service procedures and timing',
            'drive_thru' => 'Enhance drive-thru service speed and accuracy',
            'weekly_schedule' => 'Review scheduling practices and overtime management',
            'mod_financial_goal' => 'Ensure MOD understands and tracks financial goals',
            'sales_objectives' => 'Train cashiers on sales objectives and targets',
            'cash_policies' => 'Review and reinforce cash handling policies',
            'daily_waste' => 'Implement proper waste management procedures and tracking',
            'ttf_followed' => 'Ensure TTF (Time, Temperature, Freshness) standards are met',
            'sandwich_assembly' => 'Review sandwich assembly standards and procedures',
            'qsc_completed' => 'Complete QSC (Quality, Service, Cleanliness) checklist properly',
            'oil_standards' => 'Monitor and maintain oil quality standards',
            'day_labels' => 'Ensure proper day labeling procedures are followed',
            'equipment_working' => 'Inspect and repair all equipment to working condition',
            'fryer_condition' => 'Replace or repair fryer baskets to meet standards',
            'vegetable_prep' => 'Review vegetable preparation and salad prep standards',
            'employee_appearance' => 'Ensure all employees meet grooming and appearance standards',
            'equipment_wrapped' => 'Properly wrap and hang all equipment according to standards',
            'sink_setup' => 'Set up compartment sink properly according to health standards',
            'sanitizer_standard' => 'Ensure sanitizer meets required standards and concentration',
            'dining_area_clean' => 'Maintain clean dining area and family area without clutter',
            'restroom_clean' => 'Ensure restroom/handwash area has tissue and is properly cleaned'
        ];

        return $actionDescriptions[$field] ?? "Address and improve: {$question}";
    }

    /**
     * Determine priority level based on the failed item
     */
    private function determinePriority($field)
    {
        $highPriorityItems = [
            'cash_policies', 'oil_standards', 'sanitizer_standard', 'equipment_working',
            'qsc_completed', 'order_accuracy', 'service_time'
        ];
        
        $mediumPriorityItems = [
            'oca_board_followed', 'coaching_directing', 'team_work_hustle',
            'ttf_followed', 'sandwich_assembly', 'daily_waste'
        ];
        
        if (in_array($field, $highPriorityItems)) {
            return 'High';
        } elseif (in_array($field, $mediumPriorityItems)) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    /**
     * Confirm store visit and generate action plans
     */
    public function confirmAndCreateActionPlans(Request $request)
    {
        \Log::info('Action plans confirmation requested', [
            'visit_id' => $request->input('visit_id'),
            'user' => auth()->user() ? auth()->user()->name : 'Not authenticated'
        ]);
        
        try {
            $visitId = $request->input('visit_id');
            if (!$visitId) {
                \Log::error('No visit_id provided in request');
                
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Visit ID is required'], 400);
                }
                return redirect()->back()->with('error', 'Visit ID is required.');
            }
            
            $storeVisit = StoreVisit::findOrFail($visitId);
            
            // Generate action plans for "No" answers
            $actionItems = $storeVisit->getActionItems();
            $actionItemsCount = count($actionItems);
            
            if ($actionItemsCount > 0) {
                $this->generateActionPlans($storeVisit);
                
                // Update status to approved (confirmed)
                $storeVisit->status = 'Approved';
                $storeVisit->save();
                
                $message = "Store visit confirmed and {$actionItemsCount} action plans created successfully!";
            } else {
                // Update status to approved (confirmed)
                $storeVisit->status = 'Approved';
                $storeVisit->save();
                
                $message = "Store visit confirmed! No action plans needed as all items passed inspection.";
            }

            return redirect()->route('store-visits.index')->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Action plan creation failed: ' . $e->getMessage(), [
                'visit_id' => $request->input('visit_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create action plans: ' . $e->getMessage());
        }
    }
    
    /**
     * Ensure uploaded image is accessible via public storage link
     * This fixes the issue where images are stored in storage/app/public but not accessible via /storage/
     */
    private function ensureImageAccessible($path)
    {
        // Path is like 'action-plans/filename.jpg'
        $storagePath = storage_path('app/public/' . $path);
        $publicPath = public_path('storage/' . $path);
        
        // Ensure public storage directory exists
        $publicDir = dirname($publicPath);
        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        
        // Copy file to public storage if it doesn't exist or is different
        if (file_exists($storagePath) && (!file_exists($publicPath) || filemtime($storagePath) > filemtime($publicPath))) {
            copy($storagePath, $publicPath);
            \Log::info('Image copied to public storage', [
                'from' => $storagePath,
                'to' => $publicPath
            ]);
        }
    }

    /**
     * Print/export a single store visit report as PDF
     */
    public function printReport($id)
    {
        $visit = StoreVisit::with(['user', 'actionPlans'])->findOrFail($id);
        
        // Load PDF view with visit data
        $pdf = Pdf::loadView('exports.single-visit-pdf', compact('visit'))
                  ->setPaper('a4');
        
        // Stream PDF to browser (for printing/viewing)
        return $pdf->stream('store-visit-report-' . $visit->restaurant_name . '-' . $visit->visit_date->format('Y-m-d') . '.pdf');
    }
}
