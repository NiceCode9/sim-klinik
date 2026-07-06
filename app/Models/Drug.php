<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Drug extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'is_fractional',
        'pricing_type',
        'price_value',
        'minimum_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_fractional' => 'boolean',
            'is_active' => 'boolean',
            'price_value' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
