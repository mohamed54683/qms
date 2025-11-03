<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionPlanImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_plan_id',
        'image_path',
        'field_name',
        'original_name',
        'file_size',
        'mime_type',
    ];

    /**
     * Get the action plan that owns the image.
     */
    public function actionPlan()
    {
        return $this->belongsTo(ActionPlan::class);
    }

    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
