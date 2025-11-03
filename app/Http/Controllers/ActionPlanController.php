<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActionPlan;
use App\Models\StoreVisit;
use App\Models\Restaurant;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Exports\ActionPlansExport;
use Maatwebsite\Excel\Facades\Excel;

class ActionPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->roles && ($user->roles->contains('name', 'admin') || $user->roles->contains('name', 'Super Admin'));
        
        // Get assigned restaurants for the current user
        $assignedRestaurants = [];
        if (!$isAdmin) {
            $assignedRestaurants = $user->restaurants()->pluck('restaurants.id')->toArray();
        }
        
        // Build query for action plans with restaurant filtering
        $query = ActionPlan::with(['storeVisit', 'assignedUser'])
            ->forUserRestaurants($user);
        
        // Only show approved action plans (after restaurant manager confirmation)
        $query->where('is_approved', true);
        
        // Apply filters
        if ($request->filled('restaurants') && is_array($request->restaurants)) {
            $query->whereHas('storeVisit', function($q) use ($request) {
                $q->whereIn('restaurant_name', $request->restaurants);
            });
        } else if ($request->filled('restaurant')) {
            $query->whereHas('storeVisit', function($q) use ($request) {
                $q->where('restaurant_name', 'LIKE', '%' . $request->restaurant . '%');
            });
        }
        
        // Filter by area manager - filter by restaurants assigned to the area manager
        if ($request->filled('area_manager')) {
            $areaManagerId = $request->area_manager;
            
            // Get restaurants assigned to this area manager
            $areaManagerUser = User::find($areaManagerId);
            if ($areaManagerUser) {
                $assignedRestaurants = $areaManagerUser->restaurants->pluck('name')->toArray();
                if (!empty($assignedRestaurants)) {
                    $query->whereHas('storeVisit', function($q) use ($assignedRestaurants) {
                        $q->whereIn('restaurant_name', $assignedRestaurants);
                    });
                } else {
                    // If area manager has no assigned restaurants, show nothing
                    $query->whereRaw('1 = 0');
                }
            }
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item', 'LIKE', '%' . $search . '%')
                  ->orWhere('issue', 'LIKE', '%' . $search . '%')
                  ->orWhere('action_required', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Get total statistics before pagination
        $totalStatistics = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'Pending')->count(),
            'inProgress' => (clone $query)->where('status', 'In Progress')->count(),
            'completed' => (clone $query)->where('status', 'Completed')->count(),
        ];
        
        $actionPlans = $query->with(['images', 'storeVisit' => function($query) {
                                 $query->select('id', 'restaurant_name', 'mic', 'visit_date', 'score', 
                                               'general_comments', 'what_did_you_see', 'why_had_issue',
                                               'how_to_improve', 'who_when_responsible', 'attachments',
                                               // Include all comment fields for specific items
                                               'oca_board_comments', 'staff_duty_comments', 'coaching_comments',
                                               'smile_comments', 'suggestive_comments', 'promotion_comments', 'thank_comments',
                                               'teamwork_comments', 'accuracy_comments', 'service_comments',
                                               'dine_comments', 'takeout_comments', 'family_comments', 'delivery_comments', 'drive_comments',
                                               'schedule_comments', 'financial_comments', 'sales_comments', 'cash_comments', 'waste_comments',
                                               'ttf_comments', 'assembly_comments', 'qsc_comments', 'oil_comments', 'labels_comments',
                                               'equipment_comments', 'fryer_comments', 'vegetable_comments', 'appearance_comments',
                                               'wrapped_comments', 'sink_comments', 'sanitizer_comments', 'dining_comments', 'restroom_comments');
                             }, 'assignedUser', 'approvedBy'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);
        
        // Get restaurants for filter dropdown
        if ($isAdmin) {
            $restaurants = Restaurant::where('is_active', true)
                                   ->select('id', 'name', 'code')
                                   ->orderBy('code')
                                   ->get();
        } else {
            $restaurants = $user->restaurants()
                               ->where('restaurants.is_active', true)
                               ->select('restaurants.id', 'restaurants.name', 'restaurants.code')
                               ->orderBy('restaurants.code')
                               ->get();
        }
        
        // Get area managers (based on permission and assigned area managers)
        $areaManagers = [];
        $canViewAreaManagerFilter = $isAdmin || $user->can('view_area_manager_filter');
        
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
        
        // Get users for assignment dropdown (Area Managers and Admins)
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'restaurant_manager', 'area_manager']);
        })->get(['id', 'name', 'email']);
        
        return Inertia::render('ActionPlans/Index', [
            'actionPlans' => $actionPlans,
            'statistics' => $totalStatistics,
            'restaurants' => $restaurants,
            'areaManagers' => $areaManagers,
            'assignedRestaurants' => $assignedRestaurants,
            'users' => $users,
            'filters' => $request->only(['restaurants', 'restaurant', 'status', 'priority', 'date_from', 'date_to', 'search', 'area_manager']),
            'isAdmin' => $isAdmin,
            'canViewAreaManagerFilter' => $canViewAreaManagerFilter,
            'auth' => [
                'user' => $user
            ]
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $actionPlan = ActionPlan::findOrFail($id);
        
        $request->validate([
            'action_required' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            // 5W1H fields validation
            'what' => 'nullable|string',
            'where' => 'nullable|string',
            'why' => 'nullable|string',
            'how' => 'nullable|string',
            'who' => 'nullable|string',
            'when_detail' => 'nullable|string',
            'comment' => 'nullable|string'
        ]);
        
        $updateData = $request->only([
            'action_required', 'priority', 'status', 'due_date', 'notes', 'assigned_to',
            'what', 'where', 'why', 'how', 'who', 'when_detail', 'comment'
        ]);
        
        // If status is completed, set completion timestamp
        if ($request->status === 'Completed' && $actionPlan->status !== 'Completed') {
            $updateData['completed_at'] = now();
        } elseif ($request->status !== 'Completed') {
            $updateData['completed_at'] = null;
        }
        
        $actionPlan->update($updateData);
        
        return back()->with('success', 'Action plan updated successfully!');
    }
    
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action_plans' => 'required|array',
            'action_plans.*' => 'exists:action_plans,id',
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
            'assigned_to' => 'nullable|exists:users,id'
        ]);
        
        $updateData = ['status' => $request->status];
        
        if ($request->filled('assigned_to')) {
            $updateData['assigned_to'] = $request->assigned_to;
        }
        
        if ($request->status === 'Completed') {
            $updateData['completed_at'] = now();
        }
        
        ActionPlan::whereIn('id', $request->action_plans)->update($updateData);
        
        return back()->with('success', count($request->action_plans) . ' action plan(s) updated successfully!');
    }
    
    public function exportExcel(Request $request)
    {
        // Similar filtering logic as index method
        $user = auth()->user();
        $isAdmin = $user->roles && ($user->roles->contains('name', 'admin') || $user->roles->contains('name', 'Super Admin'));
        
        $query = ActionPlan::with(['storeVisit', 'assignedUser', 'qscChecklist']);
        
        if (!$isAdmin) {
            $userRestaurantNames = $user->restaurants()->pluck('name');
            $query->whereHas('storeVisit', function($q) use ($userRestaurantNames) {
                $q->whereIn('restaurant_name', $userRestaurantNames);
            });
        }
        
        // Apply same filters as index
        $this->applyFilters($query, $request);
        
        $actionPlans = $query->get();
        
        // Generate filename with timestamp
        $filename = 'Action_Plans_' . date('Y-m-d_His') . '.xlsx';
        
        // Return Excel export
        return Excel::download(new ActionPlansExport($actionPlans), $filename);
    }
    
    private function applyFilters($query, $request)
    {
        if ($request->filled('restaurant')) {
            $query->whereHas('storeVisit', function($q) use ($request) {
                $q->where('restaurant_name', 'LIKE', '%' . $request->restaurant . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item', 'LIKE', '%' . $search . '%')
                  ->orWhere('issue', 'LIKE', '%' . $search . '%')
                  ->orWhere('action_required', 'LIKE', '%' . $search . '%');
            });
        }
    }
}
