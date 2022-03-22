<?php


namespace App\Reports;


use App\SalesStaff;
use Illuminate\Support\Facades\DB;

class SalesStaffProductReportImp implements Interfaces\SalesStaffProductReport
{

    public function generate($queryParams)
    {
        $mainQuery= DB::table('leads')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("products.name as productName")
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("avg(contracts.total_contract) as averageSalesPrice")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfSales")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) / count(leads.id) * 100 as conversionRate")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->join("job_types", "job_types.lead_id", "=", "leads.id")
            ->join("products", "job_types.product_id", "=", "products.id")
            ->join("appointments", "appointments.lead_id", "=", "leads.id")
            ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
            ->join("franchises", "leads.franchise_id", "=", "franchises.id")
            ->rightJoin("contracts", "contracts.lead_id", "=", "leads.id");

        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('contracts.contract_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('franchises.id',$queryParams['franchise_id'] );
        }

        if(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== ""){

            $mainQuery = $mainQuery->where('franchises.type', $queryParams['franchise_type'] );
        }


        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }

        if(key_exists("status", $queryParams) && $queryParams['status'] !== ""){

            if($queryParams['status'] == 'active'){

                $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
            }elseif ($queryParams['status'] == 'blocked') {

                $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::BLOCKED);
            }

        }else {

            $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
        }



        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

            $mainQuery = $mainQuery->where('products.id',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'products.name'
        ]);

        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){

            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }else {
            $mainQuery = $mainQuery->orderBy('sales_staff.first_name', 'asc');
        }


        return $mainQuery->get();

    }

    public function generateByFranchise($franchiseIds, $queryParams)
    {
        $mainQuery= DB::table('leads')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("products.name as productName")
            ->selectRaw("GROUP_CONCAT(DISTINCT franchises.franchise_number)  as franchiseNumber")
            ->selectRaw("avg(contracts.contract_price) as averageSalesPrice")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) as numberOfSales")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null)) / count(leads.id) * 100 as conversionRate")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->join("job_types", "job_types.lead_id", "=", "leads.id")
            ->join("products", "job_types.product_id", "=", "products.id")
            ->join("appointments", "appointments.lead_id", "=", "leads.id")
            ->join("sales_staff", "job_types.sales_staff_id", '=', "sales_staff.id")
            ->join("franchises", "leads.franchise_id", "=", "franchises.id")
            ->rightJoin("contracts", "contracts.lead_id", "=", "leads.id")
            ->whereIn('franchises.id', $franchiseIds);

        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('contracts.contract_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('franchises.id',$queryParams['franchise_id'] );
        }

        if(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== ""){

            $mainQuery = $mainQuery->where('franchises.type', $queryParams['franchise_type'] );
        }


        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }

        if(key_exists("status", $queryParams) && $queryParams['status'] !== ""){

            if($queryParams['status'] == 'active'){

                $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
            }elseif ($queryParams['status'] == 'blocked') {

                $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::BLOCKED);
            }

        }else {

            $mainQuery = $mainQuery->where('sales_staff.status', SalesStaff::ACTIVE);
        }



        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

            $mainQuery = $mainQuery->where('products.id',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'products.name'
        ]);

        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){

            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }else {
            $mainQuery = $mainQuery->orderBy('sales_staff.first_name', 'asc');
        }


        return $mainQuery->get();
    }
}
