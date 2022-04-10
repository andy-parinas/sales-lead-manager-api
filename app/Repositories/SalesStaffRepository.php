<?php


namespace App\Repositories;


use App\SalesStaff;
use Illuminate\Support\Facades\DB;

class SalesStaffRepository implements Interfaces\SalesStafRepositoryInterface
{

    public function getAll(array $params)
    {
        $query = DB::table('sales_staff')
            ->select('sales_staff.id', 'first_name', 'last_name', 'email', 'contact_number', 'status', 'franchises.franchise_number')
            ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' );

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');

        }


        if($params['column'] == 'franchises')
        {
            $query = $query->orderBy('franchises.franchise_number', $params['direction'])
                ->paginate($params['size']);

        }elseif($params['column'] == 'created_at')
        {
            $query = $query->orderBy('sales_staff.created_at', $params['direction'])
                ->paginate($params['size']);

        }else {
            $query = $query->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
        }


        return $query;

    }


    public function searchAll($search)
    {

        return DB::table('sales_staff')
            ->select('id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number'
            )
            ->where('status', 'active')
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->get();


    }

    public function getAllByFranchise(array $franchiseIds, array $params)
    {

        $query = DB::table('sales_staff')
            ->select('sales_staff.id', 'first_name', 'last_name', 'email', 'contact_number', 'status', 'franchises.franchise_number')
            ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' )
            ->whereIn('franchises.id', $franchiseIds);

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');

        }
        if($params['column'] == 'franchises')
        {
            $query = $query->orderBy('franchises.franchise_number', $params['direction'])
                ->paginate($params['size']);

        }elseif($params['column'] == 'created_at')
        {
            $query = $query->orderBy('sales_staff.created_at', $params['direction'])
                ->paginate($params['size']);

        }else {
            $query = $query->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
        }

        return $query;




    }

    public function searchAllByFranchise(array $franchiseIds, $search)
    {
        return DB::table('sales_staff')
            ->select('sales_staff.id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number'
            )
            ->join("franchise_sales_staff", "sales_staff.id", '=', "franchise_sales_staff.sales_staff_id")
            ->join("franchises", "franchises.id", '=', "franchise_sales_staff.franchise_id")
            ->where('status', 'active')
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->whereIn('franchises.id', $franchiseIds)
            ->get();
    }
}
