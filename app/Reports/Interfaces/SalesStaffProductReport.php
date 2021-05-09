<?php


namespace App\Reports\Interfaces;


interface SalesStaffProductReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
