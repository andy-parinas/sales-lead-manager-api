<?php

namespace App\Reports;

use App\Reports\Interfaces\SalesStaffSummaryReport;
use App\SalesStaff;
use Illuminate\Support\Facades\DB;

class SalesStaffSummaryReportImp implements SalesStaffSummaryReport
{
    public function generate($franchiseIds, $queryParams)
    {   
        $mainQuery= DB::table('leads')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("avg(contracts.total_contract) as averageSalesPrice")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfSales")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) / count(leads.id) * 100 as conversionRate")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->join("job_types", "job_types.lead_id", "=", "leads.id")
            ->join("appointments", "appointments.lead_id", "=", "leads.id")
            ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
            ->join("franchises", "leads.franchise_id", "=", "franchises.id")
            ->rightJoin("contracts", "contracts.lead_id", "=", "leads.id");

        $mainQuery = $this->getQuery($mainQuery, $queryParams, $franchiseIds);

        return $mainQuery->get();
    }
    
    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $mainQuery= DB::table('leads')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("avg(contracts.contract_price) as averageSalesPrice")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfSales")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) / count(leads.id) * 100 as conversionRate")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->join("job_types", "job_types.lead_id", "=", "leads.id")
            ->join("appointments", "appointments.lead_id", "=", "leads.id")
            ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
            ->join("franchises", "leads.franchise_id", "=", "franchises.id")
            ->rightJoin("contracts", "contracts.lead_id", "=", "leads.id")
            ->whereIn('franchises.id', $franchiseIds);

        $mainQuery = $this->getQuery($mainQuery, $queryParams, $franchiseIds);

        return $mainQuery->get();
    }

    public function getQuery($mainQuery, $queryParams, $franchiseIds)
    {
        $mainQuery->when(key_exists("status", $queryParams) && $queryParams['status'] == 'active', function($mainQuery) use($queryParams){
            $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
        })->when(key_exists("status", $queryParams) && $queryParams['status'] == 'blocked', function($mainQuery){
            $mainQuery->where('sales_staff.status', SalesStaff::BLOCKED);
        })->when(key_exists("status", $queryParams) && $queryParams['status'] == '', function($mainQuery){
            $mainQuery = $mainQuery->whereIn('sales_staff.status', [SalesStaff::ACTIVE, SalesStaff::BLOCKED]);
        });

        $mainQuery->when($queryParams['start_date'] !== null && $queryParams['end_date'] !== null, function($mainQuery) use($queryParams){
            $mainQuery->whereBetween('contracts.contract_date', [$queryParams['start_date'], $queryParams['end_date']]);
        });

        $mainQuery->when(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== "", function($mainQuery) use($franchiseIds){
            $mainQuery->whereIn('franchises.id', $franchiseIds);
        });

        $mainQuery->when(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->where('sales_staff.id', $queryParams['sales_staff_id'] );
        });

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'franchises.franchise_number'
        ]);

        $mainQuery->when(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        });

        $mainQuery = $mainQuery->orderBy('sales_staff.first_name', 'asc');
        
        return $mainQuery;
    }
}
