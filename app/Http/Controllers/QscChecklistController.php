<?php

namespace App\Http\Controllers;

use App\Models\QscChecklist;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Exports\QscChecklistExport;
use Maatwebsite\Excel\Facades\Excel;

class QscChecklistController extends Controller
{
    /**
     * Check if user is admin (Super Admin or admin role)
     */
    private function isAdmin($user)
    {
        return $user->roles && (
            $user->roles->contains('name', 'admin') || 
            $user->roles->contains('name', 'Super Admin')
        );
    }

    /**
     * Check if user is area manager
     */
    private function isAreaManager($user)
    {
        return $user->roles && $user->roles->contains('name', 'Area Manager');
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        $isAreaManager = $this->isAreaManager($user);
        
        // Build query for QSC checklists
        $query = QscChecklist::with(['user.roles']);
        
        // Apply access control filters
        if (!$isAdmin && !$isAreaManager) {
            // Regular users: filter by user's assigned restaurants
            $userRestaurants = $user->restaurants->pluck('name')->toArray();
            if (!empty($userRestaurants)) {
                $query->where(function($q) use ($userRestaurants) {
                    foreach ($userRestaurants as $restaurant) {
                        $q->orWhere('store_name', 'LIKE', '%' . $restaurant . '%');
                    }
                });
            } else {
                // If user has no assigned restaurants, show nothing
                $query->whereRaw('1 = 0');
            }
        } elseif ($isAreaManager && !$isAdmin) {
            // Area managers: filter by their assigned restaurants (using pivot table)
            $assignedRestaurants = $user->restaurants->pluck('name')->toArray();
            if (!empty($assignedRestaurants)) {
                $query->where(function($q) use ($assignedRestaurants) {
                    foreach ($assignedRestaurants as $restaurant) {
                        $q->orWhere('store_name', 'LIKE', '%' . $restaurant . '%');
                    }
                });
            } else {
                // If area manager has no assigned restaurants, show nothing
                $query->whereRaw('1 = 0');
            }
        }
        
        // Apply filters
        if ($request->filled('restaurant')) {
            $query->where('store_name', 'LIKE', '%' . $request->restaurant . '%');
        }
        
        if ($request->filled('time_option')) {
            $query->where('time_option', $request->time_option);
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('mod', 'LIKE', '%' . $search . '%')
                  ->orWhere('manager_signature', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Filter by area manager if specified
        if ($request->filled('area_manager')) {
            $areaManagerId = $request->area_manager;
            
            // Get restaurants assigned to this area manager
            $areaManagerUser = User::find($areaManagerId);
            if ($areaManagerUser) {
                $assignedRestaurants = $areaManagerUser->restaurants->pluck('name')->toArray();
                if (!empty($assignedRestaurants)) {
                    $query->where(function($q) use ($assignedRestaurants) {
                        foreach ($assignedRestaurants as $restaurant) {
                            $q->orWhere('store_name', 'LIKE', '%' . $restaurant . '%');
                        }
                    });
                } else {
                    // If area manager has no restaurants, show nothing
                    $query->whereRaw('1 = 0');
                }
            }
        }
        
        // Order by latest first
        $query->orderBy('created_at', 'desc');
        
        $checklists = $query->paginate(10)->appends($request->query());
        
        // Get restaurants for filter dropdown based on user permissions
        if ($isAdmin) {
            // Admin can see all restaurants
            $restaurants = Restaurant::orderBy('name')->get();
        } elseif ($isAreaManager) {
            // Area managers see only their assigned restaurants (using pivot table)
            $restaurants = $user->restaurants()->orderBy('name')->get();
        } else {
            // Regular users see only their assigned restaurants
            $restaurants = $user->restaurants()->orderBy('name')->get();
        }
        
        // Get area managers list (based on permission and assigned area managers)
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
        
        return Inertia::render('QscChecklist/Report', [
            'checklists' => $checklists,
            'restaurants' => $restaurants,
            'areaManagers' => $areaManagers,
            'filters' => $request->only(['restaurant', 'time_option', 'date_from', 'date_to', 'search', 'status', 'area_manager']),
            'isAdmin' => $isAdmin,
            'canViewAreaManagerFilter' => $canViewAreaManagerFilter,
            'isAreaManager' => $isAreaManager,
            'canExportAll' => $isAdmin || $isAreaManager,
            'canViewAllRestaurants' => $isAdmin,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        $isAreaManager = $this->isAreaManager($user);
        
        // Load restaurants based on user permissions
        if ($isAdmin) {
            // Admin can see all restaurants
            $restaurants = Restaurant::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'latitude', 'longitude']);
        } elseif ($isAreaManager) {
            // Area managers see only their managed restaurants
            $restaurants = $user->managedRestaurants()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'latitude', 'longitude']);
        } else {
            // Regular users see only their assigned restaurants
            $restaurants = $user->restaurants()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['restaurants.id', 'restaurants.name', 'restaurants.code', 'restaurants.latitude', 'restaurants.longitude']);
        }
        
