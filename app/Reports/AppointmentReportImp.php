<?php

namespace App\Reports;

use Illuminate\Support\Facades\DB;
use App\Lead;
use App\SalesStaff;

class AppointmentReportImp implements Interfaces\AppointmentReport
{
    public function getAllAppointment($queryParams)
    {
        $mainQuery = DB::table('appointments')
            ->select(
                'appointments.id as appointment_id',
                'appointments.quoted_price',
                'appointments.outcome',
                'appointments.comments',
                'leads.id as lead_id',
                'leads.lead_number',
                'leads.lead_date',
                'franchises.name as franchise',
                'contracts.contract_date',
                'products.name as product_name',
            )
            ->selectRaw('DATE_FORMAT(leads.lead_date, "%d/%m/%Y") as lead_date')
            ->selectRaw('DATE_FORMAT(contracts.contract_date, "%d/%m/%Y") as contract_date')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
            ->leftJoin('leads', 'leads.id', '=', 'appointments.lead_id')
            ->leftJoin('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->leftJoin('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->leftJoin("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin("contracts", "leads.id", '=', "contracts.lead_id");
                                                                                                                                                                                                                                                                         
        $mainQuery = $this->parameterFilters($queryParams, $mainQuery);
        return $mainQuery;
    }

    public function getAllAppointmentByFranchise($franchiseIds, $queryParams)
    {
        $mainQuery = DB::table('appointments')
            ->select(
                'appointments.id as appointment_id',
                'appointments.quoted_price',
                'appointments.outcome',
                'appointments.comments',
                'leads.id as lead_id',
                'leads.lead_number',
                'leads.lead_date',
                'franchises.name as franchise',
                'contracts.contract_date',
                'products.name as product_name',
            )
            ->selectRaw('DATE_FORMAT(leads.lead_date, "%d/%m/%Y") as lead_date')
            ->selectRaw('DATE_FORMAT(contracts.contract_date, "%d/%m/%Y") as contract_date')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
            ->leftJoin('leads', 'leads.id', '=', 'appointments.lead_id')
            ->leftJoin('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->leftJoin('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->leftJoin("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin("contracts", "leads.id", '=', "contracts.lead_id")
            ->whereIn('franchises.id', $franchiseIds);  ;
                                                                                                                                                                                                                                                                         
        $mainQuery = $this->parameterFilters($queryParams, $mainQuery);
        return $mainQuery;
    }
    
    
    public function parameterFilters($queryParams, $mainQuery)
    {
        $mainQuery->when($queryParams['start_date'] !== null && $queryParams['end_date'] !== null, function($mainQuery) use($queryParams){
            $mainQuery->whereBetween('appointments.created_at', [$queryParams['start_date'], $queryParams['end_date']]);
        });
        
        // $mainQuery->when(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== "", function($mainQuery) use($queryParams){
        //     $mainQuery->where('franchises.id', $queryParams['franchise_id'] );
        // });
        
        // $mainQuery->when(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== "", function($mainQuery) use($queryParams){
        //     $mainQuery->where('franchises.type', $queryParams['franchise_type'] );
        // });

        // $mainQuery->when(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== "", function($mainQuery) use($queryParams){
        //     $mainQuery->where('sales_staff.id', $queryParams['sales_staff_id'] );
        // });

        // $mainQuery = $mainQuery->groupBy([
        //     'appointments.comments',
        // ]);

        // $mainQuery->when(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== "", function($mainQuery) use($queryParams){
        //     $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        // });

        $mainQuery = $mainQuery->orderBy('appointments.comments', 'desc')->get();

        return $mainQuery;
    }
}
