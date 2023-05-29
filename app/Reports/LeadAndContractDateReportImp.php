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

    public function generateLeadAndContract($userType, $franchiseIds, $queryParams)
    {
        $mainLeadsQuery = DB::table('leads')
        ->join('job_types', 'job_types.lead_id', 'leads.id')
        ->join('sales_staff', 'sales_staff.id', 'job_types.sales_staff_id')
        ->join('franchises', 'franchises.id', 'leads.franchise_id')
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw('count(leads.id) as total_leads')
        ->addSelect('leads.id as lead_id')
        ->groupBy('job_types.sales_staff_id', 'leads.id');
        
        $mainLeadsQuery = $this->leadsParamFilters($mainLeadsQuery, $franchiseIds, $queryParams);
        
        $newLeadsIds1 = [];
        $newLeads1 = [];
        foreach($mainLeadsQuery as $key => $lead) {
            $lead = (array)$lead;
            $newLeads1[$lead['franchiseNumber']] = $lead;
            $newLeadsIds1[$lead['franchiseNumber']]['lead_ids'][] = $lead['lead_id'];
            unset($newLeads1[$lead['franchiseNumber']]['lead_id']);
        }

        foreach($newLeads1 as $key => $newLead) {
            $newLeads1[$key]['lead_ids'] = $newLeadsIds1[$key]['lead_ids'];
            $newLeads1[$key]['total_leads'] = count($newLeadsIds1[$key]['lead_ids']);
        }
        
        $newArray1 = [];
        foreach($newLeads1 as $lead) {
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){

                $contracts = DB::table('contracts')
                            ->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']])
                            ->whereIn('contracts.lead_id', $lead['lead_ids'])
                            ->get();
                            
                $newArray1[$lead['franchiseNumber']] = [
                    'franchiseNumber' => isset($lead['franchiseNumber']) ? $lead['franchiseNumber'] : '',
                    'salesStaff' => isset($lead['design_advisor']) ? $lead['design_advisor'] : '',
                    'totalLeads' => $lead['total_leads'],
                    'totalContracts' => $contracts->count(),
                    'sumOfTotalContracts' => $contracts->sum('total_contract'),
                ];
            }
        }
        //END LEADS QUERY
        
        //START CONTRACTS QUERY
        $mainQuery = DB::table('contracts')->select('lead_id');
        
        $combinedLeadsAndContracts = $this->contractsQuery($userType, $mainQuery, $newArray1, $franchiseIds, $queryParams);
        return $combinedLeadsAndContracts;
        //END CONTRACTS QUERY
    }

    public function generateLeadAndContractByFranchise($userType, $franchiseIds, $queryParams)
    {
        //START LEADS QUERY
        $mainLeadsQuery = DB::table('leads')
        ->join('job_types', 'job_types.lead_id', 'leads.id')
        ->join('sales_staff', 'sales_staff.id', 'job_types.sales_staff_id')
        ->join('franchises', 'franchises.id', 'leads.franchise_id')
        ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
        ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
        ->selectRaw('count(leads.id) as total_leads')
        ->addSelect('leads.id as lead_id')
        ->whereIn('franchises.id', $franchiseIds);
                
        $mainLeadsQuery = $this->leadsParamFilters($mainLeadsQuery, $franchiseIds, $queryParams);
        
        $newLeadsIds1 = [];
        $newLeads1 = [];
        foreach($mainLeadsQuery as $key => $lead) {
            $lead = (array)$lead;
            $newLeads1[$lead['franchiseNumber']] = $lead;
            $newLeadsIds1[$lead['franchiseNumber']]['lead_ids'][] = $lead['lead_id'];
            unset($newLeads1[$lead['franchiseNumber']]['lead_id']);
        }

        foreach($newLeads1 as $key => $newLead) {
            $newLeads1[$key]['lead_ids'] = $newLeadsIds1[$key]['lead_ids'];
            $newLeads1[$key]['total_leads'] = count($newLeadsIds1[$key]['lead_ids']);
        }

        $newArray1 = [];
        foreach($newLeads1 as $lead) {
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){

                $contracts = DB::table('contracts')
                            ->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']])
                            ->whereIn('contracts.lead_id', $lead['lead_ids'])
                            ->get();

                $newArray1[$lead['franchiseNumber']] = [
                    'franchiseNumber' => isset($lead['franchiseNumber']) ? $lead['franchiseNumber'] : '',
                    'salesStaff' => isset($lead['design_advisor']) ? $lead['design_advisor'] : '',
                    'totalLeads' => $lead['total_leads'],
                    'totalContracts' => $contracts->count(),
                    'sumOfTotalContracts' => $contracts->sum('total_contract'),
                ];
            }
        }
        //dd($newArray1);
        //END LEADS QUERY

        //START CONTRACTS QUERY
        $mainQuery = DB::table('contracts')->select('lead_id');
        
        $combinedLeadsAndContracts = $this->contractsQuery($userType, $mainQuery, $newArray1, $franchiseIds, $queryParams);
        return $combinedLeadsAndContracts;
        //END CONTRACTS QUERY
    }

    public function contractsQuery($userType, $mainQuery, $newArray1, $franchiseIds, $queryParams)
    {
        $mainQuery = $this->paramContractFilters($mainQuery, $queryParams);
        $leadIds = $mainQuery->pluck('lead_id')->toArray();
        
        if($userType == 'franchise_admin'){
            $leadsQuery = DB::table('leads')
            ->join('job_types', 'job_types.lead_id', 'leads.id')
            ->join('sales_staff', 'sales_staff.id', 'job_types.sales_staff_id')
            ->join('franchises', 'franchises.id', 'leads.franchise_id')
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
            ->addSelect('leads.id as lead_id')
            ->whereIn('leads.id', $leadIds)
            ->groupBy('job_types.sales_staff_id', 'leads.id')
            ->whereIn('franchises.id', $franchiseIds);
        } else {
            $leadsQuery = DB::table('leads')
            ->join('job_types', 'job_types.lead_id', 'leads.id')
            ->join('sales_staff', 'sales_staff.id', 'job_types.sales_staff_id')
            ->join('franchises', 'franchises.id', 'leads.franchise_id')
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as design_advisor")
            ->addSelect('leads.id as lead_id')
            ->whereIn('leads.id', $leadIds)
            ->groupBy('job_types.sales_staff_id', 'leads.id');
        }

        $leadsQuery = $this->paramFilters($leadsQuery, $franchiseIds, $queryParams);
        
        $newLeadsIds = [];
        $newLeads = [];
        foreach($leadsQuery as $key => $lead) {
            $lead = (array)$lead;
            $newLeads[$lead['franchiseNumber']] = $lead;
            $newLeadsIds[$lead['franchiseNumber']]['lead_ids'][] = $lead['lead_id'];
        }
        
        foreach($newLeads as $key => $newLead) {
            $newLeads[$key]['lead_ids'] = $newLeadsIds[$key]['lead_ids'];
            $newLeads[$key]['total_leads'] = count($newLeadsIds[$key]['lead_ids']);
        }

        $newArray = [];
        foreach($newLeads as $lead) {
            if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
                $contracts = DB::table('contracts')
                            ->whereBetween('contracts.contract_date',[$queryParams['start_date'], $queryParams['end_date']])
                            ->whereIn('contracts.lead_id', $lead['lead_ids'])
                            ->get();
                            
                $newArray[] = [
                    'franchiseNumber' => isset($lead['franchiseNumber']) ? $lead['franchiseNumber'] : '',
                    'salesStaff' => isset($lead['design_advisor']) ? $lead['design_advisor'] : '',
                    'totalLeads' => isset($newArray1[$lead['franchiseNumber']]['totalLeads'])? $newArray1[$lead['franchiseNumber']]['totalLeads'] : 0,
                    'totalContracts' => isset($newArray1[$lead['franchiseNumber']]['totalContracts'])? $newArray1[$lead['franchiseNumber']]['totalContracts'] : 0,
                    'sumOfTotalContracts' => $contracts->sum('total_contract'),
                ];
            }
        }
        return $newArray;
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

    public function leadsParamFilters($mainQuery, $franchiseIds, $queryParams)
    {
        if(isset($queryParams['start_date']) && $queryParams['start_date'] !== null && isset($queryParams['end_date']) && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('leads.lead_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }

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
}
