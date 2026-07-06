<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    protected $fillable = [
        'visit_id',
        'queue_number',
        'specialization_id',
        'status',
        'source',
        'called_at',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
