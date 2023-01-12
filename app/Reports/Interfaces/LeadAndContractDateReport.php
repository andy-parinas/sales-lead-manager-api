<?php


namespace App\Reports\Interfaces;


interface LeadAndContractDateReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
