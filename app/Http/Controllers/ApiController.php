<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponser;


    protected function getRequestParams()
    {

        $column = request()->has('sort') ? request()->sort : 'created_at';
        $direction = request()->has('direction') ? request()->direction : 'asc';
        $size = request()->has('size') ? request()->size : 10;
        $page = request()->has('page') ? request()->page : 1;

        $params = [
            'column' => $column,
            'direction' => $direction,
            'size' =>$size,
            'page' => $page
        ];

        if(request()->has('search') && request()->has('on')){

            $params = array_merge($params, ['search' => request()->search]);
            $params = array_merge($params, ['on' => request()->on]);

        }elseif (request()->has('search')){
            $params = array_merge($params, ['search' => request()->search]);
        }elseif(request()->has('on')){
            $params = array_merge($params, ['on' => request()->on]);
        }

        if(request()->has('all')){
            $params = array_merge($params, ['all' => request()->all]);
        }

        return $params;

//        if(request()->has('search') && request()->has('on')){
//
//            return [
//                'column' => $column,
//                'direction' => $direction,
//                'size' => $size,
//                'search' => request()->search,
//                'on' => request()->on
//            ];
//        }elseif (request()->has('search')){
//
//            return [
//                'column' => $column,
//                'direction' => $direction,
//                'size' => $size,
//                'search' => request()->search,
//            ];
//        }
//
//        return [
//            'column' => $column,
//            'direction' => $direction,
//            'size' =>$size
//        ];
    }

    public function isAllowed($ability)
    {
        if(Gate::denies($ability)){
            // return $this->errorResponse("Not authorized to create user", Response::HTTP_FORBIDDEN);
            throw new AuthorizationException();
        }
    }

}
