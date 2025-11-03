<?php

namespace App\Exports;

use App\Models\StoreVisit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class StoreVisitsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $visits;

    public function __construct(Collection $visits)
    {
        $this->visits = $visits;
    }

    public function collection()
    {
        return $this->visits;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Restaurant',
            'MIC Shift',
            'Visit Date',
            'Score (%)',
            'Status',
            'Purpose of Visit',
            'Action Items Count',
            'OCA Board Followed',
            'Staff Know Duty',
            'Coaching & Directing',
            'Smile & Greetings',
            'Suggestive Selling',
            'Offer Promotion',
            'Thank & Direction',
            'Team Work & Hustle',
            'Order Accuracy',
            'Service Time',
            'Dine In',
            'Take Out',
            'Family',
            'Delivery',
            'Drive Thru',
            'Weekly Schedule',
            'MOD Financial Goal',
            'Sales Objectives',
            'Cash Policies',
            'Daily Waste',
            'TTF Followed',
            'Sandwich Assembly',
            'QSC Completed',
            'Oil Standards',
            'Day Labels',
            'Equipment Working',
            'Fryer Condition',
            'Vegetable Prep',
            'Employee Appearance',
            'Equipment Wrapped',
            'Sink Setup',
            'Sanitizer Standard',
            'Dining Area Clean',
            'Restroom Clean',
            'Last Visit Summary',
            'Updates on Previous Issues',
            'Other Follow-up',
            'What Did You See',
            'Why Had Issue',
            'How to Improve',
            'Who & When Responsible',
            'General Comments',
            'Created By',
            'Created At'
        ];
    }

    public function map($visit): array
    {
        return [
            $visit->id,
            $visit->restaurant_name,
            $visit->mic,
            $visit->visit_date->format('Y-m-d'),
            $visit->score ?? $visit->calculateScore(),
            $visit->status,
            is_array($visit->purpose_of_visit) ? implode(', ', $visit->purpose_of_visit) : $visit->purpose_of_visit,
            count($visit->getActionItems()),
            $this->formatBoolean($visit->oca_board_followed),
            $this->formatBoolean($visit->staff_know_duty),
            $this->formatBoolean($visit->coaching_directing),
            $this->formatBoolean($visit->smile_greetings),
            $this->formatBoolean($visit->suggestive_selling),
            $this->formatBoolean($visit->offer_promotion),
            $this->formatBoolean($visit->thank_direction),
            $this->formatBoolean($visit->team_work_hustle),
            $this->formatBoolean($visit->order_accuracy),
            $this->formatBoolean($visit->service_time),
            $this->formatBoolean($visit->dine_in),
            $this->formatBoolean($visit->take_out),
            $this->formatBoolean($visit->family),
            $this->formatBoolean($visit->delivery),
            $this->formatBoolean($visit->drive_thru),
            $this->formatBoolean($visit->weekly_schedule),
            $this->formatBoolean($visit->mod_financial_goal),
            $this->formatBoolean($visit->sales_objectives),
            $this->formatBoolean($visit->cash_policies),
            $this->formatBoolean($visit->daily_waste),
            $this->formatBoolean($visit->ttf_followed),
            $this->formatBoolean($visit->sandwich_assembly),
            $this->formatBoolean($visit->qsc_completed),
            $this->formatBoolean($visit->oil_standards),
            $this->formatBoolean($visit->day_labels),
            $this->formatBoolean($visit->equipment_working),
            $this->formatBoolean($visit->fryer_condition),
            $this->formatBoolean($visit->vegetable_prep),
            $this->formatBoolean($visit->employee_appearance),
            $this->formatBoolean($visit->equipment_wrapped),
            $this->formatBoolean($visit->sink_setup),
            $this->formatBoolean($visit->sanitizer_standard),
            $this->formatBoolean($visit->dining_area_clean),
            $this->formatBoolean($visit->restroom_clean),
            $visit->last_visit_summary,
            $visit->last_visit_update,
            $visit->other_follow_up,
            $visit->what_did_you_see,
            $visit->why_had_issue,
            $visit->how_to_improve,
            $visit->who_when_responsible,
            $visit->general_comments,
            $visit->user->name ?? 'N/A',
            $visit->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    private function formatBoolean($value): string
    {
        if ($value === true) return 'Yes';
        if ($value === false) return 'No';
        return 'Not Answered';
    }
}