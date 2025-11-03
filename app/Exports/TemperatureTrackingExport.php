<?php

namespace App\Exports;

use App\Models\TemperatureTrackingForm;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class TemperatureTrackingExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = TemperatureTrackingForm::with(['store', 'creator']);

        // If not admin, show only user's own forms or accessible restaurants
        if (!$this->isAdmin) {
            $user = Auth::user();
            $accessibleRestaurantIds = $user->restaurants()->pluck('restaurants.id')->toArray();
            
            $query->where(function($q) use ($accessibleRestaurantIds, $user) {
                $q->where('created_by', $user->id);
                if (count($accessibleRestaurantIds) > 0) {
                    $q->orWhereIn('store_id', $accessibleRestaurantIds);
                }
            });
        }

        // Apply filters
        if (!empty($this->filters['store_id'])) {
            $query->where('store_id', $this->filters['store_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('date', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['shift'])) {
            $query->where('shift', $this->filters['shift']);
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        $headers = [
            'ID',
            'Store Name',
            'Date',
            'Shift',
            'MIC Morning',
            'MIC Evening',
            'Status',
            'Created By',
            'Created At',
            'Updated At',
        ];
        
        // Cooked Products (6 items × 3 time slots = 18 columns)
        $cookedProducts = ['KIDS CHICKEN BURGER', 'REGULAR CHICKEN', 'SULTAN BEEF', 'DOUBLE G BEEF', 'CRISPY CHICKEN', 'GRILLED CHICKEN'];
        $timeSlots = ['12PM', '5PM', '8PM'];
        foreach ($cookedProducts as $product) {
            foreach ($timeSlots as $time) {
                $headers[] = "Cooked - {$product} @ {$time}";
            }
        }
        
        // Holding Products (6 items × 3 time slots = 18 columns)
        foreach ($cookedProducts as $product) {
            foreach ($timeSlots as $time) {
                $headers[] = "Holding - {$product} @ {$time}";
            }
        }
        
        // Side Cooked (3 items × 3 time slots = 9 columns)
        $sideProducts = ['JULIENNE', 'MAC & CHEESE', 'NACHOS'];
        foreach ($sideProducts as $product) {
            foreach ($timeSlots as $time) {
                $headers[] = "Side Cooked - {$product} @ {$time}";
            }
        }
        
        // Side Holding (3 items × 3 time slots = 9 columns)
        foreach ($sideProducts as $product) {
            foreach ($timeSlots as $time) {
                $headers[] = "Side Holding - {$product} @ {$time}";
            }
        }
        
        // Vegetables (4 items × 3 time slots = 12 columns)
        $vegetables = ['LETTUCE', 'TOMATO', 'PICKLES', 'ONION'];
        foreach ($vegetables as $veg) {
            foreach ($timeSlots as $time) {
                $headers[] = "Vegetable - {$veg} @ {$time}";
            }
        }
        
        // Sanitizer (3 time slots)
        foreach ($timeSlots as $time) {
            $headers[] = "Sanitizer @ {$time}";
        }
        
        // Equipment (4 items × 2 temps = 8 columns)
        $equipment = [
            'REACH IN CHILLER (THAW.)',
            'REACH IN CHILLER (VEG)',
            'WALK IN FREEZER (FROZEN)',
            'WALK IN FREEZER (REACH IN)'
        ];
        foreach ($equipment as $equip) {
            $headers[] = "{$equip} - Chill";
            $headers[] = "{$equip} - Freeze";
        }
        
        // Vegetable Receiving (4 items × 2 fields = 8 columns)
        foreach ($vegetables as $veg) {
            $headers[] = "Veg Receiving - {$veg} - Qty";
            $headers[] = "Veg Receiving - {$veg} - Date";
        }
        
        // Receiving Products (7 items × 4 fields = 28 columns)
        $receivingProducts = ['KIDS CHICKEN BURGER', 'REGULAR CHICKEN', 'SULTAN BEEF', 'DOUBLE G BEEF', 'CRISPY CHICKEN', 'GRILLED CHICKEN', 'NACHOS'];
        foreach ($receivingProducts as $product) {
            $headers[] = "Receiving - {$product} - Received";
            $headers[] = "Receiving - {$product} - Production";
            $headers[] = "Receiving - {$product} - Expiry";
            $headers[] = "Receiving - {$product} - Y/N";
        }
        
        // Product Receiving Side (3 items × 4 fields = 12 columns)
        foreach ($sideProducts as $product) {
            $headers[] = "Side Receiving - {$product} - Received";
            $headers[] = "Side Receiving - {$product} - Production";
            $headers[] = "Side Receiving - {$product} - Expiry";
            $headers[] = "Side Receiving - {$product} - Y/N";
        }
        
        // Shift Turnover (5 items × 2 shifts = 10 columns)
        $shiftItems = ['Frozen', 'Packaging', 'Bread', 'Maintenance', 'Signature'];
        foreach ($shiftItems as $item) {
            $headers[] = "Shift Turnover - {$item} - Morning";
            $headers[] = "Shift Turnover - {$item} - Evening";
        }
        
        // Corrective Actions
        $headers[] = 'Corrective Action';
        $headers[] = 'Corrective Action Upper';
        $headers[] = 'Corrective Action Lower';
        
        return $headers;
    }

    /**
     * Map each row to the columns
     */
    public function map($form): array
    {
        $row = [
            $form->id,
            $form->store ? $form->store->name : 'N/A',
            $form->date ? $form->date->format('Y-m-d') : 'N/A',
            ucfirst($form->shift ?? 'N/A'),
            $form->mic_morning ?? 'N/A',
            $form->mic_evening ?? 'N/A',
            $form->status ?? 'N/A',
            $form->creator ? $form->creator->name : 'N/A',
            $form->created_at->format('Y-m-d H:i:s'),
            $form->updated_at->format('Y-m-d H:i:s'),
        ];

        // Cooked Products (6 items × 3 time slots = 18 values)
        $cookedProducts = ['KIDS CHICKEN BURGER', 'REGULAR CHICKEN', 'SULTAN BEEF', 'DOUBLE G BEEF', 'CRISPY CHICKEN', 'GRILLED CHICKEN'];
        $timeSlots = ['12PM', '5PM', '8PM'];
        $cookedData = is_array($form->cooked_products) ? $form->cooked_products : [];
        foreach ($cookedProducts as $product) {
            foreach ($timeSlots as $time) {
                $row[] = $cookedData[$product][$time] ?? '';
            }
        }
        
        // Holding Products (6 items × 3 time slots = 18 values)
        $holdingData = is_array($form->holding_products) ? $form->holding_products : [];
        foreach ($cookedProducts as $product) {
            foreach ($timeSlots as $time) {
                $row[] = $holdingData[$product][$time] ?? '';
            }
        }
        
        // Side Cooked (3 items × 3 time slots = 9 values)
        $sideProducts = ['JULIENNE', 'MAC & CHEESE', 'NACHOS'];
        $sideCookedData = is_array($form->side_cooked) ? $form->side_cooked : [];
        foreach ($sideProducts as $product) {
            foreach ($timeSlots as $time) {
                $row[] = $sideCookedData[$product][$time] ?? '';
            }
        }
        
        // Side Holding (3 items × 3 time slots = 9 values)
        $sideHoldingData = is_array($form->side_holding) ? $form->side_holding : [];
        foreach ($sideProducts as $product) {
            foreach ($timeSlots as $time) {
                $row[] = $sideHoldingData[$product][$time] ?? '';
            }
        }
        
        // Vegetables (4 items × 3 time slots = 12 values)
        $vegetables = ['LETTUCE', 'TOMATO', 'PICKLES', 'ONION'];
        $vegetablesData = is_array($form->vegetables) ? $form->vegetables : [];
        foreach ($vegetables as $veg) {
            foreach ($timeSlots as $time) {
                $row[] = $vegetablesData[$veg][$time] ?? '';
            }
        }
        
        // Sanitizer (3 time slots)
        $sanitizerData = is_array($form->sanitizer) ? $form->sanitizer : [];
        foreach ($timeSlots as $time) {
            $row[] = $sanitizerData[$time] ?? '';
        }
        
        // Equipment (4 items × 2 temps = 8 values)
        $equipmentKeys = ['reach_in_thaw', 'reach_in_veg', 'walk_in_frozen', 'walk_in_reach'];
        $equipmentData = is_array($form->equipment) ? $form->equipment : [];
        foreach ($equipmentKeys as $key) {
            $row[] = $equipmentData[$key]['chill'] ?? '';
            $row[] = $equipmentData[$key]['freeze'] ?? '';
        }
        
        // Vegetable Receiving (4 items × 2 fields = 8 values)
        $vegReceivingData = is_array($form->vegetable_receiving) ? $form->vegetable_receiving : [];
        foreach ($vegetables as $veg) {
            $row[] = $vegReceivingData[$veg]['qty'] ?? '';
            $row[] = $vegReceivingData[$veg]['date'] ?? '';
        }
        
        // Receiving Products (7 items × 4 fields = 28 values)
        $receivingProducts = ['KIDS CHICKEN BURGER', 'REGULAR CHICKEN', 'SULTAN BEEF', 'DOUBLE G BEEF', 'CRISPY CHICKEN', 'GRILLED CHICKEN', 'NACHOS'];
        $receivingData = is_array($form->receiving_products) ? $form->receiving_products : [];
        foreach ($receivingProducts as $product) {
            $row[] = $receivingData[$product]['received'] ?? '';
            $row[] = $receivingData[$product]['production'] ?? '';
            $row[] = $receivingData[$product]['expiry'] ?? '';
            $row[] = $receivingData[$product]['yn'] ?? '';
        }
        
        // Product Receiving Side (3 items × 4 fields = 12 values)
        $receivingSideData = is_array($form->product_receiving_side) ? $form->product_receiving_side : [];
        foreach ($sideProducts as $product) {
            $row[] = $receivingSideData[$product]['received'] ?? '';
            $row[] = $receivingSideData[$product]['production'] ?? '';
            $row[] = $receivingSideData[$product]['expiry'] ?? '';
            $row[] = $receivingSideData[$product]['yn'] ?? '';
        }
        
        // Shift Turnover (5 items × 2 shifts = 10 values)
        $shiftKeys = ['frozen', 'packaging', 'bread', 'maintenance', 'signature'];
        $shiftData = is_array($form->shift_turnover) ? $form->shift_turnover : [];
        $morningData = $shiftData['morning'] ?? [];
        $eveningData = $shiftData['evening'] ?? [];
        foreach ($shiftKeys as $key) {
            $row[] = $morningData[$key] ?? '';
            $row[] = $eveningData[$key] ?? '';
        }
        
        // Corrective Actions
        $row[] = $form->corrective_action ?? '';
        $row[] = $form->corrective_action_upper ?? '';
        $row[] = $form->corrective_action_lower ?? '';

        return $row;
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
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '2563EB',
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
