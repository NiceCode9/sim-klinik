<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitBill extends Model
{
    protected $fillable = [
        'visit_id',
        'status',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
