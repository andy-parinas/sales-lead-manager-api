<?php

namespace App\Reports;

use Illuminate\Support\Facades\DB;
use App\Lead;
use App\SalesStaff;

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
                'sales_contacts.first_name',
                'sales_contacts.last_name',
                'sales_contacts.email',
                'postcodes.pcode',
                'postcodes.locality as suburb',
                'sales_contacts.contact_number as phone_number',
                'contracts.contract_date',
                'job_types.sales_staff_id',
                'appointments.outcome as lead_status',
                'lead_sources.name as heard_about',
            )
            ->selectRaw('DATE_FORMAT(leads.lead_date, "%d/%m/%Y") as lead_date')
            ->leftJoin('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
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
                'sales_contacts.first_name',
                'sales_contacts.last_name',
                'sales_contacts.email',
                'postcodes.pcode',
                'postcodes.locality as suburb',
                'sales_contacts.contact_number as phone_number',
                'contracts.contract_date',
                'job_types.sales_staff_id',
                'appointments.outcome as lead_status',
                'lead_sources.name as heard_about',
            )
            ->selectRaw('DATE_FORMAT(leads.lead_date, "%d/%m/%Y") as lead_date')
            ->leftJoin('job_types', 'leads.id', '=', 'job_types.lead_id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
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

    public function generateLeadAndContract($franchiseIds, $queryParams)
    {
        //START STEP 1 START LEAD QUERY
        $leadCountQuery = DB::table('leads')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("count(leads.id) as total_leads")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id")
        ->leftJoin("contracts", "contracts.lead_id", "=", "leads.id");

        $leadCountQuery = $this->leadsParamFilters($leadCountQuery, $franchiseIds, $queryParams, 'lead');
        
        $leadCountsArray = [];
        foreach($leadCountQuery as $lead) {
            $leadCountsArray[$lead->franchiseNumber."_".$lead->design_advisor."_".$lead->sales_staff_id] = [
                'franchiseNumber' => isset($lead->franchiseNumber) ? $lead->franchiseNumber : '',
                'salesStaff' => isset($lead->design_advisor) ? $lead->design_advisor : '',
                'totalLeads' => $lead->total_leads,
            ];
        }

        //START STEP 2 CONTRACT QUERY
        $contractCountQuery = DB::table('contracts')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("avg(contracts.contract_price) as averageSalesPrice")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) / count(leads.id) * 100 as conversionRate")
        ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfContracts")
        ->join("leads", "leads.id", "=", "contracts.lead_id")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id");

        $contractCountQuery = $this->leadsParamFilters($contractCountQuery, $franchiseIds, $queryParams, 'contract');
        
        $contractCountsArray = [];
        foreach($contractCountQuery as $contract) {
            
            $contractCountsArray[$contract->franchiseNumber."_".$contract->design_advisor."_".$contract->sales_staff_id] = [
                'franchiseNumber' => isset($contract->franchiseNumber) ? $contract->franchiseNumber : '',
                'salesStaff' => isset($contract->design_advisor) ? $contract->design_advisor : '',
                'totalContracts' => $contract->numberOfContracts,
                'averageSalesPrice' => $contract->averageSalesPrice,                
            ];
        }

        $leadAndContractCounts = $this->array_merge_recursive_ex($leadCountsArray, $contractCountsArray);

        foreach($leadAndContractCounts as $key => $leadAndContractCount) {
            if(!isset($leadAndContractCount['totalContracts'])) {
                $leadAndContractCounts[$key]['totalContracts'] = 0;
            }

            if(!isset($leadAndContractCount['totalLeads'])) {
                $leadAndContractCounts[$key]['totalLeads'] = 0;
            }

            if(!isset($leadAndContractCount['averageSalesPrice'])) {
                $leadAndContractCounts[$key]['averageSalesPrice'] = 0;
            }

            if(!isset($leadAndContractCount['conversionRate'])) {
                $leadAndContractCounts[$key]['conversionRate'] = 0;
            }
        }

        //START STEP 3 SUM TOTAL CONTRACT QUERY
        $sumContractQuery = DB::table('contracts')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("sum(contracts.total_contract) as totalSumOfContracts")
        ->join("leads", "leads.id", "=", "contracts.lead_id")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id");

        $sumOfContracts = $this->leadsParamFilters($sumContractQuery, $franchiseIds, $queryParams, 'contract');

        $sumContractArray = [];
        foreach($sumOfContracts as $sumOfContract) {
            $sumContractArray[$sumOfContract->franchiseNumber."_".$sumOfContract->design_advisor."_".$sumOfContract->sales_staff_id] = [
                'sumOfTotalContracts' => $sumOfContract->totalSumOfContracts,
            ];
        }
        
        $newleadAndContractCounts = $this->array_merge_recursive_ex($leadAndContractCounts, $sumContractArray);
        
        foreach($newleadAndContractCounts as $key => $leadAndContractCount) {            
            $totalLeadToOne = ($leadAndContractCount['totalLeads'] == 0? 1 : $leadAndContractCount['totalLeads']);
            $conversionRate = $leadAndContractCount['totalContracts'] / $totalLeadToOne;

            $newleadAndContractCounts[$key]['conversionRate'] = $conversionRate;

            if(!isset($leadAndContractCount['sumOfTotalContracts'])) {
                $newleadAndContractCounts[$key]['sumOfTotalContracts'] = 0;
            }
        }

        $newleadAndContractCounts = array_values($newleadAndContractCounts);

        return $newleadAndContractCounts;
    }    

    public function generateLeadAndContractByFranchise($franchiseIds, $queryParams)
    {
        //START STEP 1 START LEAD QUERY
        $leadCountQuery = DB::table('leads')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("count(leads.id) as total_leads")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id")
        ->leftJoin("contracts", "contracts.lead_id", "=", "leads.id")
        ->whereIn('franchises.id', $franchiseIds);

        $leadCountQuery = $this->leadsParamFilters($leadCountQuery, $franchiseIds, $queryParams, 'lead');
        
        $leadCountsArray = [];
        foreach($leadCountQuery as $lead) {
            $leadCountsArray[$lead->franchiseNumber."_".$lead->design_advisor."_".$lead->sales_staff_id] = [
                'franchiseNumber' => isset($lead->franchiseNumber) ? $lead->franchiseNumber : '',
                'salesStaff' => isset($lead->design_advisor) ? $lead->design_advisor : '',
                'totalLeads' => $lead->total_leads,
            ];
        }

        //START STEP 2 CONTRACT QUERY
        $contractCountQuery = DB::table('contracts')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("avg(contracts.contract_price) as averageSalesPrice")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) / count(leads.id) * 100 as conversionRate")
        ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfContracts")
        ->join("leads", "leads.id", "=", "contracts.lead_id")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id")
        ->whereIn('franchises.id', $franchiseIds);

        $contractCountQuery = $this->leadsParamFilters($contractCountQuery, $franchiseIds, $queryParams, 'contract');

        $contractCountsArray = [];
        foreach($contractCountQuery as $contract) {
            $contractCountsArray[$contract->franchiseNumber."_".$contract->design_advisor."_".$contract->sales_staff_id] = [
                'franchiseNumber' => isset($contract->franchiseNumber) ? $contract->franchiseNumber : '',
                'salesStaff' => isset($contract->design_advisor) ? $contract->design_advisor : '',
                'totalContracts' => $contract->numberOfContracts,
                'averageSalesPrice' => $contract->averageSalesPrice,
            ];
        }

        $leadAndContractCounts = $this->array_merge_recursive_ex($leadCountsArray, $contractCountsArray);

        foreach($leadAndContractCounts as $key => $leadAndContractCount) {
            if(!isset($leadAndContractCount['totalContracts'])) {
                $leadAndContractCounts[$key]['totalContracts'] = 0;
            }

            if(!isset($leadAndContractCount['totalLeads'])) {
                $leadAndContractCounts[$key]['totalLeads'] = 0;
            }

            if(!isset($leadAndContractCount['averageSalesPrice'])) {
                $leadAndContractCounts[$key]['averageSalesPrice'] = 0;
            }

            if(!isset($leadAndContractCount['conversionRate'])) {
                $leadAndContractCounts[$key]['conversionRate'] = 0;
            }
        }

        //START STEP 3 SUM TOTAL CONTRACT QUERY
        $sumContractQuery = DB::table('contracts')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("sum(contracts.total_contract) as totalSumOfContracts")
        ->join("leads", "leads.id", "=", "contracts.lead_id")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id")
        ->whereIn('franchises.id', $franchiseIds);

        $sumOfContracts = $this->leadsParamFilters($sumContractQuery, $franchiseIds, $queryParams, 'contract');

        $sumContractArray = [];
        foreach($sumOfContracts as $sumOfContract) {
            $sumContractArray[$sumOfContract->franchiseNumber."_".$sumOfContract->design_advisor."_".$sumOfContract->sales_staff_id] = [
                'sumOfTotalContracts' => $sumOfContract->totalSumOfContracts,
            ];
        }
        
        $newleadAndContractCounts = $this->array_merge_recursive_ex($leadAndContractCounts, $sumContractArray);
        
        foreach($newleadAndContractCounts as $key => $leadAndContractCount) {

            $totalLeadToOne = ($leadAndContractCount['totalLeads'] == 0? 1 : $leadAndContractCount['totalLeads']);
            $conversionRate = $leadAndContractCount['totalContracts'] / $totalLeadToOne;

            $newleadAndContractCounts[$key]['conversionRate'] = $conversionRate;
            
            if(!isset($leadAndContractCount['sumOfTotalContracts'])) {
                $newleadAndContractCounts[$key]['sumOfTotalContracts'] = 0;
            }
        }

        $newleadAndContractCounts = array_values($newleadAndContractCounts);

        return $newleadAndContractCounts;
    }

    function array_merge_recursive_ex(array $array1, array $array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => & $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    public function paramContractFilters($mainQuery, $queryParams)
    {
        if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('contracts.contract_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }
        
        $mainQuery = $mainQuery->groupBy('lead_id')->orderBy('contracts.contract_date', 'desc')->get();

        return $mainQuery;
    }

    public function paramFilters($mainQuery, $franchiseIds, $queryParams)
    {
        if(isset($queryParams['franchise_id']) && $queryParams['franchise_id'] !== null){
            $mainQuery = $mainQuery->where('franchises.id', $franchiseIds);
        }

        if(isset($queryParams['sales_staff_id']) && $queryParams['sales_staff_id'] !== null){
            $mainQuery = $mainQuery->where('sales_staff.id', $queryParams['sales_staff_id']);
        }

        if(isset($queryParams['status']) && $queryParams['status'] !== null){
            if($queryParams['status'] == 'active'){
                $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
            }else if($queryParams['status'] == 'blocked'){
                $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::BLOCKED);
            }else{
                $mainQuery = $mainQuery->whereIn('sales_staff.status', [SalesStaff::ACTIVE, SalesStaff::BLOCKED]);
            }
        }
        
        $mainQuery = $mainQuery->get();

        return $mainQuery;
    }

    public function leadsParamFilters($mainQuery, $franchiseIds, $queryParams, $dateFilter)
    {
        $mainQuery->when(key_exists("status", $queryParams) && $queryParams['status'] == 'active', function($mainQuery) use($queryParams){
            $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
        })->when(key_exists("status", $queryParams) && $queryParams['status'] == 'blocked', function($mainQuery){
            $mainQuery->where('sales_staff.status', SalesStaff::BLOCKED);
        })->when(key_exists("status", $queryParams) && $queryParams['status'] == '', function($mainQuery){
            $mainQuery = $mainQuery->whereIn('sales_staff.status', [SalesStaff::ACTIVE, SalesStaff::BLOCKED]);
        });

        $mainQuery->when($queryParams['start_date'] !== null && $queryParams['end_date'] !== null, function($mainQuery) use($queryParams, $dateFilter){
            if($dateFilter == 'lead'){
                    $mainQuery->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
            } else {
                $mainQuery->whereBetween('contracts.contract_date', [$queryParams['start_date'], $queryParams['end_date']]);
            }
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
            'franchises.franchise_number'
        ]);

        $mainQuery->when(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== "", function($mainQuery) use($queryParams){
            $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        });

        $mainQuery = $mainQuery->orderBy('sales_staff.first_name', 'asc')->get();

        return $mainQuery;
    }

    public function generateDesignAdvisorById($franchiseId)
    {
        $mainQuery = DB::table('leads')
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw("sales_staff.id as sales_staff_id")
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("count(leads.id) as total_leads")
        ->selectRaw("GROUP_CONCAT(DISTINCT leads.id) as lead_ids")
        ->join("job_types", "job_types.lead_id", "=", "leads.id")
        ->join("appointments", "appointments.lead_id", "=", "leads.id")
        ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
        ->join("franchises", "leads.franchise_id", "=", "franchises.id")
        ->leftJoin("contracts", "contracts.lead_id", "=", "leads.id");

        $mainQuery = $mainQuery->whereIn('sales_staff.status', [SalesStaff::ACTIVE, SalesStaff::BLOCKED]);

        $mainQuery->where('franchises.id', $franchiseId);

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'franchises.franchise_number'
        ]);

        $mainQuery = $mainQuery->orderBy('sales_staff.first_name', 'asc')->get();

        return $mainQuery;
    }
}
