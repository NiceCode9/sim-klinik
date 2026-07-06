<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Registered = 'registered';
    case VitalCheck = 'vital_check';
    case WaitingDoctor = 'waiting_doctor';
    case InExamination = 'in_examination';
    case WaitingPharmacy = 'waiting_pharmacy';
    case WaitingPayment = 'waiting_payment';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
