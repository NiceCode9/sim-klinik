<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Patient extends Model
{
    protected $fillable = [
        'medical_record_number',
        'nik',
        'name',
        'gender',
        'birth_date',
        'phone',
        'address',
        'blood_type',
        'allergies',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
