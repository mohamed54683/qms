<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreVisit;
use App\Models\ActionPlan;
use Illuminate\Support\Facades\DB;

class GenerateActionPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:action-plans {--all : Generate for all visits} {--visit= : Generate for specific visit ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate action plans from store visits with No answers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate action plans...');
        
        if ($this->option('visit')) {
            $visits = StoreVisit::where('id', $this->option('visit'))->get();
        } elseif ($this->option('all')) {
            $visits = StoreVisit::all();
        } else {
            // Generate for visits that don't have action plans yet
            $visitsWithActionPlans = ActionPlan::distinct()->pluck('store_visit_id');
            $visits = StoreVisit::whereNotIn('id', $visitsWithActionPlans)->get();
        }
        
        $totalActionPlans = 0;
        
        foreach ($visits as $visit) {
            $actionPlansCreated = $this->createActionPlansForVisit($visit);
            $totalActionPlans += $actionPlansCreated;
            
            if ($actionPlansCreated > 0) {
                $this->info("Created {$actionPlansCreated} action plans for visit ID {$visit->id} ({$visit->restaurant_name})");
            }
        }
        
        $this->info("Completed! Total action plans created: {$totalActionPlans}");
    }
    
    private function createActionPlansForVisit($visit)
    {
        // Define all the boolean fields that represent questions
        $booleanFields = [
            'oca_board_followed', 'staff_know_duty', 'coaching_directing', 'smile_greetings',
            'suggestive_selling', 'offer_promotion', 'thank_direction', 'team_work_hustle',
            'order_accuracy', 'service_time', 'dine_in', 'take_out', 'family', 'delivery',
            'daily_waste', 'ttf_followed', 'sandwich_assembly', 'qsc_completed', 'oil_standards',
            'day_labels', 'equipment_working', 'fryer_condition', 'vegetable_prep',
            'employee_appearance', 'equipment_wrapped', 'sink_setup', 'sanitizer_standard',
            'dining_area_clean', 'restroom_clean'
        ];
        
        $actionPlans = [];
        
        foreach ($booleanFields as $field) {
            // If the field is null (unanswered) or false (not compliant), create an action plan
            if (is_null($visit->$field) || $visit->$field === false || $visit->$field === 0) {
                $actionPlans[] = [
                    'store_visit_id' => $visit->id,
                    'item' => $this->getFieldLabel($field),
                    'issue' => is_null($visit->$field) ? 'Not evaluated' : 'Not compliant',
                    'action_required' => $this->getActionRequired($field),
                    'priority' => is_null($visit->$field) ? 'Medium' : 'High',
                    'status' => 'Pending',
                    // Workflow fields - auto-approve since generated from confirmed visits
                    'is_approved' => true,
                    'approved_at' => now(),
                    'approved_by' => 1, // Default to admin user
                    // Visit context information
                    'visit_purpose' => $visit->purpose ?? 'Quality Control',
                    'area_manager' => $visit->user->name ?? 'Area Manager',
                    'restaurant_manager' => $this->getRestaurantManager($visit->restaurant_name ?? $visit->restaurant),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        if (!empty($actionPlans)) {
            ActionPlan::insert($actionPlans);
        }
        
        return count($actionPlans);
    }
    
    private function getFieldLabel($field)
    {
        $labels = [
            'oca_board_followed' => 'OCA Board Followed',
            'staff_know_duty' => 'Staff Know Their Duty',
            'coaching_directing' => 'Coaching & Directing',
            'smile_greetings' => 'Smile & Greetings',
            'suggestive_selling' => 'Suggestive Selling',
            'offer_promotion' => 'Offer Promotion',
            'thank_direction' => 'Thank & Direction',
            'team_work_hustle' => 'Team Work & Hustle',
            'order_accuracy' => 'Order Accuracy',
            'service_time' => 'Service Time',
            'dine_in' => 'Dine In Service',
            'take_out' => 'Take Out Service',
            'family' => 'Family Service',
            'delivery' => 'Delivery Service',
            'daily_waste' => 'Daily Waste Management',
            'ttf_followed' => 'TTF Standards Followed',
            'sandwich_assembly' => 'Sandwich Assembly Standards',
            'qsc_completed' => 'QSC Completed',
            'oil_standards' => 'Oil Standards',
            'day_labels' => 'Day Labels',
            'equipment_working' => 'Equipment Working Properly',
            'fryer_condition' => 'Fryer Condition',
            'vegetable_prep' => 'Vegetable Preparation',
            'employee_appearance' => 'Employee Appearance',
            'equipment_wrapped' => 'Equipment Wrapped',
            'sink_setup' => 'Sink Setup',
            'sanitizer_standard' => 'Sanitizer Standards',
            'dining_area_clean' => 'Dining Area Clean',
            'restroom_clean' => 'Restroom Clean'
        ];
        
        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
    
    private function getActionRequired($field)
    {
        $actions = [
            'oca_board_followed' => 'Ensure OCA board procedures are properly followed',
            'staff_know_duty' => 'Provide additional training on job responsibilities',
            'coaching_directing' => 'Implement proper coaching and directing procedures',
            'smile_greetings' => 'Train staff on proper greeting procedures',
            'suggestive_selling' => 'Provide suggestive selling training',
            'offer_promotion' => 'Ensure staff are informed about current promotions',
            'thank_direction' => 'Improve customer thanking and direction procedures',
            'team_work_hustle' => 'Enhance team coordination and work pace',
            'order_accuracy' => 'Review and improve order taking procedures',
            'service_time' => 'Optimize service time to meet standards',
            'dine_in' => 'Improve dine-in service quality',
            'take_out' => 'Enhance take-out service procedures',
            'family' => 'Improve family-friendly service approach',
            'delivery' => 'Optimize delivery service procedures',
            'daily_waste' => 'Implement proper waste management procedures',
            'ttf_followed' => 'Ensure TTF (Time, Temperature, Freshness) standards are met',
            'sandwich_assembly' => 'Review sandwich assembly standards and procedures',
            'qsc_completed' => 'Complete QSC (Quality, Service, Cleanliness) checklist',
            'oil_standards' => 'Monitor and maintain oil quality standards',
            'day_labels' => 'Ensure proper day labeling procedures',
            'equipment_working' => 'Repair or maintain equipment to working condition',
            'fryer_condition' => 'Clean and maintain fryer to proper standards',
            'vegetable_prep' => 'Review vegetable preparation procedures',
            'employee_appearance' => 'Ensure employee appearance meets standards',
            'equipment_wrapped' => 'Properly wrap and store equipment',
            'sink_setup' => 'Set up sinks according to health standards',
            'sanitizer_standard' => 'Maintain proper sanitizer concentration',
            'dining_area_clean' => 'Deep clean dining area to meet standards',
            'restroom_clean' => 'Clean restroom to meet hygiene standards'
        ];
        
        return $actions[$field] ?? 'Address compliance issue for ' . $this->getFieldLabel($field);
    }
    
    private function getRestaurantManager($restaurantName)
    {
        if (empty($restaurantName)) {
            return 'Restaurant Manager';
        }
        
        // Try to find the restaurant and get its manager
        $restaurant = \App\Models\Restaurant::where('name', $restaurantName)->first();
        
        if ($restaurant && $restaurant->manager) {
            return $restaurant->manager->name;
        }
        
        // If no specific manager found, return default
        return 'Restaurant Manager';
    }
}
