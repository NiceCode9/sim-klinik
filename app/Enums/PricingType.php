<?php

namespace App\Enums;

enum PricingType: string
{
    case MarginPercentage = 'margin_percentage';
    case Flat = 'flat';
}