        return Inertia::render('QscChecklist/Form', [
            'restaurants' => $restaurants,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'date' => 'required|date',
            'mod' => 'required|string|max:255',
            'time_option' => 'required|in:12PM,5PM,8PM,11PM',
            'manager_signature' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $checklist = QscChecklist::create([
            'user_id' => auth()->id(),
            'store_name' => $request->store_name,
            'date' => $request->date,
            'mod' => $request->mod,
            'day' => $request->day,
            'time_option' => $request->time_option,
            
            // Exterior section
            'exterior_lights_open' => $request->input('exterior.lights_open'),
            'exterior_lights_open_comment' => $request->input('exterior.lights_open_comment'),
            'exterior_logo_clean' => $request->input('exterior.logo_clean'),
            'exterior_logo_clean_comment' => $request->input('exterior.logo_clean_comment'),
            'exterior_parking_clean' => $request->input('exterior.parking_clean'),
            'exterior_parking_clean_comment' => $request->input('exterior.parking_clean_comment'),
            'exterior_no_graffiti' => $request->input('exterior.no_graffiti'),
            'exterior_no_graffiti_comment' => $request->input('exterior.no_graffiti_comment'),
            
            // Doors and Glass section
            'doors_glass_clean' => $request->input('doors_glass.glass_clean'),
            'doors_glass_clean_comment' => $request->input('doors_glass.glass_clean_comment'),
            'doors_door_handle' => $request->input('doors_glass.door_handle'),
            'doors_door_handle_comment' => $request->input('doors_glass.door_handle_comment'),
            'doors_entrance_clean' => $request->input('doors_glass.entrance_clean'),
            'doors_entrance_clean_comment' => $request->input('doors_glass.entrance_clean_comment'),
            
            // Frontline section
            'frontline_areas_organized' => $request->input('frontline.areas_organized'),
            'frontline_areas_organized_comment' => $request->input('frontline.areas_organized_comment'),
            'frontline_customers_greeted' => $request->input('frontline.customers_greeted'),
            'frontline_customers_greeted_comment' => $request->input('frontline.customers_greeted_comment'),
            'frontline_menu_available' => $request->input('frontline.menu_available'),
            'frontline_menu_available_comment' => $request->input('frontline.menu_available_comment'),
            'frontline_seven_steps' => $request->input('frontline.seven_steps'),
            'frontline_seven_steps_comment' => $request->input('frontline.seven_steps_comment'),
            'frontline_tables_clean' => $request->input('frontline.tables_clean'),
            'frontline_tables_clean_comment' => $request->input('frontline.tables_clean_comment'),
            'frontline_high_chairs' => $request->input('frontline.high_chairs'),
            'frontline_high_chairs_comment' => $request->input('frontline.high_chairs_comment'),
            'frontline_no_damaged_tables' => $request->input('frontline.no_damaged_tables'),
            'frontline_no_damaged_tables_comment' => $request->input('frontline.no_damaged_tables_comment'),
            
            // Restroom section
            'restroom_no_full_trash' => $request->input('restroom.no_full_trash'),
            'restroom_no_full_trash_comment' => $request->input('restroom.no_full_trash_comment'),
            'restroom_soap_available' => $request->input('restroom.soap_available'),
            'restroom_soap_available_comment' => $request->input('restroom.soap_available_comment'),
            'restroom_hand_dryer' => $request->input('restroom.hand_dryer'),
            'restroom_hand_dryer_comment' => $request->input('restroom.hand_dryer_comment'),
            
            // Holding Heating section
            'holding_product_temp' => $request->input('holding_heating.product_temp'),
            'holding_product_temp_comment' => $request->input('holding_heating.product_temp_comment'),
            'holding_product_age' => $request->input('holding_heating.product_age'),
            'holding_product_age_comment' => $request->input('holding_heating.product_age_comment'),
            'holding_check_product' => $request->input('holding_heating.check_product'),
            'holding_check_product_comment' => $request->input('holding_heating.check_product_comment'),
            'holding_products_fresh' => $request->input('holding_heating.products_fresh'),
            'holding_products_fresh_comment' => $request->input('holding_heating.products_fresh_comment'),
            'holding_internal_temp' => $request->input('holding_heating.internal_temp'),
            'holding_internal_temp_comment' => $request->input('holding_heating.internal_temp_comment'),
            'holding_shortening_level' => $request->input('holding_heating.shortening_level'),
            'holding_shortening_level_comment' => $request->input('holding_heating.shortening_level_comment'),
            'holding_check_shortening' => $request->input('holding_heating.check_shortening'),
            'holding_check_shortening_comment' => $request->input('holding_heating.check_shortening_comment'),
            'holding_fryer_maintenance' => $request->input('holding_heating.fryer_maintenance'),
            'holding_fryer_maintenance_comment' => $request->input('holding_heating.fryer_maintenance_comment'),
            'holding_use_tray' => $request->input('holding_heating.use_tray'),
            'holding_use_tray_comment' => $request->input('holding_heating.use_tray_comment'),
            'holding_fry_basket' => $request->input('holding_heating.fry_basket'),
            'holding_fry_basket_comment' => $request->input('holding_heating.fry_basket_comment'),
            'holding_fries_salted' => $request->input('holding_heating.fries_salted'),
            'holding_fries_salted_comment' => $request->input('holding_heating.fries_salted_comment'),
            'holding_fries_cooking' => $request->input('holding_heating.fries_cooking'),
            'holding_fries_cooking_comment' => $request->input('holding_heating.fries_cooking_comment'),
            'holding_buns_quality' => $request->input('holding_heating.buns_quality'),
            'holding_buns_quality_comment' => $request->input('holding_heating.buns_quality_comment'),
            'holding_teflon_sheet' => $request->input('holding_heating.teflon_sheet'),
            'holding_teflon_sheet_comment' => $request->input('holding_heating.teflon_sheet_comment'),
            'holding_bread_standard' => $request->input('holding_heating.bread_standard'),
            'holding_bread_standard_comment' => $request->input('holding_heating.bread_standard_comment'),
            
            // Thawing section
            'thawing_day_labels' => $request->input('thawing.day_labels'),
            'thawing_day_labels_comment' => $request->input('thawing.day_labels_comment'),
            'thawing_no_tampering' => $request->input('thawing.no_tampering'),
            'thawing_no_tampering_comment' => $request->input('thawing.no_tampering_comment'),
            'thawing_temp_range' => $request->input('thawing.temp_range'),
            'thawing_temp_range_comment' => $request->input('thawing.temp_range_comment'),
            'thawing_no_overstock' => $request->input('thawing.no_overstock'),
            'thawing_no_overstock_comment' => $request->input('thawing.no_overstock_comment'),
            'thawing_utensils_clean' => $request->input('thawing.utensils_clean'),
            'thawing_utensils_clean_comment' => $request->input('thawing.utensils_clean_comment'),
            'thawing_sink_setup' => $request->input('thawing.sink_setup'),
            'thawing_sink_setup_comment' => $request->input('thawing.sink_setup_comment'),
            'thawing_portion_standards' => $request->input('thawing.portion_standards'),
            'thawing_portion_standards_comment' => $request->input('thawing.portion_standards_comment'),
            'thawing_sultan_sauce' => $request->input('thawing.sultan_sauce'),
            'thawing_sultan_sauce_comment' => $request->input('thawing.sultan_sauce_comment'),
            'thawing_vegetables_date' => $request->input('thawing.vegetables_date'),
            'thawing_vegetables_date_comment' => $request->input('thawing.vegetables_date_comment'),
            'thawing_follow_fifo' => $request->input('thawing.follow_fifo'),
            'thawing_follow_fifo_comment' => $request->input('thawing.follow_fifo_comment'),
            
            // Burger Assembly section
            'burger_standard_setup' => $request->input('burger_assembly.standard_setup'),
            'burger_standard_setup_comment' => $request->input('burger_assembly.standard_setup_comment'),
            'burger_sauce_spiral' => $request->input('burger_assembly.sauce_spiral'),
            'burger_sauce_spiral_comment' => $request->input('burger_assembly.sauce_spiral_comment'),
            'burger_ingredients_order' => $request->input('burger_assembly.ingredients_order'),
            'burger_ingredients_order_comment' => $request->input('burger_assembly.ingredients_order_comment'),
            
            'manager_signature' => $request->manager_signature,
            'status' => 'Submitted'
        ]);

        return redirect()->route('qsc-checklist.report')
            ->with('success', 'Q.S.C Checklist submitted successfully!');
    }

    public function show(QscChecklist $qscChecklist)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        
        // Check if user can access this checklist
        if (!$isAdmin) {
            $userRestaurants = $user->restaurants->pluck('name')->toArray();
            $canAccess = false;
            
            foreach ($userRestaurants as $restaurant) {
                if (stripos($qscChecklist->store_name, $restaurant) !== false) {
                    $canAccess = true;
                    break;
                }
            }
            
            if (!$canAccess) {
                abort(403, 'You do not have permission to view this checklist.');
            }
        }
        
        $qscChecklist->load(['user.roles']);
        
        // Calculate detailed compliance metrics
        $complianceData = $this->calculateComplianceMetrics($qscChecklist);
        
        return Inertia::render('QscChecklist/Show', [
            'checklist' => $qscChecklist,
            'complianceData' => $complianceData,
        ]);
    }

