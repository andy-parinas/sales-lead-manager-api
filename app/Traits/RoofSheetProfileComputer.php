<?php


namespace App\Traits;


trait RoofSheetProfileComputer
{
    protected function computeTotal($results)
    {
        $totalNumberOfSales = 0;
        $grandTotalContractPrice = 0;
        
        foreach ($results as $result){
            $totalNumberOfSales = $totalNumberOfSales + $result->numberOfSales;
            $grandTotalContractPrice = $grandTotalContractPrice + $result->valueOfSales;
        }
        
        $resultLength = count($results);

        return [
            'totalNumberOfSales' => $totalNumberOfSales,
            'grandTotalContractPrice' => $grandTotalContractPrice,
        ];
    }
}
