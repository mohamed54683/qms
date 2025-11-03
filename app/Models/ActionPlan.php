<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionPlan extends Model
{
    protected $fillable = [
        'store_visit_id',
        'qsc_checklist_id',
        'item',
        'issue',
        'action_required',
        'priority',
        'status',
        'assigned_to',
        'due_date',
        'notes',
        'completed_at',
        // Workflow fields
        'is_approved',
        'approved_at',
        'approved_by',
        // Visit context
        'visit_purpose',
        'area_manager',
        'restaurant_manager',
        // 5W1H fields
        'what',
        'where',
        'why',
        'how',
        'who',
        'when_detail',
        'comment'
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'approved_at' => 'datetime',
            'is_approved' => 'boolean',
        ];
    }

    // Relationships
    public function storeVisit(): BelongsTo
    {
        return $this->belongsTo(StoreVisit::class);
    }

    public function qscChecklist(): BelongsTo
    {
        return $this->belongsTo(QscChecklist::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function images()
    {
        return $this->hasMany(ActionPlanImage::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'In Progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'High');
    }

    /**
     * Scope to filter action plans by user's assigned restaurants
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserRestaurants($query, $user)
    {
        // Check if user is admin - admins see all action plans
        if ($user->roles && $user->roles->contains('name', 'admin')) {
            return $query;
        }
        
        // Get user's assigned restaurant IDs
        $restaurantIds = $user->restaurants()->pluck('restaurants.id');
        
        if ($restaurantIds->isEmpty()) {
            // User has no restaurants assigned - return empty result
            return $query->whereRaw('1 = 0');
        }
        
        // Get restaurant names from IDs
        $restaurantNames = Restaurant::whereIn('id', $restaurantIds)->pluck('name');
        
        // Filter action plans through their store visits' restaurant names
        return $query->whereHas('storeVisit', function($q) use ($restaurantNames) {
            $q->whereIn('restaurant_name', $restaurantNames);
        });
    }
}