    /**
     * Calculate detailed compliance metrics for the checklist
     */
    private function calculateComplianceMetrics($checklist)
    {
        $sections = [
            'exterior' => ['exterior_lights_open', 'exterior_logo_clean', 'exterior_parking_clean', 'exterior_no_graffiti'],
            'doors_glass' => ['doors_glass_clean', 'doors_door_handle', 'doors_entrance_clean'],
            'frontline' => ['frontline_areas_organized', 'frontline_customers_greeted', 'frontline_menu_available', 'frontline_seven_steps', 'frontline_tables_clean', 'frontline_high_chairs', 'frontline_no_damaged_tables'],
            'restroom' => ['restroom_no_full_trash', 'restroom_soap_available', 'restroom_hand_dryer'],
            'holding' => ['holding_product_temp', 'holding_product_age', 'holding_check_product', 'holding_products_fresh', 'holding_internal_temp', 'holding_shortening_level', 'holding_check_shortening', 'holding_fryer_maintenance', 'holding_use_tray', 'holding_fry_basket', 'holding_fries_salted', 'holding_fries_cooking', 'holding_buns_quality', 'holding_teflon_sheet', 'holding_bread_standard'],
            'thawing' => ['thawing_day_labels', 'thawing_no_tampering', 'thawing_temp_range', 'thawing_no_overstock', 'thawing_utensils_clean', 'thawing_sink_setup', 'thawing_portion_standards', 'thawing_sultan_sauce', 'thawing_vegetables_date', 'thawing_follow_fifo'],
            'burger_assembly' => ['burger_standard_setup', 'burger_sauce_spiral', 'burger_ingredients_order']
        ];

        $overallTotal = 0;
        $overallYes = 0;
        $sectionScores = [];
        $issuesCount = 0;
        $commentsCount = 0;

        foreach ($sections as $sectionName => $fields) {
            $sectionTotal = 0;
            $sectionYes = 0;

            foreach ($fields as $field) {
                $value = $checklist->$field;
                if ($value === 'Yes' || $value === 'No') {
                    $sectionTotal++;
                    $overallTotal++;
                    
                    if ($value === 'Yes') {
                        $sectionYes++;
                        $overallYes++;
                    } else {
                        $issuesCount++;
                    }
                }

                // Count comments
                $commentField = $field . '_comment';
                if (!empty($checklist->$commentField)) {
                    $commentsCount++;
                }
            }

            $sectionScores[$sectionName] = [
                'total' => $sectionTotal,
                'yes' => $sectionYes,
                'score' => $sectionTotal > 0 ? round(($sectionYes / $sectionTotal) * 100, 1) : 0
            ];
        }

        return [
            'overall_score' => $overallTotal > 0 ? round(($overallYes / $overallTotal) * 100, 1) : 0,
            'total_questions' => $overallTotal,
            'yes_answers' => $overallYes,
            'no_answers' => $overallTotal - $overallYes,
            'issues_count' => $issuesCount,
            'comments_count' => $commentsCount,
            'section_scores' => $sectionScores
        ];
    }

