<?php


namespace App\Reports;


use Illuminate\Support\Facades\DB;

class SalesContractVariationReportImp implements Interfaces\SalesContractVariationReport
{

    public function generate($queryParams)
    {
        $salesContactQuery = DB::table('sales_contacts')
            ->select('sales_contacts.id', 'postcodes.pcode as postcode', 'postcodes.locality as suburb')
            ->selectRaw("concat(sales_contacts.first_name, ' ', sales_contacts.last_name) as name")
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id');
            
        $mainQuery = DB::table('leads')
            ->select(
                'leads.lead_number',
                'lead_sources.name as source',
                'sales_staff.id',
                'sales_contacts.name as customer',
                'sales_contacts.postcode',
                'sales_contacts.suburb',
                'products.name as product',
                'contracts.id',
                'contracts.total_contract',
                'contracts.contract_date',
                'contracts.contract_price',
                'contract_variations.variation_date',
                'contract_variations.amount as variation_amount',
            )
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as sales_staff_name")
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->join('job_types', 'job_types.lead_id', '=', 'leads.id')
            ->join('sales_staff', 'job_types.sales_staff_id', '=', 'sales_staff.id')
            ->join('products', 'job_types.product_id', '=', 'products.id')
            ->join('appointments', 'appointments.lead_id', '=', 'leads.id')
            
            ->joinSub($salesContactQuery, 'sales_contacts', function ($join){
                $join->on('sales_contacts.id', '=', 'leads.sales_contact_id');
            })
            ->rightJoin('contracts', 'contracts.lead_id', '=', 'leads.id')
            ->join('contract_variations', 'contract_variations.contract_id', '=', 'contracts.id')
            ->where('appointments.outcome', 'success');
            
        if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('contract_variations.variation_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }
        
        $mainQuery = $mainQuery->orderBy('sales_staff.id', 'asc')->orderBy('contract_variations.variation_date', 'asc');

        return $mainQuery->get();
    }

    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $salesContactQuery = DB::table('sales_contacts')
            ->select('sales_contacts.id', 'postcodes.pcode as postcode', 'postcodes.locality as suburb')
            ->selectRaw("concat(sales_contacts.first_name, ' ', sales_contacts.last_name) as name")
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id');


        $mainQuery = DB::table('leads')
            ->select(
                'leads.lead_number',
                'lead_sources.name as source',
                'sales_staff.id',
                'sales_contacts.name as customer',
                'sales_contacts.postcode',
                'sales_contacts.suburb',
                'products.name as product',
                'contracts.total_contract',
                'contracts.contract_date',
                'contracts.contract_price',
                'contract_variations.variation_date',
                'contract_variations.amount as variation_amount',
            )
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as sales_staff_name")
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->join('job_types', 'job_types.lead_id', '=', 'leads.id')
            ->join('sales_staff', 'job_types.sales_staff_id', '=', 'sales_staff.id')
            ->join('products', 'job_types.product_id', '=', 'products.id')
            ->join('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->joinSub($salesContactQuery, 'sales_contacts', function ($join){
                $join->on('sales_contacts.id', '=', 'leads.sales_contact_id');
            })
            ->rightJoin('contracts', 'contracts.lead_id', '=', 'leads.id')
            ->join('contract_variations', 'contract_variations.contract_id', '=', 'contracts.id')
            ->where('appointments.outcome', 'success')
            ->whereIn('leads.franchise_id', $franchiseIds);

        if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('contract_variations.variation_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }

        $mainQuery = $mainQuery->orderBy('sales_staff.id', 'asc')->orderBy('contract_variations.variation_date', 'asc');

        return $mainQuery->get();
    }
}
