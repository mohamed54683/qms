<?php

namespace App\Exports;

use App\Models\ActionPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ActionPlansExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $actionPlans;

    public function __construct($actionPlans)
    {
        $this->actionPlans = $actionPlans;
    }

    public function collection()
    {
        return $this->actionPlans;
    }

    public function headings(): array
    {
        return [
            'BASIC INFORMATION',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '5W1H ANALYSIS',
            '',
            '',
            '',
            '',
            '',
            'ACTION REQUIRED',
            'ADDITIONAL DETAILS',
            '',
        ];
    }

    public function map($actionPlan): array
    {
        // Format priority with emoji
        $priority = match($actionPlan->priority) {
            'High' => '🔴 High Priority',
            'Medium' => '🟡 Medium Priority',
            'Low' => '🟢 Low Priority',
            default => $actionPlan->priority ?? 'N/A'
        };

        // Format status with emoji
        $status = match($actionPlan->status) {
            'Pending' => '⏳ Pending',
            'In Progress' => '🔄 In Progress',
            'Completed' => '✅ Completed',
            'Cancelled' => '❌ Cancelled',
            default => $actionPlan->status ?? 'N/A'
        };

        // Format due date
        $dueDate = $actionPlan->due_date 
            ? $actionPlan->due_date->format('m/d/Y') 
            : 'mm/dd/yyyy';

        // Format assigned user
        $assignedTo = $actionPlan->assignedUser 
            ? '👤 ' . $actionPlan->assignedUser->name 
            : '👤 Unassigned';

        // Get restaurant name
        $restaurant = $actionPlan->storeVisit 
            ? $actionPlan->storeVisit->restaurant_name 
            : ($actionPlan->qscChecklist ? $actionPlan->qscChecklist->store_name : 'N/A');

        // Format visit date
        $visitDate = $actionPlan->storeVisit 
            ? $actionPlan->storeVisit->visit_date 
            : ($actionPlan->qscChecklist ? $actionPlan->qscChecklist->visit_date : 'N/A');

        return [
            // Basic Information (Columns A-H)
            'Priority: ' . $priority,
            'Status: ' . $status,
            'Due Date: ' . $dueDate,
            'Assign To: ' . $assignedTo,
            'Restaurant: ' . $restaurant,
            'Visit Date: ' . $visitDate,
            'Item: ' . ($actionPlan->item ?? 'N/A'),
            'Issue: ' . ($actionPlan->issue ?? 'N/A'),
            
            // 5W1H Analysis (Columns I-N)
            'WHAT: ' . ($actionPlan->what ?? 'N/A'),
            'WHERE: ' . ($actionPlan->where ?? $restaurant),
            'WHY: ' . ($actionPlan->why ?? 'N/A'),
            'HOW: ' . ($actionPlan->how ?? 'N/A'),
            'WHO: ' . ($actionPlan->who ?? 'N/A'),
            'WHEN: ' . ($actionPlan->when_detail ?? $visitDate),
            
            // Action Required (Column O)
            $actionPlan->action_required ?? 'N/A',
            
            // Additional Details (Columns P-Q)
            'COMMENT: ' . ($actionPlan->comment ?? 'N/A'),
            'NOTES: ' . ($actionPlan->notes ?? 'N/A'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style all data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:Q' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);

        // Set row height for better readability
        $sheet->getDefaultRowDimension()->setRowHeight(30);
        $sheet->getRowDimension(1)->setRowHeight(40);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Priority
            'B' => 18, // Status
            'C' => 15, // Due Date
            'D' => 20, // Assign To
            'E' => 25, // Restaurant
            'F' => 15, // Visit Date
            'G' => 30, // Item
            'H' => 35, // Issue
            'I' => 35, // WHAT
            'J' => 25, // WHERE
            'K' => 35, // WHY
            'L' => 35, // HOW
            'M' => 25, // WHO
            'N' => 20, // WHEN
            'O' => 35, // Action Required
            'P' => 35, // Comment
            'Q' => 25, // Notes
        ];
    }
}
