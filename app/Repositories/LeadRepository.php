<?php

namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeadRepository implements LeadRepositoryInterface
{
    public function findSortPaginateByFranchise(Franchise $franchise, Array $params)
    {
        $query = DB::table('leads')->where('franchise_id', $franchise->id)
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->select(
                'leads.lead_number as leadNumber',
                'leads.lead_date',
                'leads.created_at as created_at',
                'lead_sources.name as source',
                'sales_contacts.id as salesContactId',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode'
            );
            
        if(key_exists('search', $params) && key_exists('on', $params))
        {

            if($params['on'] == 'lead_number' || $params['on'] == 'franchise_number') {
                $query = $query->where($params['on'], 'LIKE', $params['search'] . '%');
            }else {
                $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

            return $query->orderBy($params['column'], $params['direction'])->paginate($params['size']);

        }

        return $query->orderBy($params['column'], $params['direction'])->paginate($params['size']);
    }

    public function findLeadById($lead_id)
    {
        return DB::table('leads')->where('leads.id', $lead_id)
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->leftJoin('job_types', function($join) use ($lead_id) {
                $join->on( 'job_types.lead_id', '=', $lead_id)
                ->join('products', 'products.id', '=', 'job_types.product_id')
                ->join('sales_staffs', 'sales_staffs.id', '=', 'job_types.sales_staff_id');
            })
            ->leftJoin('appointments', 'appointments.lead_id', '=', $lead_id)
            ->select(
                    'leads.id',
                    'leads.lead_number as leadNumber',
                    'leads.lead_date as leadDate',
                    'lead_sources.name as source',
                    'sales_contacts.id as salesContactId',
                    'sales_contacts.first_name as firstName',
                    'sales_contacts.last_name as lastName',
                    'sales_contacts.email',
                    'sales_contacts.postcode',
                    'franchises.franchise_number as franchiseNumber',
                    'job_types.id as job_type_id',
                    'job_types.taken_by as takenBy',
                    'job_types.date_allocated as dateAllocated',
                    'job_types.description as job_type_description as jobDescription',
                    'appointments.id as appointment_id',
                    'appointments.appointment_date as appointmentDate ',
                    'appointments.followup_date as followUpDate ',
                    'appointments.appointment_notes as appointmentNotes',
                    'appointments.quoted_price as quotedPrice',
                    'appointments.outcome',
                    'appointments.comments',
                    'products.id as product_id',
                    'products.name as product_name as productName',
                    'sales_staffs.id as design_assessor_id',
                    'sales_staffs.first_name as designAssessorFirstName',
                    'sales_staffs.last_name  as designAssessorLastName')
            ->first();
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
        
        return $this->getQuery($query, $params);
    }

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
            );
            
        return $this->getQuery($query, $params);
    }

    public function getQuery($query, $params)
    {
        if(key_exists('search', $params) && key_exists('on', $params))
        {
            if($params['on'] == 'postcode'){
                $query->where('postcodes.pcode', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'suburb'){                
                $query->where('postcodes.locality', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'address'){                
                $query->where('sales_contacts.street1', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'lead_number'){                
                $query->where('leads.lead_number', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'reference_number'){                
                $query->where('leads.reference_number', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'first_name'){                
                $query->where('sales_contacts.first_name', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'last_name'){                
                $query->where('sales_contacts.last_name', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'state'){                
                $query->where('postcodes.state', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'outcome'){                
                $query->where('appointments.outcome', 'LIKE', '%' . $params['search'] . '%');
            }elseif($params['on'] == 'franchise_number'){                
                $query->where('franchises.franchise_number', 'LIKE', '%' . $params['search'] . '%');
            }else {
                $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

            if($params['column'] == 'postcode'){
                $query->orderBy('postcodes.pcode', $params['direction']);
            }elseif($params['column'] == 'suburb'){                
                $query->orderBy('postcodes.locality', $params['direction']);
            }elseif($params['column'] == 'address'){                
                $query->orderBy('sales_contacts.street1', $params['direction']);
            }elseif($params['column'] == 'lead_number'){                
                $query->orderBy('leads.lead_number', $params['direction']);
            }elseif($params['column'] == 'reference_number'){                
                $query->orderBy('leads.reference_number', $params['direction']);
            }elseif($params['column'] == 'first_name'){                
                $query->orderBy('sales_contacts.first_name', $params['direction']);
            }elseif($params['column'] == 'last_name'){                
                $query->orderBy('sales_contacts.last_name', $params['direction']);
            }elseif($params['column'] == 'state'){                
                $query->orderBy('postcodes.state', $params['direction']);
            }elseif($params['column'] == 'outcome'){                
                $query->orderBy('appointments.outcome', $params['direction']);
            }elseif($params['column'] == 'franchise_number'){                
                $query->orderBy('franchises.franchise_number', $params['direction']);
            }else {
                $query->orderBy($params['column'], $params['direction']);
            }

            return $query->paginate($params['size']);
        }
            
        if($params['column'] == 'postcode'){
            $query->orderBy('postcodes.pcode', $params['direction']);
        }elseif($params['column'] == 'suburb'){                
            $query->orderBy('postcodes.locality', $params['direction']);
        }elseif($params['column'] == 'address'){                
            $query->orderBy('sales_contacts.street1', $params['direction']);
        }elseif($params['column'] == 'lead_number'){                
            $query->orderBy('leads.lead_number', $params['direction']);
        }elseif($params['column'] == 'reference_number'){                
            $query->orderBy('leads.reference_number', $params['direction']);
        }elseif($params['column'] == 'first_name'){                
            $query->orderBy('sales_contacts.first_name', $params['direction']);
        }elseif($params['column'] == 'last_name'){                
            $query->orderBy('sales_contacts.last_name', $params['direction']);
        }elseif($params['column'] == 'state'){                
            $query->orderBy('postcodes.state', $params['direction']);
        }elseif($params['column'] == 'outcome'){                
            $query->orderBy('appointments.outcome', $params['direction']);
        }elseif($params['column'] == 'franchise_number'){                
            $query->orderBy('franchises.franchise_number', $params['direction']);
        }else {
            $query->orderBy($params['column'], $params['direction']);
        }
        
        return $query->paginate($params['size']);
    }
}
