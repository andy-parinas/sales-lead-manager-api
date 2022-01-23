<?php

namespace App\Services;

use App\Services\Interfaces\LeadServiceInterface;

class LeadService implements LeadServiceInterface
{

    public function generateLeadNumber()
    {
        $number = strtoupper(uniqid());

        return "LN-{$number}";
    }

}