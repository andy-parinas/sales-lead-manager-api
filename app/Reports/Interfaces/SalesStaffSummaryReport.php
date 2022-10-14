<?php


namespace App\Reports\Interfaces;


interface SalesStaffSummaryReport
{
    public function generate($franchiseIds, $queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
