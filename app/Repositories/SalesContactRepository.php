<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\SalesContact;

class SalesContactRepository  implements SalesContactRepositoryInterface
{
    public function sortAndPaginate(Array $params)
    {
        $query =  DB::table('sales_contacts')
                    ->join('postcodes','postcodes.id', '=', 'sales_contacts.postcode_id')
                    ->select(
                        "sales_contacts.id",
                        'title',
                        'first_name',
                        'last_name',
                        'contact_number',
                        'customer_type',
                        'street1',
                        'street2',
                        'status',
                        'email',
                        'email2',
                        'postcodes.id as postcodeId',
                        'postcodes.locality as suburb',
                        'postcodes.state',
                        'postcodes.pcode as postcode'
                        );
        

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            if($params['on'] == 'postcode'){
                $params['on'] = 'pcode';
            }

            if($params['on'] == 'suburb'){
                $params['on'] = 'locality';
            }


            $query = $query
                    ->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
//                    ->orderBy('sales_contacts.' . $params['column'], $params['direction'])
//                    ->paginate($params['size']);
        }

        if(key_exists('column', $params) && ($params['column'] == 'pcode'
                || $params['column'] == 'postcode'
                || $params['column'] == 'state'
                || $params['column'] == 'locality'
                || $params['column'] == 'suburb')){

            if($params['column'] == 'postcode'){
                $params['column'] = 'pcode';
            }

            if($params['column'] == 'suburb'){
                $params['column'] = 'locality';
            }

            $query = $query->orderBy('postcodes.' . $params['column'], $params['direction']);

        }else {
            $query = $query->orderBy('sales_contacts.' . $params['column'], $params['direction']);
        }

//        return $query->orderBy('sales_contacts.' . $params['column'], $params['direction'])
//                    ->paginate($params['size']);

        return $query->paginate($params['size']);

    }
    
    public function sortAndPaginateByFranchise(array $postcodeIds, array $params)
    {
        $query =  DB::table('sales_contacts')
            ->select(
                "sales_contacts.id",
                'title',
                'first_name',
                'last_name',
                'contact_number',
                'customer_type',
                'street1',
                'street2',
                'status',
                'email',
                'email2',
                'postcodes.id as postcodeId',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode'
            )->join('postcodes','postcodes.id', '=', 'sales_contacts.postcode_id')
            ->whereIn('postcodes.id', $postcodeIds);


        if(key_exists('search', $params) && key_exists('on', $params))
        {
            if($params['on'] == 'postcode'){
                $params['on'] = 'pcode';
            }

            if($params['on'] == 'suburb'){
                $params['on'] = 'locality';
            }
            
            $query = $query
                ->where($params['on'], 'LIKE', '%' . $params['search'] . '%');

        }

        if(key_exists('column', $params) && ($params['column'] == 'pcode'
                || $params['column'] == 'postcode'
                || $params['column'] == 'state'
                || $params['column'] == 'locality'
                || $params['column'] == 'suburb')){

            if($params['column'] == 'postcode'){
                $params['column'] = 'pcode';
            }

            if($params['column'] == 'suburb'){
                $params['column'] = 'locality';
            }

            $query = $query->orderBy('postcodes.' . $params['column'], $params['direction']);

        }else {
            $query = $query->orderBy('sales_contacts.' . $params['column'], $params['direction']);
        }

        return $query->paginate($params['size']);
    }

    public function simpleSearch(Array $params)
    {
        $query =  DB::table('sales_contacts')
            ->select("sales_contacts.id",
                'title',
                'first_name',
                'last_name',
                'contact_number',
                'customer_type',
                'street1',
                'street2',
                'status',
                'email',
                'email2',
                'postcodes.id as postcodeId',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode'
            )->join('postcodes','postcodes.id', '=', 'sales_contacts.postcode_id');
            
        if(key_exists('search', $params))
        {
            $search = $params['search'];
            //make accpetable this character ' in search
            $search = str_replace("'", "\'", $search);
            $search = trim($search);
            //dd($search);
            $query = $query->whereRaw("concat(first_name, ' ', last_name) like '%" .$search. "%' OR CONCAT(last_name, ' ', first_name) like '%" .$search. "%'");
            
            $query = $query->where('status', 'active');

            if(key_exists('column', $params) && ($params['column'] == 'pcode' || $params['column'] == 'state' || $params['column'] == 'locality' ) ){

                $query = $query->orderBy('postcodes.' . $params['column'], $params['direction']);

            }else {
                $query = $query->orderBy('sales_contacts.' . $params['column'], $params['direction']);
            }

            return $query->paginate($params['size']);
        }

        return $query->paginate($params['size']);
    }

    public function simpleSearchByFranchise(Array $params, $postcodeIds = null)
    {
        $query =  DB::table('sales_contacts')
            ->select("sales_contacts.id",
                'title',
                'first_name',
                'last_name',
                'contact_number',
                'customer_type',
                'street1',
                'street2',
                'status',
                'email',
                'email2',
                'postcodes.id as postcodeId',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode'
            )->join('postcodes','postcodes.id', '=', 'sales_contacts.postcode_id');

        if($postcodeIds != null){            
            $query = $query->whereIn('postcodes.id', $postcodeIds);            
        }
        
        if(key_exists('search', $params))
        {
            $search = $params['search'];
            $search = trim($search);
            
            $query = $query->where('status', 'active');

            if(key_exists('column', $params) && ($params['column'] == 'pcode' || $params['column'] == 'state' || $params['column'] == 'locality' ) ){
                $query = $query->orderBy('postcodes.' . $params['column'], $params['direction']);
            }else {
                $query = $query->orderBy('sales_contacts.' . $params['column'], $params['direction']);
            }

            $filterSearch = [];
            $filterSearch = $query->get()->filter(function ($item) use ($search) {
                $firstnameLastname = $item->first_name . ' ' . $item->last_name;
                $lastNameFirstname = $item->last_name . ' ' . $item->first_name;
                return stripos(strtolower($firstnameLastname), strtolower($search)) !== false || stripos(strtolower($lastNameFirstname), strtolower($search)) !== false;
            })->toArray();
            
            $query = collect($filterSearch);
            $query = new \Illuminate\Pagination\LengthAwarePaginator(
                $query->forPage($params['page'], $params['size']),
                $query->count(),
                $params['size'],
                $params['page'],
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
            return $query;
        }

        return $query->paginate($params['size']);
    }
}
