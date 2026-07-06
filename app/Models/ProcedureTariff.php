<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcedureTariff extends Model
{
    protected $fillable = [
        'icd9cm_code_id',
        'name',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function icd9cmCode(): BelongsTo
    {
        return $this->belongsTo(Icd9cmCode::class);
    }
}
