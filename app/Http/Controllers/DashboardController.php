<?php

namespace App\Http\Controllers;

use App\Models\StoreVisit;
use App\Models\ActionPlan;
use App\Models\Restaurant;
use App\Models\User;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
        
        // Get filter parameter (default: 'week')
        $filter = $request->input('filter', 'week');
        
        // Get current date ranges
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $currentWeek = Carbon::now()->startOfWeek();
        $today = Carbon::today();
        
        // Set date range based on filter
        switch ($filter) {
            case 'today':
                $dateFrom = Carbon::today();
                $dateTo = Carbon::tomorrow();
                break;
            case 'week':
                $dateFrom = Carbon::now()->startOfWeek();
                $dateTo = Carbon::now()->endOfWeek();
                break;
            case 'month':
            default:
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo = Carbon::now()->endOfMonth();
                break;
        }
        
        // Base query with restaurant filtering
        $visitsQuery = StoreVisit::query();
        $actionPlansQuery = ActionPlan::query();
        
        // Apply access control filters
        if (!$isAdmin && !$isAreaManager) {
            // Regular users: filter by assigned restaurants
            $visitsQuery->forUserRestaurants($user);
            $actionPlansQuery->forUserRestaurants($user);
        } elseif ($isAreaManager && !$isAdmin) {
            // Area managers: filter by managed restaurants
            $managedRestaurantIds = $user->managedRestaurants()->pluck('id');
            $visitsQuery->whereIn('restaurant_id', $managedRestaurantIds);
            $actionPlansQuery->whereHas('storeVisit', function($query) use ($managedRestaurantIds) {
                $query->whereIn('restaurant_id', $managedRestaurantIds);
            });
        }
        
        // Apply area manager filter (for admins)
        if ($request->filled('area_manager') && $isAdmin) {
            $areaManagerId = $request->area_manager;
            $areaManagerUser = User::find($areaManagerId);
            
            if ($areaManagerUser) {
                $assignedRestaurants = $areaManagerUser->restaurants->pluck('name')->toArray();
                if (!empty($assignedRestaurants)) {
                    $visitsQuery->whereIn('restaurant_name', $assignedRestaurants);
                    $actionPlansQuery->whereHas('storeVisit', function($query) use ($assignedRestaurants) {
                        $query->whereIn('restaurant_name', $assignedRestaurants);
                    });
                } else {
                    // If area manager has no restaurants, show nothing
                    $visitsQuery->whereRaw('1 = 0');
                    $actionPlansQuery->whereRaw('1 = 0');
                }
            }
        }
        
        // Apply date filter to base queries
        $visitsQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        $actionPlansQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        // ==== KPI SUMMARY CARDS (8 Metrics) ====
        if ($isAdmin) {
            $totalRestaurants = Restaurant::count();
        } elseif ($isAreaManager) {
            $totalRestaurants = $user->managedRestaurants()->count();
        } else {
            $totalRestaurants = $user->restaurants()->count();
        }
        $totalUsers = User::count();
        $visitsThisMonth = (clone $visitsQuery)->where('created_at', '>=', $currentMonth)->count();
        
        // Calculate average QSC score
        $avgQscScore = (clone $visitsQuery)
            ->whereNotNull('score')
            ->avg('score');
        $avgQscScore = $avgQscScore ? round($avgQscScore, 1) : 0;
        
        // Action Plans metrics
        $pendingActionPlans = (clone $actionPlansQuery)->where('status', 'Pending')->count();
        $completedActionPlans = (clone $actionPlansQuery)->where('status', 'Completed')->count();
        
        // TTF Compliance Rate (placeholder - needs actual TTF table)
        $ttfComplianceRate = 92; // TODO: Calculate from actual TTF tracking data
        
        // Temperature Compliance (placeholder - needs actual temperature table)
        $temperatureCompliance = 88; // TODO: Calculate from actual temperature tracking data
        
        // Visit statistics
        $totalVisits = (clone $visitsQuery)->count();
        $completedVisits = (clone $visitsQuery)->where(function($query) {
            $query->whereNotNull('completed_at')
                  ->orWhereIn('status', ['Completed', 'Approved']);
        })->count();
        $pendingVisits = (clone $visitsQuery)->where(function($query) {
            $query->whereNull('completed_at')
                  ->whereNotIn('status', ['Completed', 'Approved']);
        })->count();
        $visitsThisWeek = (clone $visitsQuery)->where('created_at', '>=', $currentWeek)->count();
        $visitsToday = (clone $visitsQuery)->whereDate('created_at', $today)->count();
        
        // Action Plan statistics
        $totalActionPlans = (clone $actionPlansQuery)->count();
        $inProgressActionPlans = (clone $actionPlansQuery)->where('status', 'In Progress')->count();
        $cancelledActionPlans = (clone $actionPlansQuery)->where('status', 'Cancelled')->count();
        $highPriorityActionPlans = (clone $actionPlansQuery)->where('priority', 'High')->count();
        
        // Overdue action plans
        $overdueActionPlans = (clone $actionPlansQuery)
            ->where('due_date', '<', $today)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();
        
        // ==== BRANCH & AREA PERFORMANCE ====
        $topBranches = $this->getTopBranches($visitsQuery, 5);
        $lowBranches = $this->getTopBranches($visitsQuery, 5, false);
        $areaPerformance = $this->getAreaPerformance($visitsQuery);
        
        // ==== ISSUE CATEGORIES ====
        $issueCategories = $this->getIssueCategories($actionPlansQuery);
        
        // ==== USER & ROLE ANALYTICS ====
        $usersByRole = $this->getUsersByRole();
        $activeUsersToday = User::whereDate('last_login_at', $today)->count();
        
        // ==== RECENT ACTIVITIES ====
        $recentActivities = $this->getRecentActivities($user, $isAdmin, $visitsQuery, $actionPlansQuery);
        
        // ==== CHART DATA ====
        $visitTrends = $this->getVisitTrends($visitsQuery);
        $qscScoreTrend = $this->getQscScoreTrend($visitsQuery);
        $visitsByAreaManager = $this->getVisitsByAreaManager($visitsQuery);
        $actionPlanStatus = $this->getActionPlanStatus($actionPlansQuery);
        $performanceData = $this->getPerformanceData($visitsQuery);
        
        // ==== RECENT VISITS & ACTION PLANS ====
        $recentVisits = (clone $visitsQuery)
            ->with(['user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'restaurant_name' => $visit->restaurant_name ?? 'Unknown Restaurant',
                    'user_name' => $visit->user->name ?? 'Unknown User',
                    'visit_date' => $visit->visit_date,
                    'created_at' => $visit->created_at->format('Y-m-d H:i'),
                    'status' => $visit->status ?? 'Draft',
                    'score' => $visit->score ?? 0,
                ];
            });
        
        $recentActionPlansList = (clone $actionPlansQuery)
            ->with(['storeVisit'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'item' => $plan->item,
                    'restaurant' => $plan->storeVisit->restaurant_name ?? 'Unknown',
                    'status' => $plan->status,
                    'priority' => $plan->priority,
                    'due_date' => $plan->due_date,
                    'days_overdue' => $plan->due_date && Carbon::parse($plan->due_date)->isPast() && !in_array($plan->status, ['Completed', 'Cancelled']) 
                        ? Carbon::parse($plan->due_date)->diffInDays(Carbon::now()) 
                        : 0,
                ];
            });
        
        // Performance metrics
        $completionRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0;
        $actionPlanResolutionRate = $totalActionPlans > 0 ? round(($completedActionPlans / $totalActionPlans) * 100, 1) : 0;
        
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
        
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $user,
                'isAdmin' => $isAdmin,
            ],
            // Current filter
            'currentFilter' => $filter,
            // KPI Summary Cards (8 metrics)
            'kpis' => [
                'totalRestaurants' => $totalRestaurants,
                'totalUsers' => $totalUsers,
                'visitsThisMonth' => $visitsThisMonth,
                'avgQscScore' => $avgQscScore,
                'pendingActionPlans' => $pendingActionPlans,
                'completedActionPlans' => $completedActionPlans,
                'ttfComplianceRate' => $ttfComplianceRate,
                'temperatureCompliance' => $temperatureCompliance,
            ],
            // Additional Stats
            'stats' => [
                'totalVisits' => $totalVisits,
                'completedVisits' => $completedVisits,
                'pendingVisits' => $pendingVisits,
                'visitsThisWeek' => $visitsThisWeek,
                'visitsToday' => $visitsToday,
                'totalActionPlans' => $totalActionPlans,
                'inProgressActionPlans' => $inProgressActionPlans,
                'overdueActionPlans' => $overdueActionPlans,
                'highPriorityActionPlans' => $highPriorityActionPlans,
                'completionRate' => $completionRate,
                'actionPlanResolutionRate' => $actionPlanResolutionRate,
            ],
            // Branch & Area Performance
            'branchPerformance' => [
                'topBranches' => $topBranches,
                'lowBranches' => $lowBranches,
                'areaPerformance' => $areaPerformance,
            ],
            // Charts Data
            'charts' => [
                'visitTrends' => $visitTrends,
                'qscScoreTrend' => $qscScoreTrend,
                'visitsByAreaManager' => $visitsByAreaManager,
                'actionPlanStatus' => $actionPlanStatus,
                'issueCategories' => $issueCategories,
                'performance' => $performanceData,
            ],
            // User Analytics
            'userAnalytics' => [
                'usersByRole' => $usersByRole,
                'activeUsersToday' => $activeUsersToday,
                'totalUsers' => $totalUsers,
            ],
            // Recent Data
            'recentVisits' => $recentVisits,
            'recentActionPlans' => $recentActionPlansList,
            'recentActivities' => $recentActivities,
            // User Role Information
            'isAreaManager' => $isAreaManager,
            // Filtering
            'areaManagers' => $areaManagers,
            'canViewAreaManagerFilter' => $canViewAreaManagerFilter,
            'filters' => $request->only(['area_manager', 'filter']),
        ]);
    }
    
    // ==== HELPER METHODS ====
    
    private function getTopBranches($visitsQuery, $limit = 5, $top = true)
    {
        $query = (clone $visitsQuery)
            ->select('restaurant_name', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as visit_count'))
            ->whereNotNull('score')
            ->groupBy('restaurant_name');
        
        if ($top) {
            $query->orderByDesc('avg_score');
        } else {
            $query->orderBy('avg_score');
        }
        
        return $query->take($limit)->get()->map(function($branch) {
            return [
                'name' => $branch->restaurant_name,
                'score' => round($branch->avg_score, 1),
                'visits' => $branch->visit_count,
            ];
        });
    }
    
    private function getAreaPerformance($visitsQuery)
    {
        // Placeholder - would need area field in restaurants table
        return [
            ['area' => 'North', 'visits' => 45, 'score' => 88, 'plans' => 12],
            ['area' => 'Central', 'visits' => 52, 'score' => 92, 'plans' => 8],
            ['area' => 'South', 'visits' => 28, 'score' => 85, 'plans' => 15],
        ];
    }
    
    private function getIssueCategories($actionPlansQuery)
    {
        // Analyze action plan items to categorize issues
        $plans = (clone $actionPlansQuery)->get();
        
        $categories = [
            'Quality' => 0,
            'Equipment' => 0,
            'Cleanliness' => 0,
            'Staff' => 0,
            'Training' => 0,
            'Other' => 0,
        ];
        
        foreach ($plans as $plan) {
            $item = strtolower($plan->item ?? '');
            if (str_contains($item, 'quality') || str_contains($item, 'food') || str_contains($item, 'taste')) {
                $categories['Quality']++;
            } elseif (str_contains($item, 'equipment') || str_contains($item, 'machine') || str_contains($item, 'broken')) {
                $categories['Equipment']++;
            } elseif (str_contains($item, 'clean') || str_contains($item, 'hygiene') || str_contains($item, 'sanit')) {
                $categories['Cleanliness']++;
            } elseif (str_contains($item, 'staff') || str_contains($item, 'employee') || str_contains($item, 'team')) {
                $categories['Staff']++;
            } elseif (str_contains($item, 'training') || str_contains($item, 'learn') || str_contains($item, 'education')) {
                $categories['Training']++;
            } else {
                $categories['Other']++;
            }
        }
        
        return collect($categories)->map(function($count, $category) {
            return ['category' => $category, 'count' => $count];
        })->values();
    }
    
    private function getUsersByRole()
    {
        return Role::withCount('users')
            ->get()
            ->map(function($role) {
                return [
                    'role' => ucfirst(str_replace('_', ' ', $role->name)),
                    'count' => $role->users_count,
                ];
            });
    }
    
    private function getRecentActivities($user, $isAdmin, $visitsQuery, $actionPlansQuery)
    {
        $activities = collect();
        
        // Recent visits (last 5)
        $recentVisits = (clone $visitsQuery)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();
        
        foreach ($recentVisits as $visit) {
            $activities->push([
                'id' => 'visit_' . $visit->id,
                'type' => $visit->status === 'Completed' || $visit->status === 'Approved' ? 'visit_completed' : 'visit_created',
                'icon' => $visit->status === 'Completed' || $visit->status === 'Approved' ? '✅' : '📋',
                'title' => $visit->status === 'Completed' || $visit->status === 'Approved' ? 'Store Visit Completed' : 'Store Visit Submitted',
                'description' => ($visit->user->name ?? 'Unknown') . ' — ' . ($visit->restaurant_name ?? 'Unknown Restaurant'),
                'time' => $visit->created_at->diffForHumans(),
                'timestamp' => $visit->created_at,
            ]);
        }
        
        // Recent action plans (last 3)
        $recentPlans = (clone $actionPlansQuery)
            ->with(['storeVisit.user'])
            ->latest()
            ->take(3)
            ->get();
        
        foreach ($recentPlans as $plan) {
            $activities->push([
                'id' => 'plan_' . $plan->id,
                'type' => $plan->status === 'Completed' ? 'plan_completed' : 'plan_created',
                'icon' => $plan->status === 'Completed' ? '✅' : '⚙️',
                'title' => $plan->status === 'Completed' ? 'Action Plan Completed' : 'Action Plan Created',
                'description' => $plan->item . ' — ' . ($plan->storeVisit->restaurant_name ?? 'Unknown'),
                'time' => $plan->created_at->diffForHumans(),
                'timestamp' => $plan->created_at,
            ]);
        }
        
        // Sort by timestamp and take top 10
        return $activities->sortByDesc('timestamp')->take(10)->values();
    }
    
    private function getQscScoreTrend($visitsQuery)
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $avgScore = (clone $visitsQuery)
                ->whereDate('created_at', $date)
                ->whereNotNull('score')
                ->avg('score');
            $last7Days->push([
                'date' => $date->format('M d'),
                'score' => $avgScore ? round($avgScore, 1) : 0
            ]);
        }
        
        return [
            'labels' => $last7Days->pluck('date')->toArray(),
            'data' => $last7Days->pluck('score')->toArray(),
        ];
    }
    
    private function getVisitsByAreaManager($visitsQuery)
    {
        // Get the date range from the main query
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        
        // Create a fresh query to avoid column ambiguity issues
        $visitsByAreaManager = StoreVisit::query()
            ->whereBetween('store_visits.created_at', [$startDate, $endDate])
            ->join('restaurants', 'store_visits.restaurant_name', '=', 'restaurants.name')
            ->join('restaurant_user', 'restaurants.id', '=', 'restaurant_user.restaurant_id')
            ->join('users', 'restaurant_user.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'Area Manager')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('users.name as area_manager_name', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('visit_count', 'desc')
            ->get();

        $labels = $visitsByAreaManager->pluck('area_manager_name')->take(10)->toArray();
        $data = $visitsByAreaManager->pluck('visit_count')->take(10)->toArray();
        
        // If no data, show placeholder
        if (empty($labels)) {
            return [
                'labels' => ['No area managers found'],
                'data' => [0],
                'colors' => ['#e5e7eb'],
            ];
        }

        // Generate colors for each area manager
        $colors = [];
        $baseColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'];
        for ($i = 0; $i < count($labels); $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }
    
    private function getActionPlanStatus($actionPlansQuery)
    {
        $pending = (clone $actionPlansQuery)->where('status', 'Pending')->count();
        $inProgress = (clone $actionPlansQuery)->where('status', 'In Progress')->count();
        $completed = (clone $actionPlansQuery)->where('status', 'Completed')->count();
        
        return [
            'labels' => ['Pending', 'In Progress', 'Completed'],
            'data' => [$pending, $inProgress, $completed],
            'colors' => ['#f59e0b', '#3b82f6', '#10b981'],
        ];
    }
    
    private function getVisitTrends($visitsQuery)
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = (clone $visitsQuery)
                ->whereDate('created_at', $date)
                ->count();
            $last7Days->push([
                'date' => Carbon::parse($date)->format('M d'),
                'count' => $count
            ]);
        }
        
        return [
            'labels' => $last7Days->pluck('date')->toArray(),
            'data' => $last7Days->pluck('count')->toArray(),
        ];
    }
    
    private function getPerformanceData($visitsQuery)
    {
        $performance = (clone $visitsQuery)
            ->select('restaurant_name', DB::raw('AVG(score) as avg_score'))
            ->whereNotNull('score')
            ->groupBy('restaurant_name')
            ->orderByDesc('avg_score')
            ->take(10)
            ->get();
        
        return [
            'labels' => $performance->pluck('restaurant_name')->toArray(),
            'data' => $performance->pluck('avg_score')->map(fn($s) => round($s, 1))->toArray(),
        ];
    }
    
    private function calculateVisitCompletionPercentage($visit)
    {
        // If completed_at is set, it's 100% complete
        if ($visit->completed_at) {
            return 100;
        }
        
        // Calculate based on status
        $statusPercentages = [
            'Draft' => 10,
            'Submitted' => 30,
            'Under Review' => 50,
            'Action Plans Created' => 70,
            'Completed' => 100,
            'Approved' => 100,
            'Rejected' => 25
        ];
        
        return $statusPercentages[$visit->status] ?? 10;
    }
}