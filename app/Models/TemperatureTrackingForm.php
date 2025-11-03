<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureTrackingForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_visit_id',
        'store_id',
        'date',
        'shift',
        'mic_morning',
        'mic_evening',
        'cooked_products',
        'holding_products',
        'vegetables',
        'equipment',
        'sanitizer',
        'receiving_products',
        'corrective_action',
        'shift_turnover',
        'side_cooked',
        'side_holding',
        'vegetable_receiving',
        'product_receiving_side',
        'corrective_action_upper',
        'corrective_action_lower',
        'created_by',
        'status',
    ];
    protected $casts = [
        'cooked_products' => 'array',
        'holding_products' => 'array',
        'side_cooked' => 'array',
        'side_holding' => 'array',
        'vegetables' => 'array',
        'vegetable_receiving' => 'array',
        'equipment' => 'array',
        'sanitizer' => 'array',
        'receiving_products' => 'array',
        'product_receiving_side' => 'array',
        'shift_turnover' => 'array',
        'date' => 'date',
    ];
    public function storeVisit() {
        return $this->belongsTo(StoreVisit::class, 'store_visit_id');
    }
    
    public function store() {
        return $this->belongsTo(Restaurant::class, 'store_id');
    }
    
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}