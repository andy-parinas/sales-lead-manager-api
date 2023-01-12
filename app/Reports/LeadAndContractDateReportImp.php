<?php


namespace App\Reports;


use Illuminate\Support\Facades\DB;

class LeadAndContractDateReportImp implements Interfaces\LeadAndContractDateReport
{

    public function generate($queryParams)
    {
        $orderBy = isset($queryParams['search_orderby'])? $queryParams['search_orderby'] : 'desc';

        $mainQuery = DB::table('leads')
            ->select(
                'leads.id',
                'leads.lead_number',      
                'products.name as product_name',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'sales_staff.email',
                'postcodes.pcode',
                'postcodes.locality as suburb',
                'sales_staff.contact_number as phone_number',
                'contracts.contract_date',
                'job_types.sales_staff_id',
                'appointments.outcome as lead_status',
                'lead_sources.name as heard_about',
            )
            ->selectRaw('DATE_FORMAT(leads.lead_date, "%d/%m/%Y") as lead_date')
            ->leftJoin('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin('sales_staff', 'job_types.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('sales_contacts', 'leads.sales_contact_id', '=', 'sales_contacts.id')
            ->leftJoin('postcodes', 'sales_contacts.postcode_id', '=', 'postcodes.id')
            ->leftJoin('contracts', 'leads.id', '=', 'contracts.lead_id')
            ->leftJoin('appointments', 'leads.id', '=', 'appointments.lead_id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id');                   

        if(key_exists("search_type", $queryParams) && $queryParams['search_type'] == "contract_date"){
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
                $mainQuery = $mainQuery->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']]);
            }

            $mainQuery = $mainQuery->orderBy('contracts.contract_date', $orderBy);
        } else {
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
                $mainQuery = $mainQuery->whereBetween('leads.lead_date',[$queryParams['start_date'], $queryParams['end_date']]);
            } 

            $mainQuery = $mainQuery->orderBy('leads.lead_date', $orderBy);
        }
        
        $mainQuery = $mainQuery->orderBy('leads.lead_date', $orderBy);

        return $mainQuery->get();
    }

    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $orderBy = isset($queryParams['search_orderby'])? $queryParams['search_orderby'] : 'desc';

        $mainQuery = DB::table('leads')
            ->select(
                'leads.id',
                'leads.lead_number',      
                'products.name as product_name',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'sales_staff.email',
                'postcodes.pcode',
                'postcodes.locality as suburb',
                'sales_staff.contact_number as phone_number',
                'contracts.contract_date',
                'job_types.sales_staff_id',
                'appointments.outcome as lead_status',
                'lead_sources.name as heard_about',
            )
            ->selectRaw('DATE_FORMAT(leads.lead_date, "%d/%m/%Y") as lead_date')
            ->leftJoin('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin('sales_staff', 'job_types.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('sales_contacts', 'leads.sales_contact_id', '=', 'sales_contacts.id')
            ->leftJoin('postcodes', 'sales_contacts.postcode_id', '=', 'postcodes.id')
            ->leftJoin('contracts', 'leads.id', '=', 'contracts.lead_id')
            ->leftJoin('appointments', 'leads.id', '=', 'appointments.lead_id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->leftJoin('franchises', 'franchises.id', 'leads.franchise_id')
            ->whereIn('franchises.id', $franchiseIds);             

        if(key_exists("search_type", $queryParams) && $queryParams['search_type'] == "contract_date"){
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
                $mainQuery = $mainQuery->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']]);
            }

            $mainQuery = $mainQuery->orderBy('contracts.contract_date', $orderBy);
        } else {
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
                $mainQuery = $mainQuery->whereBetween('leads.lead_date',[$queryParams['start_date'], $queryParams['end_date']]);
            } 

            $mainQuery = $mainQuery->orderBy('leads.lead_date', $orderBy);
        }
        
        $mainQuery = $mainQuery->orderBy('leads.lead_date', $orderBy);

        return $mainQuery->get();
    }
}