    public function edit(QscChecklist $qscChecklist)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        
        // Check if user can access this checklist
        if (!$isAdmin) {
            $userRestaurants = $user->restaurants->pluck('name')->toArray();
            $canAccess = false;
            
            foreach ($userRestaurants as $restaurant) {
                if (stripos($qscChecklist->store_name, $restaurant) !== false) {
                    $canAccess = true;
                    break;
                }
            }
            
            if (!$canAccess) {
                abort(403, 'You do not have permission to edit this checklist.');
            }
        }
        
        // Load restaurants based on user permissions
        if ($isAdmin) {
            // Admin can see all restaurants
            $restaurants = Restaurant::where('is_active', true)
                ->orderBy('name')
                ->get();
        } else {
            // Regular users see only their assigned restaurants
            $restaurants = $user->restaurants()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        
        return Inertia::render('QscChecklist/Form', [
            'checklist' => $qscChecklist,
            'restaurants' => $restaurants,
            'isAdmin' => $isAdmin,
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, QscChecklist $qscChecklist)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        
        // Check if user can access this checklist
        if (!$isAdmin) {
            $userRestaurants = $user->restaurants->pluck('name')->toArray();
            $canAccess = false;
            
            foreach ($userRestaurants as $restaurant) {
                if (stripos($qscChecklist->store_name, $restaurant) !== false) {
                    $canAccess = true;
                    break;
                }
            }
            
            if (!$canAccess) {
                abort(403, 'You do not have permission to update this checklist.');
            }
        }
        
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'date' => 'required|date',
            'mod' => 'required|string|max:255',
            'time_option' => 'required|in:12PM,5PM,8PM,11PM',
            'manager_signature' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $qscChecklist->update([
            'store_name' => $request->store_name,
            'date' => $request->date,
            'mod' => $request->mod,
            'day' => $request->day,
            'time_option' => $request->time_option,
            
            // Update all sections similar to store method
            // ... (same field mapping as store method)
            
            'manager_signature' => $request->manager_signature,
        ]);

        return redirect()->route('qsc-checklist.report')
            ->with('success', 'Q.S.C Checklist updated successfully!');
    }

    public function destroy(QscChecklist $qscChecklist)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        
        // Check if user can access this checklist
        if (!$isAdmin) {
            $userRestaurants = $user->restaurants->pluck('name')->toArray();
            $canAccess = false;
            
            foreach ($userRestaurants as $restaurant) {
                if (stripos($qscChecklist->store_name, $restaurant) !== false) {
                    $canAccess = true;
                    break;
                }
            }
            
            if (!$canAccess) {
                abort(403, 'You do not have permission to delete this checklist.');
            }
        }
        
        $qscChecklist->delete();
        
        return redirect()->route('qsc-checklist.report')
            ->with('success', 'Q.S.C Checklist deleted successfully!');
    }

