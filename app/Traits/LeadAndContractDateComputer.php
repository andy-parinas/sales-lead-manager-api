<?php

namespace App\Traits;

trait LeadAndContractDateComputer
{
    protected function computeTotal($results)
    {
        $totalNumberOfLeads = 0;
        $totalNumberOfContracts = 0;
        $grandTotalContracts = 0;
        $totalConversionRate = 0;
        $grandTotalAveragePrice = 0;
        
        foreach ($results as $result){
            $totalNumberOfLeads = $totalNumberOfLeads + $result['totalLeads'];
            $totalNumberOfContracts = $totalNumberOfContracts + $result['totalContracts'];
            $grandTotalContracts = $grandTotalContracts + $result['sumOfTotalContracts'];
            $totalConversionRate = $totalConversionRate + $result['conversionRate'];
            $grandTotalAveragePrice = $grandTotalAveragePrice + $result['averageSalesPrice'];
        }
        
        $resultLength = count($results);

        return [
            'totalNumberOfLeads' => $totalNumberOfLeads,
            'totalNumberOfContracts' => $totalNumberOfContracts,
            'grandTotalContracts' => $grandTotalContracts,
            'averageConversionRate' => $totalNumberOfLeads == 0? 0: $totalNumberOfContracts / $totalNumberOfLeads,
            'grandAveragePrice' => $totalNumberOfContracts == 0? 0: $grandTotalContracts / $totalNumberOfContracts,
        ];
    }
}
