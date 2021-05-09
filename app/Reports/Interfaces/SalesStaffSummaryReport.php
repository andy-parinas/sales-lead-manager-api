<?php


namespace App\Reports\Interfaces;


interface SalesStaffSummaryReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
