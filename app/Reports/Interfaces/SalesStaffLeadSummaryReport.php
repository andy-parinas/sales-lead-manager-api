<?php


namespace App\Reports\Interfaces;


interface SalesStaffLeadSummaryReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
