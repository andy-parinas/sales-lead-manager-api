<?php

namespace App\Reports\Interfaces;

interface RoofSheetProfileReport
{
    public function generate($queryParams);
    public function generateByFranchise($franchiseIds, $queryParams);
}
