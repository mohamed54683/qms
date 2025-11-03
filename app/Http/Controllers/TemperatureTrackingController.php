<?php
namespace App\Http\Controllers;

use App\Models\TemperatureTrackingForm;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Exports\TemperatureTrackingExport;
use Maatwebsite\Excel\Facades\Excel;

class TemperatureTrackingController extends Controller
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
        
        $query = TemperatureTrackingForm::with('store','creator');
        
        // Apply access control filters
        if (!$isAdmin && !$isAreaManager) {
            // Regular users: filter by assigned restaurants
            $assignedRestaurantIds = $user->restaurants()->pluck('restaurants.id');
            $query->whereIn('store_id', $assignedRestaurantIds);
        } elseif ($isAreaManager && !$isAdmin) {
            // Area managers: filter by assigned restaurants (using pivot table)
            $assignedRestaurantIds = $user->restaurants()->pluck('restaurants.id');
            $query->whereIn('store_id', $assignedRestaurantIds);
        }
        
        if ($request->store_id) $query->where('store_id', $request->store_id);
        if ($request->date_from) $query->where('date', '>=', $request->date_from);
        if ($request->date_to) $query->where('date', '<=', $request->date_to);
        if ($request->status) $query->where('status', $request->status);
        
        // Filter by area manager if specified (for admins)
        if ($request->filled('area_manager')) {
            $areaManagerId = $request->area_manager;
            
            // Get restaurants assigned to this area manager
            $areaManagerUser = User::find($areaManagerId);
            if ($areaManagerUser) {
                $assignedRestaurantIds = $areaManagerUser->restaurants()->pluck('restaurants.id');
                if ($assignedRestaurantIds->isNotEmpty()) {
                    $query->whereIn('store_id', $assignedRestaurantIds);
                } else {
                    // If area manager has no restaurants, show nothing
                    $query->whereRaw('1 = 0');
                }
            }
        }
        
        // Handle PDF export
        if ($request->export === 'pdf') {
            $forms = $query->orderBy('date','desc')->get();
            
            // Get appropriate restaurants for filter based on user permissions
            if ($isAdmin) {
                $stores = Restaurant::all(['id','name','latitude','longitude']);
            } elseif ($isAreaManager) {
                $stores = $user->restaurants()->get(['restaurants.id','restaurants.name','restaurants.latitude','restaurants.longitude']);
            } else {
                $stores = $user->restaurants()->get(['restaurants.id','restaurants.name','restaurants.latitude','restaurants.longitude']);
            }
            
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('temperature-tracking.pdf', compact('forms', 'stores'));
            return $pdf->download('temperature-tracking-report-' . date('Y-m-d') . '.pdf');
        }
        
        $forms = $query->orderBy('date','desc')->paginate(20);
        
        // Get appropriate restaurants for filter based on user permissions
        if ($isAdmin) {
            $stores = Restaurant::all(['id','name','latitude','longitude']);
        } elseif ($isAreaManager) {
            $stores = $user->restaurants()->get(['restaurants.id','restaurants.name','restaurants.latitude','restaurants.longitude']);
        } else {
            $stores = $user->restaurants()->get(['restaurants.id','restaurants.name','restaurants.latitude','restaurants.longitude']);
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

        $statuses = ['Draft', 'Submitted', 'Reviewed', 'Approved'];
        return Inertia::render('TemperatureTracking/Index', [
            'forms' => $forms,
            'stores' => $stores,
            'statuses' => $statuses,
            'areaManagers' => $areaManagers,
            'filters' => $request->only(['store_id', 'date_from', 'date_to', 'status', 'area_manager']),
            'isAdmin' => $isAdmin,
            'isAreaManager' => $isAreaManager,
            'canViewAreaManagerFilter' => $canViewAreaManagerFilter
        ]);
    }
    public function create()
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin($user);
        $isAreaManager = $this->isAreaManager($user);
        
        // Get appropriate restaurants based on user permissions
        if ($isAdmin) {
            $stores = Restaurant::all(['id','name','latitude','longitude']);
        } elseif ($isAreaManager) {
            $stores = $user->restaurants()->get(['restaurants.id','restaurants.name','restaurants.latitude','restaurants.longitude']);
        } else {
            $stores = $user->restaurants()->get(['restaurants.id','restaurants.name','restaurants.latitude','restaurants.longitude']);
        }
        
        return Inertia::render('TemperatureTracking/Create', [
            'stores' => $stores,
            'isAdmin' => $isAdmin,
            'isAreaManager' => $isAreaManager,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|exists:restaurants,id',
            'mic_morning' => 'required|string',
            'mic_evening' => 'required|string',
            'shift' => 'required|string|in:morning,evening',
            'date' => 'required|date',
            'cooked_products' => 'nullable|array',
            'holding_products' => 'nullable|array',
            'side_cooked' => 'nullable|array',
            'side_holding' => 'nullable|array',
            'vegetables' => 'nullable|array',
            'vegetable_receiving' => 'nullable|array',
            'equipment' => 'nullable|array',
            'sanitizer' => 'nullable|array',
            'receiving_products' => 'nullable|array',
            'product_receiving_side' => 'nullable|array',
            'corrective_action' => 'nullable|string',
            'corrective_action_upper' => 'nullable|string',
            'corrective_action_lower' => 'nullable|string',
            'shift_turnover' => 'nullable|array',
            'status' => 'required|in:Draft,Submitted,Reviewed',
            'user_latitude' => 'nullable|numeric',
            'user_longitude' => 'nullable|numeric',
        ]);
        
        // Verify user has access to the selected restaurant (non-admin only)
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        if (!$isAdmin) {
            $hasAccess = $user->restaurants()->where('restaurants.id', $data['store_id'])->exists();
            if (!$hasAccess) {
                return back()->withErrors(['store_id' => 'You do not have permission to create forms for this restaurant.'])->withInput();
            }
        }
        
        // Backend compliance enforcement
        $outOfRange = false;
        $sanitizerOut = false;
        $tempBlocks = ['cooked_products','holding_products','side_cooked','side_holding','vegetables'];
        foreach ($tempBlocks as $block) {
            if (!empty($data[$block]) && is_array($data[$block])) {
                foreach ($data[$block] as $prod) {
                    if (!is_array($prod)) continue;
                    foreach ($prod as $val) {
                        if ($val === '' || $val === null) continue;
                        $num = floatval($val);
                        if ($block === 'vegetables' && ($num < 36 || $num > 40)) $outOfRange = true;
                        if (in_array($block, ['cooked_products','side_cooked']) && ($num < 160 || $num > 165)) $outOfRange = true;
                        if (in_array($block, ['holding_products','side_holding']) && $num < 140) $outOfRange = true;
                    }
                }
            }
        }
        if (!empty($data['sanitizer']) && is_array($data['sanitizer'])) {
            foreach ($data['sanitizer'] as $val) {
                if ($val === '' || $val === null) continue;
                $num = floatval($val);
                if ($num < 150 || $num > 300) $sanitizerOut = true;
            }
        }
        $corrective = trim(($data['corrective_action_upper'] ?? '') . ($data['corrective_action_lower'] ?? ''));
        if (($outOfRange || $sanitizerOut) && strlen($corrective) < 3 && $data['status'] === 'Submitted') {
            return back()->withErrors(['corrective_action_upper' => 'Corrective action is required for out-of-range values.'])->withInput();
        }
        // Notification logic stub: Area Manager if product receiving Y/N is N or shift turnover is ✖
        $notifyAreaManager = false;
        if (!empty($data['receiving_products']) && is_array($data['receiving_products'])) {
            foreach ($data['receiving_products'] as $prod) {
                if (isset($prod['yn']) && strtoupper($prod['yn']) === 'N') $notifyAreaManager = true;
            }
        }
        if (!empty($data['product_receiving_side']) && is_array($data['product_receiving_side'])) {
            foreach ($data['product_receiving_side'] as $prod) {
                if (isset($prod['yn']) && strtoupper($prod['yn']) === 'N') $notifyAreaManager = true;
            }
        }
        if (!empty($data['shift_turnover']) && is_array($data['shift_turnover'])) {
            foreach (['morning','evening'] as $shift) {
                if (!empty($data['shift_turnover'][$shift]) && is_array($data['shift_turnover'][$shift])) {
                    foreach ($data['shift_turnover'][$shift] as $val) {
                        if (trim($val) === 'x' || trim($val) === '✖') $notifyAreaManager = true;
                    }
                }
            }
        }
        // TODO: Implement actual notification to Area Manager if $notifyAreaManager is true
        $data['created_by'] = Auth::id();
        TemperatureTrackingForm::create($data);
        return redirect()->route('temperature-tracking.index')->with('success','Form submitted!');
    }
    public function show($id)
    {
        $form = TemperatureTrackingForm::with('store','creator')->findOrFail($id);
        
        // Check if user has access to this restaurant
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        if (!$isAdmin) {
            $assignedRestaurantIds = $user->restaurants()->pluck('restaurants.id');
            if (!$assignedRestaurantIds->contains($form->store_id)) {
                abort(403, 'You do not have access to this restaurant.');
            }
        }
        
        // Get the form data with proper JSON casting
        $formData = $form->toArray();
        
        // Convert date to simple string format for frontend
        if (isset($formData['date'])) {
            $formData['date'] = date('Y-m-d', strtotime($formData['date']));
        }
        
        // Ensure JSON fields are properly decoded as arrays (not strings)
        $jsonFields = ['cooked_products', 'holding_products', 'side_cooked', 'vegetables', 
                      'sanitizer', 'equipment', 'vegetable_receiving', 'receiving_products', 'shift_turnover'];
        
        foreach ($jsonFields as $field) {
            if (isset($formData[$field]) && is_string($formData[$field])) {
                $formData[$field] = json_decode($formData[$field], true) ?? [];
            } elseif (!isset($formData[$field])) {
                $formData[$field] = [];
            }
        }

        // Transform equipment data to match component expectations
        if (!empty($formData['equipment'])) {
            $equipmentMapping = [
                'reach_in_thaw' => 'Reach-In Chiller (Thaw)',
                'reach_in_veg' => 'Reach-In Chiller (Veg)',
                'walk_in_frozen' => 'Walk-In Freezer (Frozen)',
                'walk_in_reach' => 'Walk-In Freezer (Reach-In)'
            ];
            $transformed = [];
            foreach ($formData['equipment'] as $key => $value) {
                $displayName = $equipmentMapping[$key] ?? $key;
                // Use 'chill' or 'freeze' value
                $transformed[$displayName] = $value['chill'] ?? $value['freeze'] ?? '';
            }
            $formData['equipment'] = $transformed;
        }

        // Transform receiving_products to match component expectations (yn -> frozen)
        if (!empty($formData['receiving_products'])) {
            foreach ($formData['receiving_products'] as $product => $data) {
                if (isset($data['yn'])) {
                    $formData['receiving_products'][$product]['frozen'] = $data['yn'];
                }
            }
        }

        // Transform shift_turnover data to match component expectations
        if (!empty($formData['shift_turnover'])) {
            $categories = ['frozen', 'packaging', 'bread', 'maintenance'];
            $transformed = [];
            foreach ($categories as $cat) {
                $transformed[ucfirst($cat)] = [
                    'morning' => $formData['shift_turnover']['morning'][$cat] ?? '',
                    'evening' => $formData['shift_turnover']['evening'][$cat] ?? '',
                ];
            }
            // Convert checkmarks to Y/N
            foreach ($transformed as $cat => $shifts) {
                foreach ($shifts as $shift => $value) {
                    if ($value === '✔') {
                        $transformed[$cat][$shift] = 'Y';
                    } elseif ($value === '✖') {
                        $transformed[$cat][$shift] = 'N';
                    }
                }
            }
            $formData['shift_turnover'] = $transformed;
        }
        
        return Inertia::render('TemperatureTracking/Show', [
            'form' => $formData
        ]);
    }
    public function edit(TemperatureTrackingForm $temperatureTrackingForm)
    {
        // Check if user has access to this restaurant
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        if (!$isAdmin) {
            $assignedRestaurantIds = $user->restaurants()->pluck('restaurants.id');
            if (!$assignedRestaurantIds->contains($temperatureTrackingForm->store_id)) {
                abort(403, 'You do not have access to this restaurant.');
            }
        }
        
        // Only show assigned restaurants in dropdown
        if ($isAdmin) {
            $stores = Restaurant::all(['id','name']);
        } else {
            $stores = $user->restaurants()->get(['restaurants.id','restaurants.name']);
        }
        
        return Inertia::render('TemperatureTracking/Create', [
            'form' => $temperatureTrackingForm,
            'stores' => $stores,
            'isAdmin' => $isAdmin,
        ]);
    }
    public function update(Request $request, TemperatureTrackingForm $temperatureTrackingForm)
    {
        // Check if user has access to this restaurant
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        if (!$isAdmin) {
            $assignedRestaurantIds = $user->restaurants()->pluck('restaurants.id');
            if (!$assignedRestaurantIds->contains($temperatureTrackingForm->store_id)) {
                abort(403, 'You do not have access to this restaurant.');
            }
        }
        
        $data = $request->validate([
            'store_id' => 'required|exists:restaurants,id',
            'mic_morning' => 'required|string',
            'mic_evening' => 'required|string',
            'shift' => 'required|string|in:morning,evening',
            'date' => 'required|date',
            'cooked_products' => 'nullable|array',
            'holding_products' => 'nullable|array',
            'side_cooked' => 'nullable|array',
            'side_holding' => 'nullable|array',
            'vegetables' => 'nullable|array',
            'vegetable_receiving' => 'nullable|array',
            'equipment' => 'nullable|array',
            'sanitizer' => 'nullable|array',
            'receiving_products' => 'nullable|array',
            'product_receiving_side' => 'nullable|array',
            'corrective_action' => 'nullable|string',
            'corrective_action_upper' => 'nullable|string',
            'corrective_action_lower' => 'nullable|string',
            'shift_turnover' => 'nullable|array',
            'status' => 'required|in:Draft,Submitted,Reviewed',
        ]);
        
        // Verify user has access to the new restaurant selection (non-admin only)
        if (!$isAdmin) {
            $hasAccess = $user->restaurants()->where('restaurants.id', $data['store_id'])->exists();
            if (!$hasAccess) {
                return back()->withErrors(['store_id' => 'You do not have permission to update forms for this restaurant.'])->withInput();
            }
        }
        
        // Backend compliance enforcement for update
        $outOfRange = false;
        $sanitizerOut = false;
        $tempBlocks = ['cooked_products','holding_products','side_cooked','side_holding','vegetables'];
        foreach ($tempBlocks as $block) {
            if (!empty($data[$block]) && is_array($data[$block])) {
                foreach ($data[$block] as $prod) {
                    if (!is_array($prod)) continue;
                    foreach ($prod as $val) {
                        if ($val === '' || $val === null) continue;
                        $num = floatval($val);
                        if ($block === 'vegetables' && ($num < 36 || $num > 40)) $outOfRange = true;
                        if (in_array($block, ['cooked_products','side_cooked']) && ($num < 160 || $num > 165)) $outOfRange = true;
                        if (in_array($block, ['holding_products','side_holding']) && $num < 140) $outOfRange = true;
                    }
                }
            }
        }
        if (!empty($data['sanitizer']) && is_array($data['sanitizer'])) {
            foreach ($data['sanitizer'] as $val) {
                if ($val === '' || $val === null) continue;
                $num = floatval($val);
                if ($num < 150 || $num > 300) $sanitizerOut = true;
            }
        }
        $corrective = trim(($data['corrective_action_upper'] ?? '') . ($data['corrective_action_lower'] ?? ''));
        if (($outOfRange || $sanitizerOut) && strlen($corrective) < 3 && $data['status'] === 'Submitted') {
            return back()->withErrors(['corrective_action_upper' => 'Corrective action is required for out-of-range values.'])->withInput();
        }
        $temperatureTrackingForm->update($data);

        return redirect()->route('temperature-tracking.index')->with('success', 'Form updated successfully!');
    }

    public function destroy(TemperatureTrackingForm $temperatureTrackingForm)
    {
        // Check if user has access to this restaurant
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        if (!$isAdmin) {
            $assignedRestaurantIds = $user->restaurants()->pluck('restaurants.id');
            if (!$assignedRestaurantIds->contains($temperatureTrackingForm->store_id)) {
                abort(403, 'You do not have access to this restaurant.');
            }
        }
        
        $temperatureTrackingForm->delete();
        return back()->with('success','Form deleted!');
    }

    /**
     * Export Temperature Tracking to Excel
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->roles && $user->roles->contains('name', 'admin');
        
        // Get filters from request
        $filters = [
            'store_id' => $request->input('store_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'status' => $request->input('status'),
            'shift' => $request->input('shift'),
        ];
        
        $filename = 'temperature-tracking-export-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new TemperatureTrackingExport($filters, $isAdmin), $filename);
    }
}