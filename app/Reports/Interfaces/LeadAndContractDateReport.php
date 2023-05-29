<?php


namespace App\Reports\Interfaces;


interface LeadAndContractDateReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
    public function generateLeadAndContract($userType, $franchiseIds, $queryParams);
    public function generateLeadAndContractByFranchise($userType, $franchiseIds, $queryParams);
}
