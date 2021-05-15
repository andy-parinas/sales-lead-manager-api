<?php


namespace App\Reports\Interfaces;


interface ProductSalesSummaryReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
