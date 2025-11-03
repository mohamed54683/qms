<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreVisitAnswer extends Model
{
    protected $fillable = [
        'visit_id',
        'section',
        'question',
        'field_name',
        'answer',
        'comment'
    ];

    protected $casts = [
        'answer' => 'boolean'
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(StoreVisit::class);
    }
}