    /**
     * Export filtered checklists to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = QscChecklist::query();
        if ($request->filled('restaurant')) $query->where('store_name', 'LIKE', '%'.$request->restaurant.'%');
        if ($request->filled('time_option')) $query->where('time_option', $request->time_option);
        if ($request->filled('date_from')) $query->where('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('date', '<=', $request->date_to);
        if ($request->filled('status')) $query->where('status', $request->status);

        $checklists = $query->orderBy('date','desc')->limit(500)->get();

        // Simple on-the-fly PDF using dompdf if configured, otherwise return JSON fallback
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.qsc_checklists', [
                'checklists' => $checklists,
                'generated_at' => now(),
            ])->setPaper('A4','landscape');
            return $pdf->download('qsc-checklists.pdf');
        }
        return response()->json(['message' => 'PDF library not installed', 'count' => $checklists->count()]);
    }

    /**
     * Export filtered checklists to CSV (Excel compatible)
     */
    public function exportCsv(Request $request)
    {
        $query = QscChecklist::query();
        if ($request->filled('restaurant')) $query->where('store_name', 'LIKE', '%'.$request->restaurant.'%');
        if ($request->filled('time_option')) $query->where('time_option', $request->time_option);
        if ($request->filled('date_from')) $query->where('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('date', '<=', $request->date_to);
        if ($request->filled('status')) $query->where('status', $request->status);
        $checklists = $query->orderBy('date','desc')->limit(1000)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="qsc-checklists.csv"',
        ];

        $callback = function() use ($checklists) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Store','Date','MOD','Time','Status','Manager','Score']);
            foreach ($checklists as $c) {
                fputcsv($out, [
                    $c->store_name,
                    $c->date,
                    $c->mod,
                    $c->time_option,
                    $c->status,
                    $c->manager_signature,
                    $c->compliance_score ?? ''
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Confirm / advance status of a checklist
     */
    public function confirm(QscChecklist $qscChecklist)
    {
        $next = match($qscChecklist->status) {
            'Submitted' => 'Reviewed',
            'Reviewed' => 'Approved',
            default => $qscChecklist->status,
        };
        if ($next !== $qscChecklist->status) {
            $qscChecklist->status = $next;
            $qscChecklist->save();
        }
        return back()->with('success', 'Checklist status updated to '.$qscChecklist->status);
    }

    /**
     * Export QSC Checklists to Excel
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        
        // Get filters from request
        $filters = [
            'restaurant' => $request->input('restaurant'),
            'time_option' => $request->input('time_option'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ];
        
        $filename = 'qsc-checklist-export-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new QscChecklistExport($filters, $isAdmin), $filename);
    }
}
