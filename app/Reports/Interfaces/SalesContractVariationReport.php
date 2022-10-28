<?php


namespace App\Reports\Interfaces;


interface SalesContractVariationReport
{
    public function generate($queryParams);

    public function generateByFranchise($franchiseIds, $queryParams);
}
