<?php

namespace App\Repositories\Interfaces;

use App\Franchise;

interface LeadTransferRepositoryInterface
{
    public function getAllLeads(Array $params);
    public function getAllLeadsByFranchiseId($franchiseId, Array $params);
    public function findLeadsByUsersFranchise(Array $franchiseIds, Array $params);
    public function getTotalFranchiseInLeads($franchiseIds);
    public function getFranchiseInLeads();
}
