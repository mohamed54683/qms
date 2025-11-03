<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreVisit extends Model
{
    protected $fillable = [
        'user_id', 'restaurant_name', 'mic', 'visit_date', 'score', 'purpose_of_visit',
        
        // Section A: Customer / QSC
        'oca_board_followed', 'oca_board_comments', 'staff_know_duty', 'staff_duty_comments',
        'coaching_directing', 'coaching_comments',
        
        // Section B: Cashier
        'smile_greetings', 'smile_comments', 'suggestive_selling', 'suggestive_comments',
        'offer_promotion', 'promotion_comments', 'thank_direction', 'thank_comments',
        
        // Section C: Service Standards
        'team_work_hustle', 'teamwork_comments', 'order_accuracy', 'accuracy_comments',
        'service_time', 'service_comments', 'dine_in', 'dine_comments', 'take_out', 'takeout_comments',
        'family', 'family_comments', 'delivery', 'delivery_comments', 'drive_thru', 'drive_comments',
        
        // Section D: Financials
        'weekly_schedule', 'schedule_comments', 'mod_financial_goal', 'financial_comments',
        'sales_objectives', 'sales_comments', 'cash_policies', 'cash_comments',
        'daily_waste', 'waste_comments',
        
        // Section E: Quality / Pathing
        'ttf_followed', 'ttf_comments', 'sandwich_assembly', 'assembly_comments',
        'qsc_completed', 'qsc_comments', 'oil_standards', 'oil_comments',
        'day_labels', 'labels_comments', 'equipment_working', 'equipment_comments',
        'fryer_condition', 'fryer_comments', 'vegetable_prep', 'vegetable_comments',
        'employee_appearance', 'appearance_comments',
        
        // Section F: Cleanliness
        'equipment_wrapped', 'wrapped_comments', 'sink_setup', 'sink_comments',
        'sanitizer_standard', 'sanitizer_comments', 'dining_area_clean', 'dining_comments',
        'restroom_clean', 'restroom_comments',
        
        // Section 8: Follow-up
        'last_visit_date', 'last_visit_summary', 'last_visit_update', 'other_follow_up',
        
        // Section 9: Observation Summary
        'what_did_you_see', 'why_had_issue', 'how_to_improve', 'who_when_responsible',
        
        // Section 10: Action Plan (Auto-Generated)
        'action_plan_items',
        
        // Section 11: Report Management
        'performance_score_percentage', 'report_timestamp', 'digital_signature_mic', 
        'digital_signature_reviewer', 'report_type', 'export_history',
        'created_by_name', 'reviewer_name', 'reviewed_at', 'approved_at',
        
        // Additional fields
        'general_comments', 'photos', 'status', 'attachments'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'last_visit_date' => 'date',
        'purpose_of_visit' => 'array',
        'photos' => 'array',
        'action_plan_items' => 'array',
        'export_history' => 'array',
        'attachments' => 'array',
        'report_timestamp' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'performance_score_percentage' => 'decimal:2',
        
        // Cast boolean fields
        'oca_board_followed' => 'boolean',
        'staff_know_duty' => 'boolean',
        'coaching_directing' => 'boolean',
        'smile_greetings' => 'boolean',
        'suggestive_selling' => 'boolean',
        'offer_promotion' => 'boolean',
        'thank_direction' => 'boolean',
        'team_work_hustle' => 'boolean',
        'order_accuracy' => 'boolean',
        'service_time' => 'boolean',
        'dine_in' => 'boolean',
        'take_out' => 'boolean',
        'family' => 'boolean',
        'delivery' => 'boolean',
        'drive_thru' => 'boolean',
        'weekly_schedule' => 'boolean',
        'mod_financial_goal' => 'boolean',
        'sales_objectives' => 'boolean',
        'cash_policies' => 'boolean',
        'daily_waste' => 'boolean',
        'ttf_followed' => 'boolean',
        'sandwich_assembly' => 'boolean',
        'qsc_completed' => 'boolean',
        'oil_standards' => 'boolean',
        'day_labels' => 'boolean',
        'equipment_working' => 'boolean',
        'fryer_condition' => 'boolean',
        'vegetable_prep' => 'boolean',
        'employee_appearance' => 'boolean',
        'equipment_wrapped' => 'boolean',
        'sink_setup' => 'boolean',
        'sanitizer_standard' => 'boolean',
        'dining_area_clean' => 'boolean',
        'restroom_clean' => 'boolean',
    ];

    /**
     * Get the user that owns the store visit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the action plans associated with this store visit.
     */
    public function actionPlans(): HasMany
    {
        return $this->hasMany(ActionPlan::class);
    }

    /**
     * Get the restaurant associated with this store visit.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_name', 'name');
    }

    /**
     * Calculate the score based on Y/N answers
     */
    public function calculateScore(): int
    {
        $totalQuestions = 34; // Total number of questions from all sections
        $yesCount = 0;

        // Count all the 'Yes' answers (true values)
        $booleanFields = [
            'oca_board_followed', 'staff_know_duty', 'coaching_directing',
            'smile_greetings', 'suggestive_selling', 'offer_promotion', 'thank_direction',
            'team_work_hustle', 'order_accuracy', 'service_time', 'dine_in', 'take_out', 'family', 'delivery', 'drive_thru',
            'weekly_schedule', 'mod_financial_goal', 'sales_objectives', 'cash_policies', 'daily_waste',
            'ttf_followed', 'sandwich_assembly', 'qsc_completed', 'oil_standards', 'day_labels', 'equipment_working', 'fryer_condition', 'vegetable_prep', 'employee_appearance',
            'equipment_wrapped', 'sink_setup', 'sanitizer_standard', 'dining_area_clean', 'restroom_clean'
        ];

        foreach ($booleanFields as $field) {
            if ($this->$field === true) {
                $yesCount++;
            }
        }

        return $totalQuestions > 0 ? round(($yesCount / $totalQuestions) * 100) : 0;
    }

    /**
     * Get items that need action (answered 'No')
     */
    public function getActionItems(): array
    {
        $actionItems = [];
        
        $questionMap = [
            'oca_board_followed' => ['question' => 'OCA Board is Completely Followed/Communicated', 'comment_field' => 'oca_board_comments'],
            'staff_know_duty' => ['question' => 'Staff Know their Side Duty', 'comment_field' => 'staff_duty_comments'],
            'coaching_directing' => ['question' => 'Coaching and Directing', 'comment_field' => 'coaching_comments'],
            'smile_greetings' => ['question' => 'Smile and Friendly Greetings', 'comment_field' => 'smile_comments'],
            'suggestive_selling' => ['question' => 'Suggestive Selling', 'comment_field' => 'suggestive_comments'],
            'offer_promotion' => ['question' => 'Offer new Promotion', 'comment_field' => 'promotion_comments'],
            'thank_direction' => ['question' => 'Saying Thank You and Provides Direction', 'comment_field' => 'thank_comments'],
            'team_work_hustle' => ['question' => 'Team Work and Hustle', 'comment_field' => 'teamwork_comments'],
            'order_accuracy' => ['question' => 'Order Accuracy', 'comment_field' => 'accuracy_comments'],
            'service_time' => ['question' => 'Service Time', 'comment_field' => 'service_comments'],
            'dine_in' => ['question' => 'Dine In', 'comment_field' => 'dine_comments'],
            'take_out' => ['question' => 'Take Out', 'comment_field' => 'takeout_comments'],
            'family' => ['question' => 'Family', 'comment_field' => 'family_comments'],
            'delivery' => ['question' => 'Delivery', 'comment_field' => 'delivery_comments'],
            'drive_thru' => ['question' => 'Drive Thru', 'comment_field' => 'drive_comments'],
            'weekly_schedule' => ['question' => 'Weekly Schedule and Overtime', 'comment_field' => 'schedule_comments'],
            'mod_financial_goal' => ['question' => 'MOD aware of Financial Goal', 'comment_field' => 'financial_comments'],
            'sales_objectives' => ['question' => 'Sales (Cashier Objectives)', 'comment_field' => 'sales_comments'],
            'cash_policies' => ['question' => 'Cash Policies followed Spot Check', 'comment_field' => 'cash_comments'],
            'daily_waste' => ['question' => 'Daily Waste Followed Properly (Daily)', 'comment_field' => 'waste_comments'],
            'ttf_followed' => ['question' => 'TTF followed properly', 'comment_field' => 'ttf_comments'],
            'sandwich_assembly' => ['question' => 'Sandwich Assembly being followed', 'comment_field' => 'assembly_comments'],
            'qsc_completed' => ['question' => 'QSC was completed and followed properly', 'comment_field' => 'qsc_comments'],
            'oil_standards' => ['question' => 'Oil/Shortening Meets Standards', 'comment_field' => 'oil_comments'],
            'day_labels' => ['question' => 'Day Labels updated', 'comment_field' => 'labels_comments'],
            'equipment_working' => ['question' => 'Equipments are all working properly', 'comment_field' => 'equipment_comments'],
            'fryer_condition' => ['question' => 'Fryer Basket in Good Condition (not broken or rusty)', 'comment_field' => 'fryer_comments'],
            'vegetable_prep' => ['question' => 'Vegetable Preparation meets standards and Salad Prep', 'comment_field' => 'vegetable_comments'],
            'employee_appearance' => ['question' => 'Employee Appearance well groomed', 'comment_field' => 'appearance_comments'],
            'equipment_wrapped' => ['question' => 'Equipment are wrapped and hang', 'comment_field' => 'wrapped_comments'],
            'sink_setup' => ['question' => 'Compartment sink are set-up properly', 'comment_field' => 'sink_comments'],
            'sanitizer_standard' => ['question' => 'Sanitizer meets standard', 'comment_field' => 'sanitizer_comments'],
            'dining_area_clean' => ['question' => 'Dining Area/Family area no busting', 'comment_field' => 'dining_comments'],
            'restroom_clean' => ['question' => 'CR or handwash area has tissue and clean', 'comment_field' => 'restroom_comments']
        ];

        foreach ($questionMap as $field => $data) {
            if ($this->$field === false) {
                $commentField = $data['comment_field'];
                $actionItems[] = [
                    'question' => $data['question'],
                    'field' => $field,
                    'comment' => $this->$commentField ?? ''
                ];
            }
        }

        return $actionItems;
    }

    // New relationships for workflow
    public function answers(): HasMany
    {
        return $this->hasMany(StoreVisitAnswer::class, 'visit_id');
    }

    public function areaManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'area_manager_id');
    }

    public function storeManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_manager_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to filter visits by user's assigned restaurants
     */
    public function scopeForUserRestaurants($query, $user = null)
    {
        // TEMPORARILY DISABLED FOR TESTING - RETURN ALL VISITS
        return $query;
        
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        // If user has admin role, return all visits
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Get user's assigned restaurant names
        $restaurantNames = $user->restaurants->pluck('name')->toArray();

        if (empty($restaurantNames)) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        return $query->whereIn('restaurant_name', $restaurantNames);
    }

    /**
     * Create action plans for this visit with images
     */
    public function createActionPlansWithImages($questionImagePaths = [])
    {
        $actionItems = $this->getActionItems();
        $createdActionPlans = [];

        foreach ($actionItems as $item) {
            // Create the action plan
            $actionPlan = \App\Models\ActionPlan::create([
                'store_visit_id' => $this->id,
                'item' => $item['question'],
                'issue' => $item['question'] . ' - Issue identified during visit',
                'action_required' => 'Address: ' . $item['comment'],
                'priority' => 'Medium',
                'status' => 'Pending',
                'due_date' => now()->addDays(7),
                'notes' => $item['comment'],
                // 5W1H from visit context
                'what' => $this->what_did_you_see ?? '',
                'why' => $this->why_had_issue ?? '',
                'how' => $this->how_to_improve ?? '',
                'who' => $this->who_when_responsible ?? '',
                'comment' => $item['comment'],
            ]);

            // Attach images if they exist for this field
            $fieldName = $item['field'];
            if (isset($questionImagePaths[$fieldName]) && is_array($questionImagePaths[$fieldName])) {
                foreach ($questionImagePaths[$fieldName] as $imagePath) {
                    \App\Models\ActionPlanImage::create([
                        'action_plan_id' => $actionPlan->id,
                        'image_path' => $imagePath,
                        'field_name' => $fieldName,
                    ]);
                }
            }

            $createdActionPlans[] = $actionPlan;
        }

        return $createdActionPlans;
    }
}
