<?php

namespace App\Traits;

trait LeadAndContractDateComputer
{
    protected function computeTotal($results)
    {
        $totalNumberOfLeads = 0;
        $totalNumberOfContracts = 0;
        $grandTotalContracts = 0;
        
        foreach ($results as $result){
            $totalNumberOfLeads = $totalNumberOfLeads + $result['totalLeads'];
            $totalNumberOfContracts = $totalNumberOfContracts + $result['totalContracts'];
            $grandTotalContracts = $grandTotalContracts + $result['sumOfTotalContracts'];
        }
        
        $resultLength = count($results);

        return [
            'totalNumberOfLeads' => $totalNumberOfLeads,
            'totalNumberOfContracts' => $totalNumberOfContracts,
            'grandTotalContracts' => $grandTotalContracts,
        ];
    }
}
