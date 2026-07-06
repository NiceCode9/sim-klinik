<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTestMaster extends Model
{
    protected $fillable = [
        'name',
        'category',
        'unit',
        'normal_range_min',
        'normal_range_max',
        'tariff_id',
    ];

    protected function casts(): array
    {
        return [
            'normal_range_min' => 'decimal:2',
            'normal_range_max' => 'decimal:2',
        ];
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }
}
