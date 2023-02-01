<?php


namespace App\Repositories;


use App\Repositories\Interfaces\CustomerReviewReportInterface;
use Illuminate\Support\Facades\DB;

class CustomerReviewReportRepository implements CustomerReviewReportInterface
{
    public function getAll($queryParams)
    {
        $mainQuery = DB::table('leads')
            ->select('leads.id',
                'leads.sales_contact_id',
                'leads.lead_number',
                'leads.lead_date',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'sales_staff.id',
                'products.name as product_name',
                'franchises.franchise_number',
                'franchises.id as franchise_id',
                'franchises.type',
                'customer_reviews.date_project_completed',
                'customer_reviews.workmanship_rating',
                'customer_reviews.service_received_rating',
                'customer_reviews.finished_product_rating',
                'customer_reviews.design_consultant_rating',
                'customer_reviews.comments',
            )
            ->leftJoin('job_types', 'job_types.lead_id', '=',  'leads.id')
            ->leftJoin('sales_staff', 'job_types.sales_staff_id', '=',  'sales_staff.id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->leftJoin('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->leftJoin('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->leftJoin('customer_reviews', 'customer_reviews.lead_id', '=', 'leads.id')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("concat(sales_contacts.last_name, ' ', postcodes.locality) as last_name_suburb");

        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('customer_reviews.date_project_completed', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){
            $mainQuery = $mainQuery->where('leads.franchise_id',$queryParams['franchise_id'] );
        }

        if(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== ""){
            $mainQuery = $mainQuery->where('franchises.type', $queryParams['franchise_type'] );
        }

        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){
            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }
        
        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){
            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }

        $mainQuery->groupBy([
            'customer_reviews.date_project_completed',
            'customer_reviews.date_warranty_received',
            'customer_reviews.workmanship_rating',
            'customer_reviews.service_received_rating',
            'customer_reviews.finished_product_rating',
            'customer_reviews.design_consultant_rating',
            'customer_reviews.comments',
            'leads.lead_number',
            'leads.lead_date',
            'leads.franchise_id',
        ]);
        
        return $mainQuery->get();
        
    }

    public function getAllByFranchise($franchiseIds, $queryParams)
    {
        $mainQuery = DB::table('leads')
            ->select('leads.id',
                'leads.sales_contact_id',
                'leads.lead_number',
                'leads.lead_date',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'sales_staff.id',
                'products.name as product_name',
                'franchises.franchise_number',
                'franchises.id as franchise_id',
                'franchises.type as franchise_type',
                'customer_reviews.date_project_completed',
                'customer_reviews.workmanship_rating',
                'customer_reviews.service_received_rating',
                'customer_reviews.finished_product_rating',
                'customer_reviews.design_consultant_rating',
                'customer_reviews.comments',                
            )
            ->leftJoin('job_types', 'job_types.lead_id', '=',  'leads.id')
            ->leftJoin('sales_staff', 'job_types.sales_staff_id', '=',  'sales_staff.id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->leftJoin('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->leftJoin('postcodes', 'postcodes.id', '=', 'sales_contacts.postcode_id')
            ->leftJoin('customer_reviews', 'customer_reviews.lead_id', '=', 'leads.id')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("concat(sales_contacts.last_name, ' ', postcodes.locality) as last_name_suburb")
            ->whereIn('leads.franchise_id', $franchiseIds);

        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){
            $mainQuery = $mainQuery->whereBetween('customer_reviews.date_project_completed', [$queryParams['start_date'], $queryParams['end_date']]);
        }
        
        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){
            $mainQuery = $mainQuery->where('leads.franchise_id',$queryParams['franchise_id'] );
        }

        if(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== ""){
            $mainQuery = $mainQuery->where('franchises.type', $queryParams['franchise_type'] );
        }

        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){
            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }

        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){
            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }
        
        $mainQuery->groupBy([
            'customer_reviews.date_project_completed',
            'customer_reviews.date_warranty_received',
            'customer_reviews.workmanship_rating',
            'customer_reviews.service_received_rating',
            'customer_reviews.finished_product_rating',
            'customer_reviews.design_consultant_rating',
            'customer_reviews.comments',
            'leads.lead_number',
            'leads.lead_date',
            'leads.franchise_id',
        ]);
        
        return $mainQuery->get();

    }
}
