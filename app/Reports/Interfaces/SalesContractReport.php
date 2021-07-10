<?php


namespace App\Reports\Interfaces;


interface SalesContractReport
{
    public function generate($queryParams);

    public function generateByFranchise($franchiseIds, $queryParams);
}
