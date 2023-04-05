<?php

namespace App\Reports;

use Illuminate\Support\Facades\DB;
use App\SalesStaff;

class RoofSheetProfileReportImp implements Interfaces\RoofSheetProfileReport
{
    public function generate($queryParams)
    {
        $mainQuery = DB::table('leads')            
            ->select(
                'leads.lead_number',
                'contracts.id',
                'contracts.total_contract',
                'contracts.contract_date',
                'contracts.contract_price',
                'contracts.roof_sheet_profile',     
                'sales_staff.id',
                'franchises.name as franchise',
            )
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfSales")
            ->selectRaw("sum(contracts.contract_price) as valueOfSales")
            ->join('job_types', 'job_types.lead_id', '=', 'leads.id')
            ->join('sales_staff', 'job_types.sales_staff_id', '=', 'sales_staff.id')
            ->join('franchises', 'leads.franchise_id', '=', 'franchises.id')
            ->join('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->join('contracts', 'leads.id', '=', 'contracts.lead_id')
            ->where('appointments.outcome', 'success');
            
        $mainQuery = $this->getQuery($mainQuery, $queryParams);
        
        return $mainQuery->get();
    }
    
    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $mainQuery = DB::table('leads')            
            ->select(
                'leads.lead_number',
                'contracts.id',
                'contracts.total_contract',
                'contracts.contract_date',
                'contracts.contract_price',
                'contracts.roof_sheet_profile',     
                'sales_staff.id',
                'franchises.name as franchise',
            )
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfSales")
            ->selectRaw("sum(contracts.contract_price) as valueOfSales")
            ->join('job_types', 'job_types.lead_id', '=', 'leads.id')
            ->join('sales_staff', 'job_types.sales_staff_id', '=', 'sales_staff.id')
            ->join('franchises', 'leads.franchise_id', '=', 'franchises.id')
            ->join('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->join('contracts', 'leads.id', '=', 'contracts.lead_id')
            ->where('appointments.outcome', 'success')
            ->whereIn('leads.franchise_id', $franchiseIds);
            
        $mainQuery = $this->getQuery($mainQuery, $queryParams);
        
        return $mainQuery->get();
    }

    public function getQuery($mainQuery, $queryParams)
    {
        $mainQuery->when(key_exists("status", $queryParams) && $queryParams['status'] == 'active', function($mainQuery) use($queryParams){
            $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
        })->when(key_exists("status", $queryParams) && $queryParams['status'] == 'blocked', function($mainQuery){
            $mainQuery->where('sales_staff.status', SalesStaff::BLOCKED);
        })->when(key_exists("status", $queryParams) && $queryParams['status'] == '', function($mainQuery){
            $mainQuery = $mainQuery->whereIn('sales_staff.status', [SalesStaff::ACTIVE, SalesStaff::BLOCKED]);
        });

        $mainQuery->when($queryParams['start_date'] !== null && $queryParams['end_date'] !== null, function($mainQuery) use($queryParams){
            $mainQuery->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        });
        
        $mainQuery->when(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->where('franchises.id', $queryParams['franchise_id'] );
        });
        
        $mainQuery->when(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->where('franchises.type', $queryParams['franchise_type'] );
        });

        $mainQuery->when(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->where('sales_staff.id', $queryParams['sales_staff_id'] );
        });

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'franchises.franchise_number',
            'contracts.roof_sheet_profile'
        ]);

        $mainQuery->when(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        });

        return $mainQuery->orderBy('sales_staff.first_name', 'asc')->orderBy('contracts.contract_date', 'asc');
    }
}
