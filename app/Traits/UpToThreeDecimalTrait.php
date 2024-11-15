<?php

namespace App\Traits;

trait UpToThreeDecimalTrait
{
    public function takeUptoThreeDecimal($number): float
    {
        return round((float) $number, 3);
    }

}
