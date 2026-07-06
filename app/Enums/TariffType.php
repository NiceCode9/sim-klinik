<?php

namespace App\Enums;

enum TariffType: string
{
    case Tuslah = 'tuslah';
    case Embalase = 'embalase';
    case Procedure = 'procedure';
    case DoctorFee = 'doctor_fee';
    case Other = 'other';
}
