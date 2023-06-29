<?php


namespace App\Reports\Interfaces;


interface LeadAndContractDateReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
    public function generateLeadAndContract($franchiseIds, $queryParams);
    public function generateLeadAndContractByFranchise($franchiseIds, $queryParams);
    public function generateDesignAdvisorById($franchiseId);
}
