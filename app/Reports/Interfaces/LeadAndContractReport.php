<?php


namespace App\Reports\Interfaces;


interface LeadAndContractReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
