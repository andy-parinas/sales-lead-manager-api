<?php


namespace App\Repositories;


use App\SalesStaff;
use Illuminate\Support\Facades\DB;

class SalesStaffRepository implements Interfaces\SalesStafRepositoryInterface
{

    public function getAll(array $params)
    {
        $query = DB::table('sales_staff')
            ->select(
                'sales_staff.id', 
                'first_name', 
                'last_name', 
                'email', 
                'contact_number', 
                'status')
            ->selectRaw("JSON_ARRAYAGG(franchises.id) as franchise_ids")
            ->selectRaw("GROUP_CONCAT(franchises.franchise_number) as franchise_number")
            ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' );

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
        }

        if($params['column'] == 'franchises')
        {
            $query = $query->orderBy('franchises.franchise_number', $params['direction']);
            //     ->paginate($params['size']);

        }elseif($params['column'] == 'created_at')
        {
            $query = $query->orderBy('sales_staff.created_at', $params['direction']);
            //     ->paginate($params['size']);

        }else {
            $query = $query->orderBy($params['column'], $params['direction']);
            // ->paginate($params['size']);
        }


        $size= 10;
        $offset= 0;

        if(key_exists('size', $params) && key_exists('page', $params))
        {
            $offset = ((int)$params['page'] - 1) * (int)$params['size'];
            $size = $params['size'];          
        }

        $query->limit($size)
        ->offset($offset);

        $query->groupBy([
            'sales_staff.id', 
            'first_name', 
            'sales_staff.last_name', 
            'sales_staff.email', 
            'sales_staff.contact_number', 
            'sales_staff.status',
            'sales_staff.created_at'
        ]);        

        $items = $query->get();
        $count = SalesStaff::count();

        // return $query->get();
        return [
            'data' => $items,
            'count' => $count
        ];

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
        )->where(function ($query) use ($search){
        $query->where('first_name','LIKE', '%' . $search . '%' )
                ->orWhere('last_name','LIKE', '%' . $search . '%' )
                ->orWhere('email', 'LIKE', '%' . $search . '%');
        })->get();
    }
    
    public function getAllByFranchise(array $franchiseIds, array $params)
    {

        // $query = DB::table('sales_staff')
        //     ->select('sales_staff.id', 'first_name', 'last_name', 'email', 'contact_number', 'status', 'franchises.franchise_number')
        //     ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
        //     ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' )
        //     ->whereIn('franchises.id', $franchiseIds);

        $query = DB::table('sales_staff')
            ->select(
                'sales_staff.id', 
                'first_name', 
                'last_name', 
                'email', 
                'contact_number', 
                'status')
            ->selectRaw("JSON_ARRAYAGG(franchises.id) as franchise_ids")
            ->selectRaw("GROUP_CONCAT(franchises.franchise_number) as franchise_number")
            ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' )
            ->whereIn('franchises.id', $franchiseIds);


        if(key_exists('search', $params) && key_exists('on', $params))
        {

            $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');

        }
        if($params['column'] == 'franchises')
        {
            $query = $query->orderBy('franchises.franchise_number', $params['direction']);
                // ->paginate($params['size']);

        }elseif($params['column'] == 'created_at')
        {
            $query = $query->orderBy('sales_staff.created_at', $params['direction']);
                // ->paginate($params['size']);

        }else {
            $query = $query->orderBy($params['column'], $params['direction']);
            // ->paginate($params['size']);
        }

        $size= 10;
        $offset= 0;

        if(key_exists('size', $params) && key_exists('page', $params))
        {

            $offset = ((int)$params['page'] - 1) * (int)$params['size'];
            $size = $params['size'];

          
        }

        $query->limit($size)
        ->offset($offset);

        $query->groupBy([
            'sales_staff.id', 
            'first_name', 
            'sales_staff.last_name', 
            'sales_staff.email', 
            'sales_staff.contact_number', 
            'sales_staff.status',
            'sales_staff.created_at'
        ]);

        $items = $query->get();
        $count = SalesStaff::leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
        ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' )
        ->whereIn('franchises.id', $franchiseIds)
        ->count();

        // return $query;

        return [
            'data' => $items,
            'count' => $count
        ];
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
            //->where('status', 'active')
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->whereIn('franchises.id', $franchiseIds)
            ->get();
    }

    public function getAllSalesStaff()
    {
        return DB::table('sales_staff')
        ->select(
            'id',
            'first_name',
            'last_name',
            'email',
            'status',
            'contact_number'
        )
        ->get();
    }

    public function getAllSalesStaffByFranchise(array $franchiseIds)
    {
        return DB::table('sales_staff')
            ->select(
                'sales_staff.id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number'
            )
            ->join("franchise_sales_staff", "sales_staff.id", '=', "franchise_sales_staff.sales_staff_id")
            ->join("franchises", "franchises.id", '=', "franchise_sales_staff.franchise_id")
            ->whereIn('franchises.id', $franchiseIds)
            ->get();
    }
}
