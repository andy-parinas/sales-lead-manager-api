<?php

namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\LeadTransferRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeadTransferRepository implements LeadTransferRepositoryInterface
{   
    public function getAllLeads(Array $params)
    {
        $query = DB::table('leads')
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->select(
                'leads.id as leadId',
                'leads.lead_number as leadNumber',
                'leads.reference_number as referenceNumber',
                'leads.franchise_id',
                'franchises.franchise_number as franchiseNumber',
                'leads.lead_date as leadDate',
                'leads.created_at as created_at',
                'lead_sources.name as source',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'sales_contacts.street1 as firstaddress',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode',
                'appointments.outcome as outcome',
                'appointments.quoted_price as quotedPrice'
            )
            ->groupBy('leads.franchise_id');

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            if($params['on'] == 'postcode'){
                $query->where('postcodes.pcode', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'suburb'){                
                $query->where('postcodes.locality', 'LIKE', '%' . $params['search'] . '%');

            }else {
                $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

            if($params['column'] == 'postcode'){

                $query->orderBy('postcodes.pcode', $params['direction']);

            }elseif($params['column'] == 'suburb'){
                
                $query->orderBy('postcodes.locality', $params['direction']);
            }else {

                $query->orderBy($params['column'], $params['direction']);
            }
            
            return $query->paginate($params['size']);
        }
        
        if($params['column'] == 'postcode'){
            $query->orderBy('postcodes.pcode', $params['direction']);
        }else {            
            $query->orderBy($params['column'], $params['direction']);
        }
        
        return $query->paginate($params['size']);
    }

    public function getAllLeadsByFranchiseId($franchiseId, Array $params)
    {
        $query = DB::table('leads')
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->select(
                'leads.id as leadId',
                'leads.lead_number as leadNumber',
                'leads.reference_number as referenceNumber',
                'leads.franchise_id',
                'franchises.franchise_number as franchiseNumber',
                'leads.lead_date as leadDate',
                'leads.created_at as created_at',
                'lead_sources.name as source',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'sales_contacts.street1 as firstaddress',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode',
                'appointments.outcome as outcome',
                'appointments.quoted_price as quotedPrice'
            )
            ->where('leads.franchise_id', $franchiseId);

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            if($params['on'] == 'postcode'){
                $query->where('postcodes.pcode', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'suburb'){                
                $query->where('postcodes.locality', 'LIKE', '%' . $params['search'] . '%');
            }else {
                $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

            if($params['column'] == 'postcode'){

                $query->orderBy('postcodes.pcode', $params['direction']);

            }elseif($params['column'] == 'suburb'){
                
                $query->orderBy('postcodes.locality', $params['direction']);
            }else {

                $query->orderBy($params['column'], $params['direction']);
            }

            return $query->paginate($params['size']);
        }
            
        if($params['column'] == 'postcode'){
            $query->orderBy('postcodes.pcode', $params['direction']);
        }else {            
            $query->orderBy($params['column'], $params['direction']);
        }
        
        return $query->paginate($params['size']);
    }

    public function findLeadsByUsersFranchise(Array $franchiseIds, Array $params)
    {
        $query = DB::table('leads')->whereIn('franchise_id', $franchiseIds)
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->select(
                'leads.id as leadId',
                'leads.lead_number as leadNumber',
                'leads.reference_number as referenceNumber',
                'franchises.franchise_number as franchiseNumber',
                'leads.lead_date as leadDate',
                'leads.created_at as created_at',
                'leads.postcode_status as postcodeStatus',
                'lead_sources.name as source',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'sales_contacts.street1 as firstaddress',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode',
                'appointments.outcome as outcome',
                'appointments.quoted_price as quotedPrice'
            );

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            if($params['on'] == 'postcode'){
                $query->where('postcodes.pcode', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'suburb'){
                $query->where('postcodes.locality', 'LIKE', '%' . $params['search'] . '%');                
            }else {
                $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

            if($params['column'] == 'postcode'){
                $query->orderBy('postcodes.pcode', $params['direction']);
            }elseif($params['column'] == 'suburb'){                
                $query->orderBy('postcodes.locality', $params['direction']);
            }else {
                $query->orderBy($params['column'], $params['direction']);
            }           
            return $query->paginate($params['size']);
        }
        
        if($params['column'] == 'postcode'){
            $query->orderBy('postcodes.pcode', $params['direction']);
        }else {            
            $query->orderBy($params['column'], $params['direction']);
        }

        return $query->paginate($params['size']);
    }

    public function getTotalFranchiseInLeads($franchiseId)
    {
        $query = DB::table('leads')
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->where('leads.franchise_id', $franchiseId);

        return $query->count();
    }

    public function getFranchiseInLeads()
    {
        $query = DB::table('franchises')
            ->join('leads', 'leads.franchise_id', '=', 'franchises.id')
            ->select(
                'leads.id as leadId',
                'leads.lead_number as leadNumber',
                'leads.reference_number as referenceNumber',
                'franchises.id',
                'franchises.franchise_number',
                'franchises.name',
            )
            ->groupBy('franchises.id');

        return $query->get();
    }

    public function transferFranchises($newFranchiseId, $id)
    {
        try{
            DB::table('leads')->where('franchise_id', $id)
            ->chunkById(100, function($leads) use($newFranchiseId){
                foreach($leads as $lead){
                    DB::table('leads')->where('id', $lead->id)
                    ->update(['franchise_id' => $newFranchiseId]);
                }
            });
            
            return true;
        } catch(Exception $e){
            return false;
        } 
    }
}
