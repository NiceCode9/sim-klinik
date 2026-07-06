<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    protected $fillable = [
        'patient_id',
        'specialization_id',
        'employee_id',
        'visit_date',
        'status',
        'registration_channel',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function queue(): HasOne
    {
        return $this->hasOne(Queue::class);
    }

    public function vitalSigns(): HasOne
    {
        return $this->hasOne(VisitVital::class);
    }

    public function bill(): HasOne
    {
        return $this->hasOne(VisitBill::class);
    }
}
