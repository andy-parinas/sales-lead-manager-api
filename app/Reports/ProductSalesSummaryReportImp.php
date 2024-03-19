<?php


namespace App\Reports;


use Illuminate\Support\Facades\DB;

class ProductSalesSummaryReportImp implements Interfaces\ProductSalesSummaryReport
{

    public function generate($queryParams)
    {
        $mainQuery= DB::table('leads')
            ->select('products.name')
            ->selectRaw("count(case appointments.outcome when 'success' then 1 else null end) as SuccessCount")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("(count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as conversionRate")
            ->selectRaw("avg(contracts.total_contract) as averageSalesPrice")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->join("job_types", "job_types.lead_id", "=", "leads.id")
            ->join("products", "job_types.product_id", "=", "products.id")
            ->join("appointments", "appointments.lead_id", "=", "leads.id")
            ->leftJoin("contracts", "contracts.lead_id", "=", "leads.id");


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }


        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

            $mainQuery = $mainQuery->where('products.id',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'products.name',
        ]);

        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){

            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }

        return $mainQuery->get();
    }

    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $mainQuery= DB::table('leads')
            ->select('products.name')
            ->selectRaw("count(case appointments.outcome when 'success' then 1 else null end) as SuccessCount")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("(count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as conversionRate")
            ->selectRaw("avg(contracts.total_contract) as averageSalesPrice")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->join("job_types", "job_types.lead_id", "=", "leads.id")
            ->join("products", "job_types.product_id", "=", "products.id")
            ->join("appointments", "appointments.lead_id", "=", "leads.id")
            ->join("franchises", "leads.franchise_id", "=", "franchises.id")
            ->leftJoin("contracts", "contracts.lead_id", "=", "leads.id")
            ->whereIn('franchises.id', $franchiseIds);


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }


        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

            $mainQuery = $mainQuery->where('products.id',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'products.name',
        ]);

        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){

            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }

        return $mainQuery->get();
    }
}
