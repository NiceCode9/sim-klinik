<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitVital extends Model
{
    protected $fillable = [
        'visit_id',
        'employee_id',
        'blood_pressure',
        'pulse',
        'temperature',
        'respiration_rate',
        'height_cm',
        'weight_kg',
        'chief_complaint',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
