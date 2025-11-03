<?php

namespace App\Exports;

use App\Models\QscChecklist;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class QscChecklistExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    protected $isAdmin;

    public function __construct($filters = [], $isAdmin = false)
    {
        $this->filters = $filters;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Query builder for export
     */
    public function query()
    {
        $query = QscChecklist::with(['user']);

        // If not admin, show only user's own checklists or accessible restaurants
        if (!$this->isAdmin) {
            $user = Auth::user();
            $accessibleRestaurantIds = $user->restaurants()->pluck('restaurants.id')->toArray();
            
            $query->where(function($q) use ($accessibleRestaurantIds, $user) {
                $q->where('user_id', $user->id);
                if (count($accessibleRestaurantIds) > 0) {
                    $q->orWhereIn('restaurant_id', $accessibleRestaurantIds);
                }
            });
        }

        // Apply filters
        if (!empty($this->filters['restaurant'])) {
            $query->where('store_name', 'LIKE', '%' . $this->filters['restaurant'] . '%');
        }

        if (!empty($this->filters['time_option'])) {
            $query->where('time_option', $this->filters['time_option']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('store_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('mod', 'LIKE', '%' . $search . '%')
                  ->orWhere('manager_signature', 'LIKE', '%' . $search . '%');
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Store Name',
            'Date',
            'MOD',
            'Day',
            'Time Option',
            'Status',
            'Manager Signature',
            'Created By',
            'Created At',
            'Updated At',
            
            // Exterior Section
            'Exterior - Lights Open',
            'Exterior - Lights Open Comment',
            'Exterior - Logo Clean',
            'Exterior - Logo Clean Comment',
            'Exterior - Parking Clean',
            'Exterior - Parking Clean Comment',
            'Exterior - No Graffiti',
            'Exterior - No Graffiti Comment',
            
            // Doors and Glass Section
            'Doors/Glass - Glass Clean',
            'Doors/Glass - Glass Clean Comment',
            'Doors/Glass - Door Handle',
            'Doors/Glass - Door Handle Comment',
            'Doors/Glass - Entrance Clean',
            'Doors/Glass - Entrance Clean Comment',
            
            // Frontline Section
            'Frontline - Areas Organized',
            'Frontline - Areas Organized Comment',
            'Frontline - Customers Greeted',
            'Frontline - Customers Greeted Comment',
            'Frontline - Menu Available',
            'Frontline - Menu Available Comment',
            'Frontline - Seven Steps',
            'Frontline - Seven Steps Comment',
            'Frontline - Tables Clean',
            'Frontline - Tables Clean Comment',
            'Frontline - High Chairs',
            'Frontline - High Chairs Comment',
            'Frontline - No Damaged Tables',
            'Frontline - No Damaged Tables Comment',
            
            // Restroom Section
            'Restroom - No Full Trash',
            'Restroom - No Full Trash Comment',
            'Restroom - Soap Available',
            'Restroom - Soap Available Comment',
            'Restroom - Hand Dryer',
            'Restroom - Hand Dryer Comment',
            
            // Holding/Heating Section
            'Holding - Product Temp',
            'Holding - Product Temp Comment',
            'Holding - Product Age',
            'Holding - Product Age Comment',
            'Holding - Check Product',
            'Holding - Check Product Comment',
            'Holding - Products Fresh',
            'Holding - Products Fresh Comment',
            'Holding - Internal Temp',
            'Holding - Internal Temp Comment',
            'Holding - Shortening Level',
            'Holding - Shortening Level Comment',
            'Holding - Check Shortening',
            'Holding - Check Shortening Comment',
            'Holding - Fryer Maintenance',
            'Holding - Fryer Maintenance Comment',
            'Holding - Use Tray',
            'Holding - Use Tray Comment',
            'Holding - Fry Basket',
            'Holding - Fry Basket Comment',
            'Holding - Fries Salted',
            'Holding - Fries Salted Comment',
            'Holding - Fries Cooking',
            'Holding - Fries Cooking Comment',
            'Holding - Buns Quality',
            'Holding - Buns Quality Comment',
            'Holding - Teflon Sheet',
            'Holding - Teflon Sheet Comment',
            'Holding - Bread Standard',
            'Holding - Bread Standard Comment',
            
            // Thawing Section
            'Thawing - Day Labels',
            'Thawing - Day Labels Comment',
            'Thawing - No Tampering',
            'Thawing - No Tampering Comment',
            'Thawing - Temp Range',
            'Thawing - Temp Range Comment',
            'Thawing - No Overstock',
            'Thawing - No Overstock Comment',
            'Thawing - Utensils Clean',
            'Thawing - Utensils Clean Comment',
            'Thawing - Sink Setup',
            'Thawing - Sink Setup Comment',
            'Thawing - Portion Standards',
            'Thawing - Portion Standards Comment',
            'Thawing - Sultan Sauce',
            'Thawing - Sultan Sauce Comment',
            'Thawing - Vegetables Date',
            'Thawing - Vegetables Date Comment',
            'Thawing - Follow FIFO',
            'Thawing - Follow FIFO Comment',
        ];
    }

    /**
     * Map each row to the columns
     */
    public function map($checklist): array
    {
        return [
            $checklist->id,
            $checklist->store_name,
            $checklist->date,
            $checklist->mod,
            $checklist->day,
            $checklist->time_option,
            $checklist->status ?? 'N/A',
            $checklist->manager_signature,
            $checklist->user ? $checklist->user->name : 'N/A',
            $checklist->created_at->format('Y-m-d H:i:s'),
            $checklist->updated_at->format('Y-m-d H:i:s'),
            
            // Exterior Section
            $this->formatValue($checklist->exterior_lights_open),
            $checklist->exterior_lights_open_comment,
            $this->formatValue($checklist->exterior_logo_clean),
            $checklist->exterior_logo_clean_comment,
            $this->formatValue($checklist->exterior_parking_clean),
            $checklist->exterior_parking_clean_comment,
            $this->formatValue($checklist->exterior_no_graffiti),
            $checklist->exterior_no_graffiti_comment,
            
            // Doors and Glass Section
            $this->formatValue($checklist->doors_glass_clean),
            $checklist->doors_glass_clean_comment,
            $this->formatValue($checklist->doors_door_handle),
            $checklist->doors_door_handle_comment,
            $this->formatValue($checklist->doors_entrance_clean),
            $checklist->doors_entrance_clean_comment,
            
            // Frontline Section
            $this->formatValue($checklist->frontline_areas_organized),
            $checklist->frontline_areas_organized_comment,
            $this->formatValue($checklist->frontline_customers_greeted),
            $checklist->frontline_customers_greeted_comment,
            $this->formatValue($checklist->frontline_menu_available),
            $checklist->frontline_menu_available_comment,
            $this->formatValue($checklist->frontline_seven_steps),
            $checklist->frontline_seven_steps_comment,
            $this->formatValue($checklist->frontline_tables_clean),
            $checklist->frontline_tables_clean_comment,
            $this->formatValue($checklist->frontline_high_chairs),
            $checklist->frontline_high_chairs_comment,
            $this->formatValue($checklist->frontline_no_damaged_tables),
            $checklist->frontline_no_damaged_tables_comment,
            
            // Restroom Section
            $this->formatValue($checklist->restroom_no_full_trash),
            $checklist->restroom_no_full_trash_comment,
            $this->formatValue($checklist->restroom_soap_available),
            $checklist->restroom_soap_available_comment,
            $this->formatValue($checklist->restroom_hand_dryer),
            $checklist->restroom_hand_dryer_comment,
            
            // Holding/Heating Section
            $this->formatValue($checklist->holding_product_temp),
            $checklist->holding_product_temp_comment,
            $this->formatValue($checklist->holding_product_age),
            $checklist->holding_product_age_comment,
            $this->formatValue($checklist->holding_check_product),
            $checklist->holding_check_product_comment,
            $this->formatValue($checklist->holding_products_fresh),
            $checklist->holding_products_fresh_comment,
            $this->formatValue($checklist->holding_internal_temp),
            $checklist->holding_internal_temp_comment,
            $this->formatValue($checklist->holding_shortening_level),
            $checklist->holding_shortening_level_comment,
            $this->formatValue($checklist->holding_check_shortening),
            $checklist->holding_check_shortening_comment,
            $this->formatValue($checklist->holding_fryer_maintenance),
            $checklist->holding_fryer_maintenance_comment,
            $this->formatValue($checklist->holding_use_tray),
            $checklist->holding_use_tray_comment,
            $this->formatValue($checklist->holding_fry_basket),
            $checklist->holding_fry_basket_comment,
            $this->formatValue($checklist->holding_fries_salted),
            $checklist->holding_fries_salted_comment,
            $this->formatValue($checklist->holding_fries_cooking),
            $checklist->holding_fries_cooking_comment,
            $this->formatValue($checklist->holding_buns_quality),
            $checklist->holding_buns_quality_comment,
            $this->formatValue($checklist->holding_teflon_sheet),
            $checklist->holding_teflon_sheet_comment,
            $this->formatValue($checklist->holding_bread_standard),
            $checklist->holding_bread_standard_comment,
            
            // Thawing Section
            $this->formatValue($checklist->thawing_day_labels),
            $checklist->thawing_day_labels_comment,
            $this->formatValue($checklist->thawing_no_tampering),
            $checklist->thawing_no_tampering_comment,
            $this->formatValue($checklist->thawing_temp_range),
            $checklist->thawing_temp_range_comment,
            $this->formatValue($checklist->thawing_no_overstock),
            $checklist->thawing_no_overstock_comment,
            $this->formatValue($checklist->thawing_utensils_clean),
            $checklist->thawing_utensils_clean_comment,
            $this->formatValue($checklist->thawing_sink_setup),
            $checklist->thawing_sink_setup_comment,
            $this->formatValue($checklist->thawing_portion_standards),
            $checklist->thawing_portion_standards_comment,
            $this->formatValue($checklist->thawing_sultan_sauce),
            $checklist->thawing_sultan_sauce_comment,
            $this->formatValue($checklist->thawing_vegetables_date),
            $checklist->thawing_vegetables_date_comment,
            $this->formatValue($checklist->thawing_follow_fifo),
            $checklist->thawing_follow_fifo_comment,
        ];
    }

    /**
     * Format values for display
     */
    private function formatValue($value)
    {
        if (is_null($value)) {
            return 'N/A';
        }
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        if ($value === 1 || $value === '1') {
            return 'Yes';
        }
        if ($value === 0 || $value === '0') {
            return 'No';
        }
        return $value;
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '4A5568',
                    ],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }
}
