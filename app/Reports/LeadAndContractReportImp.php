<?php


namespace App\Reports;


use Illuminate\Support\Facades\DB;

class LeadAndContractReportImp implements Interfaces\LeadAndContractReport
{

    public function generate($queryParams)
    {

        $salesContactQuery = DB::table('leads')
            ->select(
                'leads.lead_number',
                'leads.id',
                'leads.lead_date',
                'job_types.sales_staff_id',
                'appointments.outcome',
                'products.name as product_name',
                'lead_sources.name as lead_source',
                'sales_contacts.last_name',
                'contracts.total_contract',
                'appointments.quoted_price'
            )->join('sales_contacts', 'leads.sales_contact_id', '=', 'sales_contacts.id')
            ->join('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->join('products', 'job_types.product_id', '=','products.id')
            ->join('appointments', 'leads.id', '=', 'appointments.lead_id')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id');


        $mainQuery = DB::table('sales_staff')
            ->select(
                'sales_staff.id',
                'leads.lead_number',
                'leads.lead_date',
                'leads.outcome',
                'leads.product_name',
                'leads.lead_source',
                'leads.last_name',
                'leads.quoted_price',
                'leads.total_contract'
            )->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as sales_staff")
            ->joinSub($salesContactQuery, 'leads', function ($join){
                $join->on('leads.sales_staff_id', '=', 'sales_staff.id');
            });

        if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null
            && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery->whereBetween('leads.lead_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }


        $mainQuery = $mainQuery->orderBy('sales_staff.id', 'asc')
            ->orderBy('leads.lead_date', 'asc');

        return $mainQuery->get();


    }

    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $salesContactQuery = DB::table('leads')
            ->select(
                'leads.lead_number',
                'leads.id',
                'leads.lead_date',
                'job_types.sales_staff_id',
                'appointments.outcome',
                'products.name as product_name',
                'lead_sources.name as lead_source',
                'sales_contacts.last_name',
                'contracts.total_contract',
                'appointments.quoted_price'
            )->join('sales_contacts', 'leads.sales_contact_id', '=', 'sales_contacts.id')
            ->join('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->join('products', 'job_types.product_id', '=','products.id')
            ->join('appointments', 'leads.id', '=', 'appointments.lead_id')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id');


        $mainQuery = DB::table('sales_staff')
            ->select(
                'sales_staff.id',
                'leads.lead_number',
                'leads.lead_date',
                'leads.outcome',
                'leads.product_name',
                'leads.lead_source',
                'leads.last_name',
                'leads.quoted_price',
                'leads.total_contract'
            )->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as sales_staff")
            ->join('franchise_sales_staff', 'sales_staff.id', '=', 'franchise_sales_staff.sales_staff_id')
            ->join('franchises', 'franchise_sales_staff.franchise_id', '=', 'franchises.id')
            ->joinSub($salesContactQuery, 'leads', function ($join){
                $join->on('leads.sales_staff_id', '=', 'sales_staff.id');
            })
            ->whereIn('franchises.id', $franchiseIds);

        if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null
            && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery->whereBetween('leads.lead_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }


        $mainQuery = $mainQuery->orderBy('sales_staff.id', 'asc')
            ->orderBy('leads.lead_date', 'asc');

        return $mainQuery->get();
    }
}
